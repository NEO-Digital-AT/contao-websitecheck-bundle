<?php

declare(strict_types=1);

use Neo\BasicBundle\Access\AccessPage;
use Neo\WebsitecheckBundle\License\Editions;
use Neo\WebsitecheckBundle\Settings;

/*
 * The website check's part of "Access keys and licences" (Basic bundle): it uses
 * the shared AI connection for its AI review, keeps the PageSpeed key there and
 * has a licence row. Without the PageSpeed key the form on the website accepts
 * no checks; without a licence the element shows the credit line and there is
 * no PDF report and no e-mail to the visitor (Editions).
 */
AccessPage::useAi('Website-Check');

$GLOBALS['TL_DCA']['tl_neo_access']['fields'][Settings::PSI_KEY] = AccessPage::secretField();
AccessPage::addSection('websitecheck_psi_legend', [Settings::PSI_KEY], 'Website-Check');

AccessPage::addLicense(Editions::MODULE, Editions::PACKAGE, 'Website-Check');
