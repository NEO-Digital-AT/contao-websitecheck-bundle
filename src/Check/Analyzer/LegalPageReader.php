<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Check\Analyzer;

/**
 * Reads the content of a linked legal page (imprint, privacy policy, terms):
 * how much text the page has of its own — without header, menu, footer and
 * whatever it shares with the start page — and whether it contains a
 * privacy policy. Many small sites put the privacy policy into the imprint;
 * that page then counts as having one.
 */
final class LegalPageReader
{
    /** Below this many words of its own a page counts as empty (a heading and "coming soon" stay below). */
    private const EMPTY_WORDS = 15;

    /** Headings that open a privacy policy, in the report languages. */
    private const PRIVACY_HEADINGS = [
        'datenschutz', 'privacy', 'dsgvo', 'gdpr', 'rgpd', 'données personnelles', 'donnees personnelles', 'protection des données',
        'confidentialité', 'privacyverklaring', 'privacybeleid', 'gegevensbescherming', 'informativa', 'protezione dei dati',
        'privacidad', 'protección de datos', 'ochrona danych', 'prywatności', 'rodo', 'ochrana osobních údajů', 'osobních údajů',
        'adatvédelem', 'adatkezelés', 'varstvo osebnih podatkov', 'zasebnost', 'zaštita osobnih podataka', 'zaštita podataka', 'privatnost',
    ];

    /** Terms a privacy policy is made of; several different ones must appear. */
    private const PRIVACY_TERMS = [
        // de
        'personenbezogen', 'dsgvo', 'verantwortliche', 'betroffen', 'auskunft', 'löschung', 'widerspruch', 'aufsichtsbehörde',
        'datenschutzbehörde', 'rechtsgrundlage', 'speicherdauer', 'art. 6', 'einwilligung', 'verarbeitung', 'cookies',
        // en
        'personal data', 'gdpr', 'data controller', 'data subject', 'right of access', 'erasure', 'supervisory authority', 'legal basis', 'retention', 'processing',
        // fr
        'données personnelles', 'données à caractère personnel', 'rgpd', 'responsable du traitement', "droit d'accès", 'effacement', 'cnil', 'base légale',
        // nl
        'persoonsgegevens', 'avg', 'verwerkingsverantwoordelijke', 'autoriteit persoonsgegevens', 'bewaartermijn',
        // it
        'dati personali', 'titolare del trattamento', 'garante', 'trattamento dei dati',
        // es
        'datos personales', 'responsable del tratamiento', 'derecho de acceso', 'supresión', 'tratamiento de datos',
        // pl
        'dane osobowe', 'danych osobowych', 'rodo', 'administrator danych', 'przetwarzanie',
        // cs
        'osobní údaje', 'osobních údajů', 'správce osobních', 'zpracování',
        // hu
        'személyes adat', 'adatkezelő', 'adatkezelés',
        // sl
        'osebni podatki', 'osebnih podatkov', 'upravljavec', 'obdelava',
        // hr
        'osobni podaci', 'osobnih podataka', 'voditelj obrade', 'obrada',
    ];

    /** Hosts that deliver ready-made legal texts by script (generators, legal services, cookie declarations). */
    private const LEGAL_TEXT_PROVIDERS = [
        'iubenda.com', 'termly.io', 'e-recht24.de', 'erecht24.de', 'datenschutz-generator.de', 'it-recht-kanzlei.de',
        'haendlerbund.de', 'protectedshops.de', 'trustedshops.', 'activemind.', 'privacypolicies.com', 'termsfeed.com', 'osano.com',
    ];

    /** Hosts whose iframes show media, maps or social posts, never a legal text. */
    private const MEDIA_HOSTS = [
        'youtube.com', 'youtube-nocookie.com', 'youtu.be', 'vimeo.com', 'google.', 'googleapis.com', 'openstreetmap.org',
        'spotify.com', 'soundcloud.com', 'facebook.com', 'instagram.com', 'twitter.com', 'x.com', 'linkedin.com', 'tiktok.com', 'calendly.com',
    ];

    /**
     * Ids and class names of page furniture that is not the page's own content. Exact names only:
     * a suffix match would also hit content wrappers such as "has-sidebar" or component prefixes.
     */
    private const FURNITURE = [
        'header', 'footer', 'nav', 'navbar', 'navigation', 'menu', 'sidebar', 'left', 'right', 'breadcrumb', 'breadcrumbs',
        'site-header', 'site-footer', 'page-footer', 'main-nav', 'main-navigation', 'skip-link', 'skiplinks',
        // Contao front-end modules
        'mod_navigation', 'mod_customnav', 'mod_breadcrumb', 'mod_quicknav', 'mod_quicklink', 'mod_search', 'mod_changelanguage',
    ];

    /**
     * @param string $html     the legal page
     * @param string $homeHtml the start page; text blocks it shares with the legal page are not counted (only for pages without a <main>)
     *
     * @return array{words: int, empty: bool, privacy: bool, embedded: bool}
     */
    public function read(string $html, string $homeHtml = ''): array
    {
        [$blocks, $hasMain, $embedded] = $this->blocks($html);

        if (!$hasMain && '' !== $homeHtml) {
            $shared = array_flip($this->blocks($homeHtml)[0]);
            $blocks = array_values(array_filter($blocks, static fn (string $b): bool => !isset($shared[$b])));
        }

        $text = implode("\n", $blocks);
        $words = \count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: []);

