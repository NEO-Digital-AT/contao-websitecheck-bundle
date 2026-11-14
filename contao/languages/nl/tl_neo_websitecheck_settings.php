<?php

declare(strict_types=1);

$l = &$GLOBALS['TL_LANG']['tl_neo_websitecheck_settings'];

$l['brand_legend'] = 'Wie het rapport verstuurt';
$l['design_legend'] = 'Vormgeving';
$l['texts_legend'] = 'Configuraties (teksten, e-mail, pdf per taal)';
$l['mail_legend'] = 'E-mail';
$l['limits_legend'] = 'Limieten';

$l['brand_name'] = ['Bedrijf / bureau', 'Verschijnt in de kop van het rapport, in de e-mail en in de ondertekening. Leeg = titel van de website.'];
$l['owner_name'] = ['Contactpersoon', 'De persoon die het rapport ondertekent, bijv. “Jan Jansen”.'];
$l['phone'] = ['Telefoon', 'Verschijnt in het rapport en in de ondertekening van de e-mail.'];
$l['website_url'] = ['Website', 'Uw website, bijv. https://example.com'];
$l['contact_page'] = ['Contactpagina', 'Doel van de knoppen “resultaat bespreken” in het rapport, de e-mail en het element (het element kan dit overschrijven).'];
$l['privacy_page'] = ['Pagina privacyverklaring', 'Wordt onder het contactformulier gelinkt.'];
$l['logo'] = ['Logo', 'PNG, JPG of SVG voor de kop van het rapport en de e-mail. Donker op een lichte achtergrond.'];
$l['logo_dark'] = ['Logo voor donkere achtergrond', 'Optionele variant voor de gekleurde kop van de e-mail; leeg = het logo hierboven.'];
$l['portrait'] = ['Portretfoto', 'Optioneel, verschijnt in het kader “wat gebeurt er nu” en in de ondertekening van de e-mail.'];
$l['color_primary'] = ['Hoofdkleur', 'Pdf en e-mail: koppen, kaders, kop van de e-mail (hex, bijv. 1f2328). Het element op de website krijgt zijn opmaak via de CSS van het thema (--nwc-*-eigenschappen).'];
$l['color_accent'] = ['Accentkleur', 'Pdf en e-mail: knoppen, markeringen, lijnen (hex, bijv. 0969da).'];
$l['texts_info'] = ['Configuraties', ''];
$l['from_name'] = ['Naam afzender', 'Leeg = bedrijfsnaam.'];
$l['from_email'] = ['Adres afzender', 'Leeg = config.yaml of het beheerders-e-mailadres van Contao.'];
$l['reply_to'] = ['Antwoordadres', 'Waar antwoorden op de rapportmail naartoe gaan.'];
$l['notify_email'] = ['Kopie naar', 'Ontvangt een kopie van elk rapport met naam, e-mailadres en nieuwsbriefkeuze van de bezoeker. Leeg = geen kopie; zonder licentie gaat het rapport dan naar het afzenderadres.'];
$l['checks_per_ip_hour'] = ['Checks per bezoeker per uur', 'Beschermt tegen misbruik en kosten. 0 = waarde uit config.yaml (standaard 2).'];
$l['checks_per_ip'] = ['Checks per bezoeker per dag', '0 = waarde uit config.yaml (standaard 10).'];
$l['cache_hours'] = ['Cache in uren', 'Wordt hetzelfde domein binnen deze tijd opnieuw gecontroleerd, dan volgt het opgeslagen resultaat. 0 = config.yaml (24).'];
$l['retention_days'] = ['Bewaartermijn in dagen', 'Checks inclusief contactgegevens worden daarna verwijderd. 0 = config.yaml (90).'];
$l['max_pages'] = ['Pagina’s per check', 'Startpagina plus de belangrijkste subpagina’s, maximaal 8. 0 = config.yaml (5).'];

$l['edit'] = ['Bewerken', 'Instellingen bewerken'];
$l['configs'] = ['Configuraties', 'E-mail, pdf en teksten per taal vormgeven'];
$l['preview_pdf'] = ['Pdf bekijken', 'Het pdf-rapport genereren met de laatste afgeronde check (of voorbeeldgegevens)'];
$l['preview_mail'] = ['E-mail bekijken', 'De rapportmail genereren met de laatste afgeronde check (of voorbeeldgegevens)'];
$l['textsButton'] = 'Configuraties bewerken (%d)';
$l['textsNone'] = 'Nog geen configuratie. Maak er één per taal aan: daar geeft u de e-mail en het pdf-rapport vorm en stelt u onderwerp, afzender en de teksten van het element in. Tot die tijd gelden de meegeleverde templates.';
$l['textsList'] = 'Bestaand: %s';

$l['api_legend'] = 'AI-model en toegang';
$l['api_info'] = 'Waar de sleutels vandaan komen';
$l['ai_model'] = ['AI-model', 'Zodra een AI-sleutel bekend is, een keuze uit de modellen van de aanbieder. Leeg = waarde uit de serverconfiguratie.'];
$l['apiIntro'] = 'De PageSpeed-sleutel en de AI-sleutel staan onder „Toegang en licenties”. Staat daar geen PageSpeed-sleutel, dan geldt die uit de serverconfiguratie (config.yaml / omgeving).';
$l['apiPsi'] = 'PageSpeed';
$l['apiAi'] = 'AI';
$l['apiModel'] = 'Gebruikt model';
$l['apiMissingAi'] = 'ontbreekt — de controle loopt zonder AI-beoordeling';
$l['apiMissingPsi'] = 'ontbreekt — de website neemt geen controles aan';
$l['apiFromAccess'] = 'opgeslagen onder „Toegang en licenties”';
$l['apiOpenAccess'] = 'Openen';
$l['apiFromConfig'] = 'uit de serverconfiguratie';
$l['modelFromConfig'] = '– serverconfiguratie (%s) –';
$l['test_api'] = ['Verbindingen testen', ''];
$l['checks_total_hour'] = ['Nieuwe checks totaal per uur', 'Alle bezoekers samen; beschermt het AI-budget tegen geautomatiseerd gebruik. Gecachte resultaten tellen niet. 0 = config.yaml (20).'];
$l['checks_total_day'] = ['Nieuwe checks totaal per dag', 'Alle bezoekers samen. 0 = config.yaml (60).'];
$l['mail_note'] = 'Zonder licentie gaat deze e-mail met het pdf-rapport alleen naar u als beheerder van de website (naar het adres onder “Kopie naar”, anders naar het afzenderadres), nooit naar de bezoeker.';

// Settings without effect: the reason at the field (Backend\FieldLock).
$GLOBALS['TL_LANG']['tl_neo_websitecheck_settings']['locked_ai'] = 'Nog geen AI-verbinding onder „Toegang en licenties” (sleutel en adres): zonder die heeft de check geen AI-beoordeling en wordt het model niet gebruikt.';
