<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Check\Analyzer;

/**
 * Finds fonts a page loads from other servers on the first visit: font
 * links in the HTML, @import and @font-face in inline styles and in every
 * linked stylesheet. Independent of Lighthouse, so it also works when
 * PageSpeed is not available. Fetching is left to the caller.
 */
final class FontScanner
{
    /** Services whose stylesheet or script itself means fonts from their servers. */
    private const SERVICES = [
        'fonts.googleapis.com' => 'Google Fonts', 'fonts.gstatic.com' => 'Google Fonts',
        'typekit.net' => 'Adobe Fonts', 'typekit.com' => 'Adobe Fonts',
        'fonts.bunny.net' => 'Bunny Fonts',
        'fontawesome.com' => 'Font Awesome',
        'fonts.net' => 'Fonts.com',
        'typography.com' => 'Typography.com',
        'fontshare.com' => 'Fontshare',
        'cdnfonts.com' => 'CDNFonts',
    ];

    /**
     * @return array{stylesheets: list<string>, fonts: list<string>} stylesheets to fetch, font URLs found directly
     */
    public function fromHtml(string $html, string $pageUrl): array
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
        libxml_clear_errors();
        $xp = new \DOMXPath($doc);

        // Relative addresses resolve against <base href> like in the browser (Contao always sets one).
        $base = $xp->query('//base[@href]')->item(0);

        if ($base instanceof \DOMElement && '' !== $resolved = self::absolute(trim($base->getAttribute('href')), $pageUrl)) {
            $pageUrl = $resolved;
        }

        $sheets = [];
        $fonts = [];

        foreach ($xp->query('//link[@href]') ?: [] as $link) {
            if (!$link instanceof \DOMElement) {
                continue;
            }

            $rel = strtolower(' '.$link->getAttribute('rel').' ');
            $url = self::absolute(trim($link->getAttribute('href')), $pageUrl);

            if ('' === $url) {
                continue;
            }

            if (str_contains($rel, ' stylesheet ') || (str_contains($rel, ' preload ') && 'style' === strtolower($link->getAttribute('as')))) {
                // A font service's stylesheet is a font reference by itself, no need to fetch it.
                if (null !== self::service($url)) {
                    $fonts[] = $url;
                } else {
                    $sheets[] = $url;
                }
            } elseif (str_contains($rel, ' preload ') && 'font' === strtolower($link->getAttribute('as'))) {
                $fonts[] = $url;
            }
        }

        foreach ($xp->query('//script[@src]') ?: [] as $script) {
            $url = $script instanceof \DOMElement ? self::absolute(trim($script->getAttribute('src')), $pageUrl) : '';

            // Font kits and loaders: Adobe Fonts, Font Awesome kits, Google's Web Font Loader.
            if ('' !== $url && (null !== self::service($url) || preg_match('~ajax\.googleapis\.com/ajax/libs/webfont~i', $url))) {
                $fonts[] = $url;
            }
        }

        foreach ($xp->query('//style') ?: [] as $style) {
            $scan = $this->scanCss($style->textContent, $pageUrl);
            $fonts = array_merge($fonts, $scan['fonts']);
            $sheets = array_merge($sheets, $scan['imports']);
        }

        return ['stylesheets' => array_values(array_unique($sheets)), 'fonts' => array_values(array_unique($fonts))];
    }

    /**
     * @return array{imports: list<string>, fonts: list<string>} stylesheets to fetch next, font URLs
     */
    public function scanCss(string $css, string $cssUrl): array
    {
        $css = preg_replace('~/\*.*?\*/~s', '', $css) ?? $css;
        $imports = [];
        $fonts = [];

        if (preg_match_all('~@import\s+(?:url\(\s*)?[\'"]?([^\'")\s;]+)~i', $css, $m)) {
            foreach ($m[1] as $href) {
                $url = self::absolute($href, $cssUrl);

                if ('' === $url) {
                    continue;
                }

                if (null !== self::service($url)) {
                    $fonts[] = $url;
                } else {
                    $imports[] = $url;
                }
            }
        }

        if (preg_match_all('~@font-face\s*\{([^}]*)\}~i', $css, $faces)) {
            foreach ($faces[1] as $face) {
                if (!preg_match_all('~url\(\s*[\'"]?([^\'")]+)~i', $face, $u)) {
                    continue;
                }

                foreach ($u[1] as $href) {
                    $url = self::absolute(trim($href), $cssUrl);

                    if ('' !== $url) {
                        $fonts[] = $url;
                    }
                }
            }
        }

        return ['imports' => array_values(array_unique($imports)), 'fonts' => array_values(array_unique($fonts))];
    }

    /**
     * Hosts of the given URLs that belong to another site than the page.
     *
     * @param list<string> $urls
     *
     * @return list<string>
     */
    public static function foreignHosts(array $urls, string $pageUrl): array
    {
        $page = self::bare((string) parse_url($pageUrl, PHP_URL_HOST));
        $hosts = [];

        foreach ($urls as $url) {
            $host = self::bare((string) parse_url($url, PHP_URL_HOST));

            if ('' !== $host && $host !== $page && !str_ends_with($host, '.'.$page) && !str_ends_with($page, '.'.$host)) {
                $hosts[$host] = true;
            }
        }

        return array_keys($hosts);
    }

    /** Name of the font service a host belongs to, or the host itself. */
    public static function label(string $host): string
    {
        return self::service('https://'.$host.'/') ?? $host;
    }

    private static function service(string $url): string|null
    {
        $host = self::bare((string) parse_url($url, PHP_URL_HOST));

        foreach (self::SERVICES as $domain => $label) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                return $label;
            }
        }

        return null;
    }

    private static function bare(string $host): string
    {
        return preg_replace('/^www\./', '', strtolower($host)) ?? '';
    }

    /** Absolute http(s) URL, or '' for data: URIs and other schemes. */
    private static function absolute(string $href, string $base): string
    {
        if ('' === $href || preg_match('~^(data|blob|javascript|about):~i', $href)) {
            return '';
        }

        if (preg_match('~^https?://~i', $href)) {
            return $href;
        }

        $p = parse_url($base);
        $scheme = $p['scheme'] ?? 'https';

        if (str_starts_with($href, '//')) {
            return $scheme.':'.$href;
        }

        if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $href)) {
            return '';
        }

        $root = $scheme.'://'.($p['host'] ?? '').(isset($p['port']) ? ':'.$p['port'] : '');

        $path = str_starts_with($href, '/') ? $href : (preg_replace('~/[^/]*$~', '/', $p['path'] ?? '/') ?? '/').$href;

        return $root.self::withoutDotSegments($path);
    }

    /** "/assets/css/../fonts/x.woff2" → "/assets/fonts/x.woff2" (RFC 3986, 5.2.4), query and fragment untouched. */
    private static function withoutDotSegments(string $path): string
    {
        $cut = strcspn($path, '?#');
        $suffix = substr($path, $cut);
        $path = substr($path, 0, $cut);
        $out = [];

        foreach (explode('/', $path) as $segment) {
            if ('..' === $segment) {
                // never above the root: the first entry is the empty segment before the leading slash
                if (\count($out) > 1) {
                    array_pop($out);
                }
            } elseif ('.' !== $segment) {
                $out[] = $segment;
            }
        }

        return implode('/', $out).(\in_array(basename($path), ['.', '..'], true) ? '/' : '').$suffix;
    }
}
