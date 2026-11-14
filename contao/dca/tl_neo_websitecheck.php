<?php

declare(strict_types=1);

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\DC_Table;
use Contao\DataContainer;
use Contao\System;
use Neo\WebsitecheckBundle\Backend\AccessWarnings;
use Neo\WebsitecheckBundle\License\Editions;

/*
 * One row per website check: job state while running, result + lead data once
 * finished. Rows are created by the front end only; the back end lists them,
 * lets the operator open the details, re-send the report or delete a row.
 * Everything is purged after limits.retention_days by the daily cron job.
 */
$GLOBALS['TL_DCA']['tl_neo_websitecheck'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => false,
        'notCreatable' => true,
        'notCopyable' => true,
        'onload_callback' => [
            // The button "Settings" only for users who may open the settings module.
            static function (): void {
                if (!System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, 'neo_websitecheck_settings')) {
                    unset($GLOBALS['TL_DCA']['tl_neo_websitecheck']['list']['global_operations']['settings']);
                }
            },
            // Missing PageSpeed or AI key: say so, with a link to "Access keys and licences".
            [AccessWarnings::class, 'add'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'domain' => 'index',
                'status' => 'index',
                'ip_hash,created' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['created DESC'],
            'flag' => DataContainer::SORT_DAY_DESC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['domain', 'grade', 'status', 'firstname', 'lastname', 'email', 'created'],
            'showColumns' => true,
        ],
        'global_operations' => [
            'settings' => [
                // With the table, so that the table of the current list (e.g. after the breadcrumb) is not carried over.
                'href' => 'do=neo_websitecheck_settings&table=tl_neo_websitecheck_settings',
                'icon' => 'settings.svg',
                'class' => 'header_settings',
                'primary' => true,
            ],
        ],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            // The PDF report of a finished check, also without a licence (it is for the operator then).
            'pdf' => [
                'href' => 'key=pdf',
                'icon' => 'bundles/neowebsitecheck/pdf.svg',
                'attributes' => 'target="_blank"',
                'button_callback' => static function (array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string {
                    if ('done' !== (string) $row['status']) {
                        return '';
                    }

                    return '<a href="'.\Contao\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.htmlspecialchars($title).'" '.$attributes.'>'.\Contao\Image::getHtml($icon, $label).'</a> ';
                },
            ],
            'resend' => [
                'href' => 'key=resend',
                'icon' => 'bundles/neowebsitecheck/mail.svg',
                'attributes' => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(this.dataset.confirm))return false"',
                'button_callback' => static function (array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string {
                    // The e-mail to the visitor belongs to the licence of the check's website.
                    if (!$row['claimed'] || '' === (string) $row['email'] || !System::getContainer()->get(Editions::class)->allows(Editions::VISITOR_MAIL, Editions::hostOf($row))) {
                        return '';
                    }

                    $confirm = $GLOBALS['TL_LANG']['tl_neo_websitecheck']['resendConfirm'] ?? 'Re-send the report to %s?';
                    $attributes = str_replace('onclick', 'data-confirm="'.htmlspecialchars(sprintf($confirm, $row['email'])).'" onclick', $attributes);

                    return '<a href="'.\Contao\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.htmlspecialchars($title).'"'.$attributes.'>'.\Contao\Image::getHtml($icon, $label).'</a> ';
                },
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '').'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => ['href' => 'act=show', 'icon' => 'show.svg'],
        ],
    ],
    'palettes' => [
        'default' => '{check_legend},domain,url,status,grade,created,finished;{lead_legend},firstname,lastname,email,contact,newsletter,claimed;{result_legend},summary',
    ],
    'fields' => [
        'id' => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'created' => [
            'filter' => true,
            'sorting' => true,
            'flag' => DataContainer::SORT_DAY_DESC,
            'eval' => ['rgxp' => 'datim', 'readonly' => true, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'finished' => [
            'eval' => ['rgxp' => 'datim', 'readonly' => true, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'domain' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['readonly' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'url' => [
            'inputType' => 'text',
            'eval' => ['readonly' => true, 'tl_class' => 'w50'],
            'sql' => 'text NULL',
        ],
        'locale' => ['sql' => "varchar(32) NOT NULL default 'en'"],
        'config_id' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        // Address of the website the check was started on (not the checked domain):
        // the licence of this website decides about the PDF report and the visitor e-mail.
        'site_host' => ['sql' => "varchar(255) NOT NULL default ''"],
        'status' => [
            'filter' => true,
            'inputType' => 'text',
            // Shown translated in the list and the filter; the form shows the stored value (read only).
            'options' => ['queued', 'running', 'done', 'failed'],
            'reference' => &$GLOBALS['TL_LANG']['tl_neo_websitecheck']['status_options'],
            'eval' => ['readonly' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default 'queued'",
        ],
        'step' => ['sql' => "varchar(32) NOT NULL default ''"],
        'attempts' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'locked_until' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'step_started' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'step_times' => ['sql' => 'text NULL'],
        'error' => ['sql' => 'text NULL'],
        'ip_hash' => ['sql' => "varchar(64) NOT NULL default ''"],
        'token' => ['sql' => "varchar(64) NOT NULL default ''"],
        'source_id' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'data' => ['sql' => 'mediumtext NULL'],
        'result' => ['sql' => 'mediumtext NULL'],
        'grade' => [
            'filter' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['readonly' => true, 'tl_class' => 'w50'],
            'sql' => 'int(1) unsigned NOT NULL default 0',
        ],
        'firstname' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'lastname' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'email' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        // Visitor allowed one personal follow-up about this result (consent text and time in data.consent).
        'contact' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['readonly' => true, 'tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'newsletter' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['readonly' => true, 'tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'claimed' => [
            'eval' => ['rgxp' => 'datim', 'readonly' => true, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'mail_sent' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'pdf_path' => ['sql' => "varchar(255) NOT NULL default ''"],
        'summary' => [
            'inputType' => 'textarea',
            'eval' => ['readonly' => true, 'rows' => 12, 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
    ],
];
