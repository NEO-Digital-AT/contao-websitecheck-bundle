<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Check;

/**
 * Known critical PHP vulnerabilities and the security support of the PHP
 * branches, and the comparison of a version string against them.
 *
 * A version string as the server announces it ("8.3.12", "8.2.29-1~deb12u1",
 * "7.4.33") is "upstream" when it is the PHP project's own number (also the
 * builds of deb.sury.org, which follow upstream releases) and "distro" when a
 * Linux distribution added its package revision, or when it is one of the
 * versions long-term distributions keep and patch (DISTRIBUTION_VERSIONS):
 * distributions backport fixes without changing the PHP number, so such a
 * build cannot be judged from outside and needs a confirmation from the
 * hosting provider. Hosts that patch other versions themselves exist too; the
 * warning for an affected upstream number says so.
 *
 * Sources: php.net/supported-versions.php and the PHP security advisory of
 * 21 Oct 2026 (www.php.net/archive/2026.php). Add new advisories at the top.
 */
final class PhpAdvisories
{
    /** End of security support per branch; older branches are all unsupported. */
    public const SUPPORT_UNTIL = [
        '8.1' => '2025-12-31',
        '8.2' => '2026-12-31',
        '8.3' => '2027-12-31',
        '8.4' => '2028-12-31',
        '8.5' => '2029-12-31',
    ];

    /**
     * Versions frozen in long-term Linux distributions that receive their fixes as backports
     * and report the bare upstream number (Ubuntu's packages have no suffix in PHP_VERSION).
     * A server reporting one of these cannot be judged from outside.
     */
    public const DISTRIBUTION_VERSIONS = [
        '8.3.6' => 'Ubuntu 24.04 LTS',
        '8.1.2' => 'Ubuntu 22.04 LTS',
        '7.4.3' => 'Ubuntu 20.04 LTS',
        '7.2.24' => 'Ubuntu 18.04 LTS, RHEL 8',
    ];

    public const ADVISORIES = [
        'php2shell' => [
            'name' => 'PHP2Shell',
            'cves' => ['CVE-2026-109618', 'CVE-2026-109619'],
            'published' => '2026-10-21',
            // First fixed release per branch. Branches older than the oldest key got no
            // official fix; branches newer than the newest key are not affected.
            'fixed' => ['8.2' => '8.2.35', '8.3' => '8.3.36', '8.4' => '8.4.27', '8.5' => '8.5.12', '8.6' => '8.6.0RC4'],
        ],
    ];

    /**
     * @return array{version: string, branch: string, build: string, partial: bool, eol: bool, status: string, affected: list<string>, fixed: string, target: string, cves: list<string>, names: list<string>}|null
     *               null when the string is no PHP version
     *
     * status: "vulnerable" (affected, upstream build) · "verify" (affected or unclear, distribution
     * build or no patch number) · "eol" (no known vulnerability, but no security updates any more) · "ok"
     */
    public static function evaluate(string $raw, \DateTimeInterface $today): array|null
    {
        if (!preg_match('/^(\d+)\.(\d+)(?:\.(\d+))?((?:alpha|beta|RC|dev)\d*)?(.*)$/i', trim($raw), $m)) {
            return null;
        }

        $branch = $m[1].'.'.$m[2];
        $partial = '' === ($m[3] ?? '');
        $version = $branch.($partial ? '' : '.'.$m[3]).($m[4] ?? '');
        $suffix = trim((string) ($m[5] ?? ''));
        $build = ('' === $suffix || false !== stripos($suffix, 'sury')) && !isset(self::DISTRIBUTION_VERSIONS[$branch.'.'.($m[3] ?? '')]) ? 'upstream' : 'distro';

        $until = self::SUPPORT_UNTIL[$branch] ?? null;
        $eol = null !== $until ? $today->format('Y-m-d') > $until : version_compare($branch, (string) array_key_first(self::SUPPORT_UNTIL), '<');

        $affected = [];
        $unclear = false;
        $fixed = '';
        $cves = [];
        $names = [];

        foreach (self::ADVISORIES as $id => $advisory) {
            $branches = array_keys($advisory['fixed']);
            $hit = false;

            if (isset($advisory['fixed'][$branch])) {
                if ($partial) {
                    $unclear = true;
                } elseif (version_compare($version, $advisory['fixed'][$branch], '<')) {
                    $hit = true;
                    $fixed = '' !== $fixed ? $fixed : $advisory['fixed'][$branch];
                }
            } elseif (version_compare($branch, (string) $branches[0], '<')) {
                $hit = true;
            }

            if ($hit) {
                $affected[] = $id;
                $cves = array_merge($cves, $advisory['cves']);
                $names[] = $advisory['name'];
            }
        }

        $status = match (true) {
            // a version without patch number only counts as affected in a branch that got no fix at all
            [] !== $affected && 'upstream' === $build => 'vulnerable',
            [] !== $affected || $unclear => 'verify',
            $eol => 'eol',
            default => 'ok',
        };

        return [
            'version' => $version,
            'branch' => $branch,
            'build' => $build,
            'partial' => $partial,
            'eol' => $eol,
            'status' => $status,
            'affected' => $affected,
            // the release of the own branch that fixes it ('' = the branch got no fix)
            'fixed' => $fixed,
            // what to update to: the own fixed release, else the lowest branch with support for at least another year
            'target' => '' !== $fixed ? $fixed : self::target($today),
            'cves' => array_values(array_unique($cves)),
            'names' => $names,
        ];
    }

    /** The newest fixed releases of all branches that still get security updates ("8.2.35, 8.3.36 …"). */
    public static function fixedReleases(\DateTimeInterface $today): array
    {
        $out = [];

        foreach (self::ADVISORIES as $advisory) {
            foreach ($advisory['fixed'] as $branch => $release) {
                $until = self::SUPPORT_UNTIL[$branch] ?? null;

                if (null !== $until && $today->format('Y-m-d') <= $until && !preg_match('/[a-z]/i', $release)) {
                    $out[$branch] = $release;
                }
            }

            break; // the newest advisory decides what is current
        }

        return array_values($out);
    }

    private static function target(\DateTimeInterface $today): string
    {
        $inAYear = (new \DateTimeImmutable($today->format('Y-m-d')))->modify('+1 year')->format('Y-m-d');
        $newest = array_values(self::ADVISORIES)[0];

        foreach ($newest['fixed'] as $branch => $release) {
            if (($until = self::SUPPORT_UNTIL[$branch] ?? null) !== null && $until >= $inAYear) {
                return $release;
            }
        }

        return (string) end($newest['fixed']);
    }
}