        return [
            'words' => $words,
            // Legal texts embedded from a provider (iframe, script) are loaded in the browser: never judged empty.
            'empty' => $words < self::EMPTY_WORDS && !$embedded,
            'privacy' => $this->hasPrivacy($blocks),
            'embedded' => $embedded,
        ];
    }

    /** A privacy heading plus at least four different privacy terms, or eight terms without a heading. */
    private function hasPrivacy(array $blocks): bool
    {
        $heading = false;

        foreach ($blocks as $block) {
            if (str_starts_with($block, '#') && $this->containsAny(mb_substr($block, 1), self::PRIVACY_HEADINGS)) {
                $heading = true;
                break;
            }
        }

        $text = mb_strtolower(implode("\n", $blocks));
        $terms = 0;

        foreach (array_unique(self::PRIVACY_TERMS) as $term) {
            if (str_contains($text, $term)) {
                ++$terms;
            }
        }

        return $terms >= ($heading ? 4 : 8);
    }

    /** Scripts only from known legal-text providers; iframes from any other site except media, maps and social posts. */
    private function embedsLegalText(\DOMElement $node): bool
    {
        $src = trim($node->getAttribute('src'));
        $host = strtolower((string) parse_url(str_starts_with($src, '//') ? 'https:'.$src : $src, PHP_URL_HOST));

        // relative address: the site's own script or page
        if ('' === $host) {
            return false;
        }

        $matches = static fn (array $list): bool => [] !== array_filter($list, static fn (string $domain): bool => str_contains($host, $domain));

        if ('script' === $node->nodeName) {
            return $matches(self::LEGAL_TEXT_PROVIDERS)
                || (str_contains($host, 'cookiebot.com') && ('CookieDeclaration' === $node->getAttribute('id') || str_contains($src, '/cd.js')));
        }

        return !$matches(self::MEDIA_HOSTS);
    }

    private function isFurniture(\DOMElement $node): bool
    {
        $names = preg_split('/\s+/', strtolower($node->getAttribute('id').' '.$node->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($names as $name) {
            // Cookie banners and consent layers come with many names (cookie-banner, consent-manager …).
            if (\in_array($name, self::FURNITURE, true) || str_starts_with($name, 'cookie') || str_starts_with($name, 'consent')) {
                return true;
            }
        }

        return false;
    }

    private function containsAny(string $text, array $words): bool
    {
        $text = mb_strtolower($text);

        foreach ($words as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The page's own text as normalised blocks (headings prefixed with "#").
     *
     * @return array{0: list<string>, 1: bool, 2: bool} blocks, whether a main element was found, whether content is embedded
     */
    private function blocks(string $html): array
    {
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
        libxml_clear_errors();
        $xp = new \DOMXPath($doc);

        $root = $xp->query('//main | //*[@role="main"] | //*[@id="main"]')->item(0);
        $hasMain = $root instanceof \DOMElement;
        $root ??= $xp->query('//body')->item(0);

        if (!$root instanceof \DOMElement) {
            return [[], false, false];
        }

        $remove = [];

        foreach ($xp->query('.//script | .//style | .//noscript | .//template | .//svg | .//header | .//nav | .//footer | .//aside | .//form | .//*[@role="banner" or @role="navigation" or @role="contentinfo" or @role="complementary"] | .//*[@aria-hidden="true"]', $root) ?: [] as $node) {
            $remove[] = $node;
        }

        foreach ($xp->query('.//*[@id or @class]', $root) ?: [] as $node) {
            if ($node instanceof \DOMElement && $this->isFurniture($node)) {
                $remove[] = $node;
            }
        }

        // Embedded legal texts: a provider's script or an iframe inside the content. Tag managers,
        // analytics and video or map frames are no legal text and leave an empty page empty.
        $embedded = false;

        foreach ($xp->query('.//iframe[@src] | .//script[@src]', $root) ?: [] as $node) {
            if ($node instanceof \DOMElement && $this->embedsLegalText($node)) {
                $embedded = true;
                break;
            }
        }

        foreach ($remove as $node) {
            $node->parentNode?->removeChild($node);
        }

        $blocks = [];

        foreach ($xp->query('.//h1 | .//h2 | .//h3 | .//h4 | .//h5 | .//h6 | .//p | .//li | .//td | .//th | .//dt | .//dd | .//address | .//blockquote | .//pre | .//figcaption', $root) ?: [] as $node) {
            // Only the innermost block: a <li> with a <p> inside is counted once.
            if ($xp->query('.//p | .//li | .//td | .//dd', $node)->length > 0 && !\in_array($node->nodeName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
                continue;
            }

            $text = trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? '');

            if ('' !== $text) {
                $blocks[] = (str_starts_with($node->nodeName, 'h') && 2 === \strlen($node->nodeName) ? '#' : '').$text;
            }
        }

        // Text directly in container elements (sites without paragraphs).
        foreach ($xp->query('.//text()[normalize-space()]', $root) ?: [] as $textNode) {
            if (null === $xp->query('ancestor::h1 | ancestor::h2 | ancestor::h3 | ancestor::h4 | ancestor::h5 | ancestor::h6 | ancestor::p | ancestor::li | ancestor::td | ancestor::th | ancestor::dt | ancestor::dd | ancestor::address | ancestor::blockquote | ancestor::pre | ancestor::figcaption', $textNode)->item(0)) {
                $blocks[] = trim(preg_replace('/\s+/u', ' ', $textNode->textContent) ?? '');
            }
        }

        return [array_values(array_unique(array_filter($blocks, static fn (string $b): bool => '' !== $b && '#' !== $b))), $hasMain, $embedded];
    }
}
