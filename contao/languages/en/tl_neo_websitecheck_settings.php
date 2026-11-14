<?php

declare(strict_types=1);

$l = &$GLOBALS['TL_LANG']['tl_neo_websitecheck_settings'];

$l['brand_legend'] = 'Who sends the report';
$l['design_legend'] = 'Design';
$l['texts_legend'] = 'Configurations (texts, e-mail, PDF per language)';
$l['mail_legend'] = 'E-mail';
$l['limits_legend'] = 'Limits';

$l['brand_name'] = ['Company / agency', 'Shown in the report header, the e-mail and the signature. Empty = website title.'];
$l['owner_name'] = ['Contact person', 'The person who signs the report, e.g. "John Smith".'];
$l['phone'] = ['Phone', 'Shown in the report and the e-mail signature.'];
$l['website_url'] = ['Website', 'Your website, e.g. https://example.com'];
$l['contact_page'] = ['Contact page', 'Target of the "talk about the result" buttons in the report, the e-mail and the element (the element may override it).'];
$l['privacy_page'] = ['Privacy policy page', 'Linked under the lead form.'];
$l['logo'] = ['Logo', 'PNG, JPG or SVG for the report header and the e-mail. Dark on light background.'];
$l['logo_dark'] = ['Logo for dark background', 'Optional variant for the coloured e-mail header; empty = the logo above.'];
$l['portrait'] = ['Portrait photo', 'Optional, shown in the "what happens next" box and the e-mail signature.'];
$l['color_primary'] = ['Primary colour', 'PDF and e-mail: headlines, boxes, e-mail header (hex, e.g. 1f2328). The element on the website is styled by the theme CSS (--nwc-* properties).'];
$l['color_accent'] = ['Accent colour', 'PDF and e-mail: buttons, highlights, rules (hex, e.g. 0969da).'];
$l['texts_info'] = ['Configurations', ''];
$l['from_name'] = ['Sender name', 'Empty = company name.'];
$l['from_email'] = ['Sender address', 'Empty = config.yaml or the Contao admin e-mail.'];
$l['reply_to'] = ['Reply-to address', 'Where replies to the report e-mail go.'];
$l['notify_email'] = ['Copy to', 'Receives a copy of every report with the visitor\'s name, e-mail and newsletter choice. Empty = no copy; without a licence the report then goes to the sender address.'];
$l['checks_per_ip_hour'] = ['Checks per visitor and hour', 'Protects against abuse and cost. 0 = value from config.yaml (default 2).'];
$l['checks_per_ip'] = ['Checks per visitor and day', '0 = value from config.yaml (default 10).'];
$l['cache_hours'] = ['Cache in hours', 'Same domain again within this time: stored result. 0 = config.yaml (24).'];
$l['retention_days'] = ['Retention in days', 'Checks incl. lead data are deleted after this. 0 = config.yaml (90).'];
$l['max_pages'] = ['Pages per check', 'Start page plus key sub-pages, at most 8. 0 = config.yaml (5).'];

$l['edit'] = ['Edit', 'Edit settings'];
$l['configs'] = ['Configurations', 'Design e-mail, PDF and texts per language'];
$l['preview_pdf'] = ['Preview PDF', 'Render the PDF report with the last finished check (or demo data)'];
$l['preview_mail'] = ['Preview e-mail', 'Render the report e-mail with the last finished check (or demo data)'];
$l['textsButton'] = 'Edit configurations (%d)';
$l['textsNone'] = 'No configuration yet. Create one per language: that is where you design the e-mail and the PDF report, subject, sender and the element texts. Until then the bundled templates apply.';
$l['textsList'] = 'Existing: %s';

$l['api_legend'] = 'AI model and access keys';
$l['api_info'] = 'Where the keys come from';
$l['ai_model'] = ['AI model', 'Once an AI key is known, a selection of the provider\'s models. Empty = value from the server configuration.'];
$l['apiIntro'] = 'The PageSpeed key and the AI key are entered under "Access keys and licences". Without a PageSpeed key there, the one from the server configuration (config.yaml / environment) applies.';
$l['apiPsi'] = 'PageSpeed';
$l['apiAi'] = 'AI';
$l['apiModel'] = 'Model in use';
$l['apiMissingAi'] = 'missing — the check runs without the AI review';
$l['apiMissingPsi'] = 'missing — the website accepts no checks';
$l['apiFromAccess'] = 'stored under "Access keys and licences"';
$l['apiOpenAccess'] = 'Open';
$l['apiFromConfig'] = 'from the server configuration';
$l['modelFromConfig'] = '– server configuration (%s) –';
$l['test_api'] = ['Test connections', ''];
$l['checks_total_hour'] = ['New checks in total per hour', 'All visitors together; protects the AI budget from automated use. Cached results do not count. 0 = config.yaml (20).'];
$l['checks_total_day'] = ['New checks in total per day', 'All visitors together. 0 = config.yaml (60).'];
$l['mail_note'] = 'Without a licence this e-mail with the PDF report goes only to you as the website operator (to the address under “Copy to”, otherwise to the sender address), never to the visitor.';

// Settings without effect: the reason at the field (Backend\FieldLock).
$GLOBALS['TL_LANG']['tl_neo_websitecheck_settings']['locked_ai'] = 'No AI connection on “Access keys and licences” yet (key and address): without it the check has no AI review and the model is not used.';
