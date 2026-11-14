<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Check\Analyzer;

/**
 * Which PHP version the web server runs, as far as it can be seen from
 * outside: the version the server announces in its response headers
 * (X-Powered-By, Server — on any page, including error pages) and the
 * version a publicly reachable phpinfo() page shows. Nothing is attacked or
 * tried out: the check reads ordinary responses and asks for a few common
 * file names of forgotten test files.
 *
 * Signs that the site runs on PHP at all (session cookie, CMS traces) tell
 * "the version is hidden" apart from "no PHP seen". A hidden version is not
 * a finding: hiding it (expose_php = Off) is recommended.
 */
final class ServerAnalyzer
{
    /** Common names of a forgotten phpinfo() test file, asked for once each. */
    public const PROBES = ['/phpinfo.php', '/info.php', '/php_info.php', '/phpinfo', '/test.php', '/i.php', '/php.php', '/pi.php'];

    /** Generator names of PHP systems (meta generator, start of the value). */
    private const PHP_GENERATORS = ['WordPress', 'Joomla', 'Drupal', 'TYPO3', 'Contao', 'Shopware', 'PrestaShop', 'Magento', 'Concrete', 'Craft CMS', 'ProcessWire', 'Kirby', 'Grav', 'Neos', 'MODX', 'OpenCart', 'WooCommerce', 'Elementor'];

    /**
     * @param array<string, array{status: int, url: string, body: string, headers: array<string, list<string>>}> $responses every response of the check so far, by URL (start page, probes, robots.txt …)
     * @param list<string>                                                                                          $probeUrls the URLs of the phpinfo probes among them
     *
     * @return array{php: bool, hints: list<string>, raw: string, source: string, phpinfo: string, uploadProgress: bool|null}
     */
    public function analyze(array $responses, array $probeUrls, string $homeHtml): array
    {
        $raw = '';
        $source = '';
        $hints = [];

        foreach ($responses as $response) {
            $headers = (array) ($response['headers'] ?? []);

            foreach (['x-powered-by', 'server'] as $name) {
                foreach ((array) ($headers[$name] ?? []) as $value) {
                    if ('' === $raw && preg_match('~\bPHP/(\d+\.\d+[^\s,;()]*)~i', (string) $value, $m)) {
                        $raw = $m[1];
                        $source = 'header';
                    }

                    if (preg_match('~\bPHP\b~i', (string) $value)) {
                        $hints['header'] = true;
                    }
                }
            }

            foreach ((array) ($headers['set-cookie'] ?? []) as $cookie) {
                if (preg_match('/^\s*PHPSESSID=/i', (string) $cookie)) {
                    $hints['cookie'] = true;
                }
            }
        }

        $phpinfo = '';
        $uploadProgress = null;

        foreach ($probeUrls as $url) {
            $r = $responses[$url] ?? null;

            if (null === $r || 200 !== $r['status'] || !str_contains($r['body'], 'phpinfo()')) {
                continue;
            }

            if (!preg_match('~<h1 class="p">PHP Version ([^<\s]+)</h1>~', $r['body'], $m) && !preg_match('~<title>PHP ([0-9][^<\s]*) - phpinfo\(\)</title>~', $r['body'], $m)) {
                continue;
            }

            $phpinfo = (string) (parse_url($url, PHP_URL_PATH) ?: $url);
            $hints['phpinfo'] = true;

            if ('header' !== $source) {
                $raw = html_entity_decode($m[1]);
                $source = 'phpinfo';
            }

            // local value of the mitigation the PHP advisory names for CVE-2026-109619
            if (preg_match('~session\.upload_progress\.enabled</td><td class="v">([^<]*)</td>~i', $r['body'], $u)) {
                $uploadProgress = \in_array(strtolower(trim($u[1])), ['on', '1'], true);
            }

            break;
        }

        foreach ($this->htmlHints($homeHtml) as $hint) {
            $hints[$hint] = true;
        }

        return [
            'php' => '' !== $raw || [] !== $hints,
            'hints' => array_keys($hints),
            'raw' => mb_substr($raw, 0, 80),
            'source' => $source,
            'phpinfo' => $phpinfo,
            'uploadProgress' => $uploadProgress,
        ];
    }

    /** @return list<string> */
    private function htmlHints(string $html): array
    {
        if ('' === $html) {
            return [];
        }

        $hints = [];

        if (preg_match('~<meta[^>]+name=["\']generator["\'][^>]*content=["\']([^"\']+)~i', $html, $m) || preg_match('~<meta[^>]+content=["\']([^"\']+)["\'][^>]*name=["\']generator["\']~i', $html, $m)) {
            foreach (self::PHP_GENERATORS as $name) {
                if (0 === stripos(trim($m[1]), $name)) {
                    $hints[] = 'cms';
                    break;
                }
            }
        }

        if (preg_match('~/wp-(?:content|includes)/|/bundles/contao|/typo3(?:conf|temp)/|/media/jui/|/sites/default/files/~i', $html)) {
            $hints[] = 'cms';
        }

        if (preg_match('~href=["\'][^"\'#?]+\.php(?:[?#"\'])~i', $html)) {
            $hints[] = 'links';
        }

        return array_values(array_unique($hints));
    }
}
