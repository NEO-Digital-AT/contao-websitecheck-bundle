<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Backend;

use Contao\System;
use Neo\BasicBundle\Access\AccessPage;
use Neo\WebsitecheckBundle\Settings;

/**
 * Messages at the top of the list of checks and of the settings: what is
 * missing on "Access keys and licences" and what that means for the website, each
 * with a link to the section (Basic bundle). Texts: TL_LANG.tl_neo_access of
 * this bundle.
 */
final class AccessWarnings
{
    public function __construct(private readonly Settings $settings)
    {
    }

    public function add(): void
    {
        System::loadLanguageFile(AccessPage::TABLE);
        $lang = $GLOBALS['TL_LANG'][AccessPage::TABLE];

        if ('' === $this->settings->pageSpeedKey()) {
            AccessPage::addMissingMessage($lang['websitecheck_psi_missing'] ?? '', 'websitecheck_psi_legend');
        }

        if ('' === $this->settings->keySource('ai')) {
            AccessPage::addMissingMessage($lang['websitecheck_ai_missing'] ?? '', AccessPage::AI_LEGEND);
        }
    }
}
