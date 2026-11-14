<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\License;

use Neo\BasicBundle\License\LicenseService;

/**
 * What the licence of the website check unlocks on a website, from the licence
 * check of the Basic bundle (signed certificate of the licence service,
 * checked locally on every request).
 *
 * - Free: the check and the result on the page, the AI review, the PDF report
 *   and the designed e-mail, which go to the site operator only; the element,
 *   the PDF and the e-mail carry the credit line.
 * - Every edition ("basic" and above): the e-mail with the PDF goes to the
 *   visitor (VISITOR_MAIL), the newsletter sign-up (NEWSLETTER), no credit
 *   line anywhere (UNBRANDED).
 *
 * A function that needs a licence later is one line in LICENSED and one call
 * of allows() where the function runs. There is no exception for development
 * addresses: like the credit line, everything follows the certificate.
 */
final class Editions
{
    /** Code of the extension at the licence service. */
    public const MODULE = 'websitecheck';

    /** Composer package, reported to the licence service with its version. */
    public const PACKAGE = 'neo-digital/contao-websitecheck-bundle';

    /** In LICENSED: every edition, i.e. any valid licence. */
    public const ANY = '*';

    /** The e-mail with the PDF report to the visitor (without: to the operator only). */
    public const VISITOR_MAIL = 'visitor_mail';

    /** Newsletter sign-up in the lead form (Brevo module or Contao's newsletter). */
    public const NEWSLETTER = 'newsletter';

    /** Element, PDF report and e-mails without the credit line. */
    public const UNBRANDED = 'unbranded';

    /**
     * Functions that need a licence => the editions that include them. A
     * function that is not listed is free.
     *
     * @var array<string, list<string>>
     */
    private const LICENSED = [
        self::VISITOR_MAIL => [self::ANY],
        self::NEWSLETTER => [self::ANY],
        self::UNBRANDED => [self::ANY],
    ];

    public function __construct(private readonly LicenseService $licenses)
    {
    }

    /**
     * Whether a function is available on a website. $host is the website the
     * visitor is on; an empty host skips the domain check (back end, console,
     * checks stored before the host was recorded).
     */
    public function allows(string $feature, string $host): bool
    {
        $editions = self::LICENSED[$feature] ?? null;

        if (null === $editions) {
            return true;
        }

        $state = $this->licenses->state(self::MODULE, $host);

        return \in_array(self::ANY, $editions, true) ? $state->isLicensed() : $state->hasEdition(...$editions);
    }

    public function isLicensed(string $host): bool
    {
        return $this->licenses->state(self::MODULE, $host)->isLicensed();
    }

    /** Edition code of a valid licence ("basic" …), '' without one. */
    public function edition(string $host): string
    {
        return $this->licenses->state(self::MODULE, $host)->edition();
    }

    /**
     * The website a check was started on (column site_host), '' for checks
     * stored before the column existed and for demo checks.
     *
     * @param array<string, mixed> $row
     */
    public static function hostOf(array $row): string
    {
        return (string) ($row['site_host'] ?? '');
    }
}
