<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Check\Analyzer;

/**
 * Legal basics as far as they can be checked technically: imprint and privacy
 * policy linked, reachable and not empty (a privacy policy inside the imprint
 * counts, rated lower than a page of its own), linked terms not empty, a
 * consent tool present, third-party services and fonts from other servers
 * loaded on the first visit (before any consent). Wording in the report stays
 * careful — this is a technical check, not legal advice.
 */
final class LegalAnalyzer
{
    /** Hosts that imply tracking/marketing or personal data transfer when loaded without consent. */
    private const TRACKERS = [
        'google-analytics.com' => 'Google Analytics',
        'analytics.google.com' => 'Google Analytics',
        'googletagmanager.com' => 'Google Tag Manager',
        'doubleclick.net' => 'Google Ads',
        'googleadservices.com' => 'Google Ads',
        'googlesyndication.com' => 'Google Ads',
        'connect.facebook.net' => 'Meta Pixel',
        'facebook.com' => 'Facebook',
        'facebook.net' => 'Meta Pixel',
        'hotjar.com' => 'Hotjar',
        'clarity.ms' => 'Microsoft Clarity',
        'tiktok.com' => 'TikTok Pixel',
        'linkedin.com' => 'LinkedIn Insight',
        'licdn.com' => 'LinkedIn Insight',
        'youtube.com' => 'YouTube',
        'youtube-nocookie.com' => 'YouTube',
        'vimeo.com' => 'Vimeo',
        'maps.googleapis.com' => 'Google Maps',
        'google.com/maps' => 'Google Maps',
        'hubspot.com' => 'HubSpot',
        'hs-scripts.com' => 'HubSpot',
        'matomo.cloud' => 'Matomo Cloud',
        'pinterest.com' => 'Pinterest Tag',
        'snapchat.com' => 'Snap Pixel',
        'criteo.com' => 'Criteo',
        'outbrain.com' => 'Outbrain',
        'taboola.com' => 'Taboola',
    ];

    /**
     * @param array<string, array<string, mixed>> $pages      HtmlAnalyzer results keyed by URL
     * @param array<string, int>                  $thirdParties host => bytes from the Lighthouse network list of the start page
     * @param array<string, int>                  $legalStatus  kind => HTTP status of the fetched legal page
     * @param array<string, array<string, mixed>> $legalPages   kind => LegalPageReader result of the fetched legal page
     * @param list<string>                        $fontHosts    hosts of fonts loaded from other servers (Lighthouse and FontScanner)
     *
     * @return array<string, mixed>
     */
    public function analyze(array $pages, array $thirdParties, array $legalStatus, array $legalPages = [], array $fontHosts = []): array
    {
        $imprint = '';
        $privacy = '';
        $terms = '';
        $cmp = [];

        foreach ($pages as $page) {
            $imprint = $imprint ?: (string) ($page['links']['legal']['imprint'] ?? '');
            $privacy = $privacy ?: (string) ($page['links']['legal']['privacy'] ?? '');
            $terms = $terms ?: (string) ($page['links']['legal']['terms'] ?? '');
            $cmp = array_merge($cmp, (array) ($page['cmp'] ?? []));
        }

        $cmp = array_values(array_unique($cmp));

        $reachable = static fn (string $url, string $kind): bool => '' !== $url && ($legalStatus[$kind] ?? 200) < 400;
        $empty = static fn (string $kind): bool => (bool) ($legalPages[$kind]['empty'] ?? false);
        $path = static fn (string $url): string => strtolower(rtrim((string) preg_replace('~[#?].*$~', '', $url), '/'));

        $imprintOk = $reachable($imprint, 'imprint') && !$empty('imprint');
        // A privacy link to the imprint itself ("Impressum & Datenschutz", "/impressum#datenschutz") is the imprint page.
        $privacyOwnPage = '' !== $privacy && ('' === $imprint || $path($privacy) !== $path($imprint));
        $privacyOk = $privacyOwnPage && $reachable($privacy, 'privacy') && !$empty('privacy');
        $privacyInImprint = !$privacyOk && $imprintOk && (('' !== $privacy && !$privacyOwnPage) || (bool) ($legalPages['imprint']['privacy'] ?? false));

        $found = [];

        foreach ($thirdParties as $host => $bytes) {
            foreach (self::TRACKERS as $pattern => $label) {
                if ($host === $pattern || str_ends_with($host, '.'.$pattern) || str_contains($host, $pattern)) {
                    $found[$label] = ($found[$label] ?? 0) + (int) $bytes;
                    break;
                }
            }
        }

        $tracking = array_filter($found, static fn (string $label): bool => !\in_array($label, ['Google Maps', 'YouTube', 'Vimeo'], true), ARRAY_FILTER_USE_KEY);
        $embeds = array_diff_key($found, $tracking);

        return [
            'imprintUrl' => $imprint,
            'imprintOk' => $imprintOk,
            'imprintEmpty' => '' !== $imprint && $empty('imprint'),
            'privacyUrl' => $privacy,
            'privacyOk' => $privacyOk,
            'privacyEmpty' => $privacyOwnPage && $empty('privacy'),
            'privacyInImprint' => $privacyInImprint,
            'termsUrl' => $terms,
            'termsEmpty' => $reachable($terms, 'terms') && $empty('terms'),
            'cmp' => $cmp,
            'hasCmp' => [] !== $cmp,
            // Services seen on the very first load (Lighthouse loads without clicking any banner).
            'trackingBeforeConsent' => array_keys($tracking),
            'embedsBeforeConsent' => array_keys($embeds),
            // Fonts from other servers load with the page, a consent banner does not hold them back.
            'fontsBeforeConsent' => array_values(array_unique(array_map(FontScanner::label(...), $fontHosts))),
            'fontHosts' => array_values($fontHosts),
            'thirdPartyCount' => \count($thirdParties),
        ];
    }
}
