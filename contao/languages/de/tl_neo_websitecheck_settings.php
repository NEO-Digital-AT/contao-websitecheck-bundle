<?php

declare(strict_types=1);

$l = &$GLOBALS['TL_LANG']['tl_neo_websitecheck_settings'];

$l['brand_legend'] = 'Wer den Bericht verschickt';
$l['design_legend'] = 'Design';
$l['texts_legend'] = 'Konfigurationen (Texte, E-Mail, PDF je Sprache)';
$l['mail_legend'] = 'E-Mail';
$l['limits_legend'] = 'Grenzen';

$l['brand_name'] = ['Firma / Agentur', 'Steht im Kopf des Berichts, in der E-Mail und in der Signatur. Leer = Titel der Website.'];
$l['owner_name'] = ['Ansprechperson', 'Die Person, die den Bericht unterschreibt, z. B. „Max Mustermann“.'];
$l['phone'] = ['Telefon', 'Steht im Bericht und in der E-Mail-Signatur.'];
$l['website_url'] = ['Website', 'Ihre Website, z. B. https://example.com'];
$l['contact_page'] = ['Kontaktseite', 'Ziel der Knöpfe „Ergebnis besprechen“ in Bericht, E-Mail und Element (das Element kann sie überschreiben).'];
$l['privacy_page'] = ['Datenschutzseite', 'Wird unter dem Kontaktformular verlinkt.'];
$l['logo'] = ['Logo', 'PNG, JPG oder SVG für den Berichtskopf und die E-Mail. Dunkel auf hellem Grund.'];
$l['logo_dark'] = ['Logo für dunklen Hintergrund', 'Optionale Variante für den farbigen E-Mail-Kopf; leer = Logo oben.'];
$l['portrait'] = ['Porträtfoto', 'Optional, erscheint im Kasten „So geht es weiter“ und in der E-Mail-Signatur.'];
$l['color_primary'] = ['Hauptfarbe', 'PDF und E-Mail: Überschriften, Kästen, E-Mail-Kopf (Hex, z. B. 1f2328). Das Element auf der Website gestaltet das Theme-CSS (Variablen --nwc-*).'];
$l['color_accent'] = ['Akzentfarbe', 'PDF und E-Mail: Knöpfe, Marker, Linien (Hex, z. B. 0969da).'];
$l['texts_info'] = ['Konfigurationen', ''];
$l['from_name'] = ['Absendername', 'Leer = Firmenname.'];
$l['from_email'] = ['Absenderadresse', 'Leer = config.yaml oder die Contao-Admin-Adresse.'];
$l['reply_to'] = ['Antwortadresse', 'Dorthin gehen Antworten auf die Berichts-E-Mail.'];
$l['notify_email'] = ['Kopie an', 'Bekommt jeden Bericht als Kopie samt Name, E-Mail und Newsletter-Entscheidung des Besuchers. Leer = keine Kopie; ohne Lizenz geht der Bericht dann an die Absenderadresse.'];
$l['checks_per_ip_hour'] = ['Checks je Besucher und Stunde', 'Schutz vor Missbrauch und Kosten. 0 = Wert aus config.yaml (Standard 2).'];
$l['checks_per_ip'] = ['Checks je Besucher und Tag', '0 = Wert aus config.yaml (Standard 10).'];
$l['cache_hours'] = ['Zwischenspeicher in Stunden', 'Dieselbe Domain innerhalb dieser Zeit: gespeichertes Ergebnis. 0 = config.yaml (24).'];
$l['retention_days'] = ['Aufbewahrung in Tagen', 'Danach werden Checks samt Kontaktdaten gelöscht. 0 = config.yaml (90).'];
$l['max_pages'] = ['Seiten je Check', 'Startseite plus wichtige Unterseiten, höchstens 8. 0 = config.yaml (5).'];

$l['edit'] = ['Bearbeiten', 'Einstellungen bearbeiten'];
$l['configs'] = ['Konfigurationen', 'E-Mail, PDF und Texte je Sprache gestalten'];
$l['preview_pdf'] = ['Vorschau PDF', 'PDF-Bericht mit dem letzten fertigen Check (oder Beispieldaten) anzeigen'];
$l['preview_mail'] = ['Vorschau E-Mail', 'Berichts-E-Mail mit dem letzten fertigen Check (oder Beispieldaten) anzeigen'];
$l['textsButton'] = 'Konfigurationen bearbeiten (%d)';
$l['textsNone'] = 'Noch keine Konfiguration. Legen Sie je Sprache eine an: Dort gestalten Sie E-Mail und PDF-Bericht, Betreff, Absender und die Texte des Elements. Bis dahin gelten die mitgelieferten Vorlagen.';
$l['textsList'] = 'Vorhanden: %s';

$l['api_legend'] = 'KI-Modell und Zugänge';
$l['api_info'] = 'Woher die Schlüssel kommen';
$l['ai_model'] = ['KI-Modell', 'Sobald ein KI-Schlüssel bekannt ist, zur Auswahl aus den Modellen des Anbieters. Leer = Wert aus der Server-Konfiguration.'];
$l['apiIntro'] = 'Der PageSpeed-Schlüssel und der KI-Schlüssel stehen unter „Zugänge und Lizenzen“. Ist dort kein PageSpeed-Schlüssel hinterlegt, gilt der aus der Server-Konfiguration (config.yaml / Umgebung).';
$l['apiPsi'] = 'PageSpeed';
$l['apiAi'] = 'KI';
$l['apiModel'] = 'Modell in Verwendung';
$l['apiMissingAi'] = 'fehlt — der Check läuft ohne KI-Bewertung';
$l['apiMissingPsi'] = 'fehlt — die Website nimmt keine Prüfung an';
$l['apiFromAccess'] = 'unter „Zugänge und Lizenzen“ hinterlegt';
$l['apiOpenAccess'] = 'Öffnen';
$l['apiFromConfig'] = 'aus der Server-Konfiguration';
$l['modelFromConfig'] = '– Server-Konfiguration (%s) –';
$l['test_api'] = ['Verbindung prüfen', ''];
$l['checks_total_hour'] = ['Neue Checks gesamt je Stunde', 'Alle Besucher zusammen; schützt das KI-Budget vor automatisierten Abrufen. Gespeicherte Ergebnisse zählen nicht. 0 = config.yaml (20).'];
$l['checks_total_day'] = ['Neue Checks gesamt je Tag', 'Alle Besucher zusammen. 0 = config.yaml (60).'];
$l['mail_note'] = 'Ohne Lizenz geht diese E-Mail mit dem PDF-Bericht nur an Sie als Betreiber der Website (an die Adresse unter „Kopie an“, sonst an die Absenderadresse), nie an den Besucher.';

// Settings without effect: the reason at the field (Backend\FieldLock).
$GLOBALS['TL_LANG']['tl_neo_websitecheck_settings']['locked_ai'] = 'Noch keine KI-Verbindung unter „Zugänge und Lizenzen“ (Schlüssel und Adresse): Ohne sie gibt es keine KI-Bewertung, und das Modell wird nicht verwendet.';
