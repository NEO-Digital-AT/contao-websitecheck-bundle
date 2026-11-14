<?php

declare(strict_types=1);

use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
use Contao\System;
use Neo\WebsitecheckBundle\Backend\AccessWarnings;
use Neo\WebsitecheckBundle\Backend\LicensedSettings;
use Neo\WebsitecheckBundle\Backend\SettingsCallbacks;

/*
 * tl_neo_websitecheck_settings — the ONE settings record of the website check:
 * brand (name, person, phone, pages), design (logo, portrait, colours),
 * sender/copy addresses and limits. Everything an operator should be able to
 * change without touching config.yaml. Values here override the YAML config;
 * empty fields fall back to it.
 *
 * Like other single-record modules the list view is skipped: the module opens
 * the edit mask directly. Texts (PDF, e-mail, element) live per language in
 * the child table tl_neo_websitecheck_config ("Edit configurations" button,
 * with the visual e-mail/PDF designer). "Preview PDF"
 * and "Preview e-mail" render the templates with the last finished check or
 * bundled demo data, so changes can be checked immediately.
 */
$GLOBALS['TL_DCA']['tl_neo_websitecheck_settings'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_neo_websitecheck_config'],
        'enableVersioning' => true,
        'notCreatable' => true,
        'notCopyable' => true,
        'notDeletable' => true,
        'closed' => true,
        // One record, the list opens its form: the breadcrumb shows the module name only (BreadcrumbListener of the Basic bundle).
        'neoSingleRecord' => true,
        'onload_callback' => [
            [SettingsCallbacks::class, 'ensureRow'],
            [SettingsCallbacks::class, 'redirectToEditView'],
            [SettingsCallbacks::class, 'adjustModelField'],
            // Without an AI connection the model has no effect: disabled, the reason at the field.
            [SettingsCallbacks::class, 'lock'],
            [AccessWarnings::class, 'add'],
            // Without a licence the reply-to of the visitor e-mail is hidden; the value stays.
            [LicensedSettings::class, 'hideUnlicensed'],
        ],
        'sql' => ['keys' => ['id' => 'primary']],
    ],
    'list' => [
        'sorting' => ['mode' => DataContainer::MODE_SORTED, 'fields' => ['id'], 'panelLayout' => ''],
        'label' => ['fields' => ['brand_name'], 'format' => '%s'],
        'global_operations' => [
            'preview_pdf' => ['href' => 'key=preview_pdf', 'icon' => 'bundles/neowebsitecheck/pdf.svg', 'attributes' => 'target="_blank"', 'class' => 'header_icon'],
            'preview_mail' => ['href' => 'key=preview_mail', 'icon' => 'bundles/neowebsitecheck/mail.svg', 'attributes' => 'target="_blank"', 'class' => 'header_icon'],
        ],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'configs' => ['href' => 'table=tl_neo_websitecheck_config', 'icon' => 'edit.svg'],
        ],
    ],
    'edit' => [
        'buttons_callback' => [[SettingsCallbacks::class, 'addPreviewButtons'], [SettingsCallbacks::class, 'editButtons']],
    ],
    'palettes' => [
        'default' => '{brand_legend},brand_name,owner_name,phone,website_url,contact_page,privacy_page;'
            .'{design_legend},logo,logo_dark,portrait,color_primary,color_accent;'
            .'{texts_legend},texts_info;'
            .'{mail_legend},mail_note,from_name,from_email,reply_to,notify_email;'
            .'{limits_legend},checks_per_ip_hour,checks_per_ip,checks_total_hour,checks_total_day,cache_hours,retention_days,max_pages;'
            .'{api_legend},api_info,ai_model',
    ],
    'fields' => [
        'id' => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],

        // --- brand ---------------------------------------------------------
        'brand_name' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],
        'owner_name' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],
        'phone' => ['inputType' => 'text', 'eval' => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'], 'sql' => "varchar(64) NOT NULL default ''"],
        'website_url' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'rgxp' => 'url', 'decodeEntities' => true, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],
        'contact_page' => ['inputType' => 'pageTree', 'foreignKey' => 'tl_page.title', 'eval' => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'], 'sql' => 'int(10) unsigned NOT NULL default 0', 'relation' => ['type' => 'hasOne', 'load' => 'lazy']],
        'privacy_page' => ['inputType' => 'pageTree', 'foreignKey' => 'tl_page.title', 'eval' => ['fieldType' => 'radio', 'tl_class' => 'w50'], 'sql' => 'int(10) unsigned NOT NULL default 0', 'relation' => ['type' => 'hasOne', 'load' => 'lazy']],

        // --- design (PDF and e-mail) ----------------------------------------
        'logo' => ['inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg,png,jpg,jpeg', 'tl_class' => 'w50 clr'], 'sql' => 'binary(16) NULL'],
        'logo_dark' => ['inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg,png,jpg,jpeg', 'tl_class' => 'w50'], 'sql' => 'binary(16) NULL'],
        'portrait' => ['inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'png,jpg,jpeg,webp', 'tl_class' => 'w50 clr'], 'sql' => 'binary(16) NULL'],
        'color_primary' => ['inputType' => 'text', 'eval' => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard clr'], 'sql' => "varchar(6) NOT NULL default ''"],
        'color_accent' => ['inputType' => 'text', 'eval' => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'], 'sql' => "varchar(6) NOT NULL default ''"],

        // --- texts (child table) -------------------------------------------
        'texts_info' => ['input_field_callback' => [SettingsCallbacks::class, 'textsInfo']],

        // --- mail (without a licence the e-mail goes to the operator only) --
        'mail_note' => ['input_field_callback' => [LicensedSettings::class, 'mailNote']],
        'from_name' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],
        'from_email' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],
        'reply_to' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],
        'notify_email' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'], 'sql' => "varchar(255) NOT NULL default ''"],

        // --- limits --------------------------------------------------------
        'checks_per_ip_hour' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],
        'checks_per_ip' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],
        'checks_total_hour' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],
        'checks_total_day' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],
        'cache_hours' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],
        'retention_days' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],
        'max_pages' => ['inputType' => 'text', 'eval' => ['rgxp' => 'natural', 'tl_class' => 'w25'], 'sql' => 'int(10) unsigned NOT NULL default 0'],

        // The PageSpeed key and route and key of the AI connection are entered on
        // "Access keys and licences" (Basic bundle, contao/dca/tl_neo_access.php of
        // this bundle); api_info says where each comes from. The columns
        // psi_api_key, ai_api_key and ai_base_url stay for existing installations
        // and the one-time takeovers.
        'api_info' => ['input_field_callback' => [SettingsCallbacks::class, 'apiInfo']],
        'psi_api_key' => ['sql' => "varchar(512) NOT NULL default ''"],
        'ai_api_key' => ['sql' => "varchar(512) NOT NULL default ''"],
        // A text field until a key is known; then adjustModelField() turns it into
        // a searchable selection of the models the provider really offers.
        'ai_model' => ['inputType' => 'text', 'eval' => ['maxlength' => 128, 'decodeEntities' => true, 'tl_class' => 'w50 clr'], 'sql' => "varchar(128) NOT NULL default ''"],
        'ai_base_url' => ['sql' => "varchar(255) NOT NULL default ''"],
    ],
];
