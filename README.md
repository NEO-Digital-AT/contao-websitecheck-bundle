# Website Check for Contao 5

A website check as a content element: a visitor enters the address of a website, the extension measures it with Google PageSpeed Insights, runs its own checks of HTML, legal pages, AI visibility and the server, optionally lets an AI read the pages like a first-time visitor, grades seven areas with school grades and shows the result on the page in exchange for first name, last name and e-mail address. A PDF report goes out by e-mail: with a licence to the visitor, without one to the website operator.

Free without a licence key, with a credit line in the element, the PDF and the e-mail — see [License](#license).

*Deutsche Fassung weiter unten: [Deutsch](#website-check-für-contao-5-deutsch).*

## Features

- Live progress in six steps with a screenshot of the checked website and markers on the findings, 60 to 120 seconds per check.
- Seven graded areas: speed, accessibility, findability on Google, visibility in AI search, content and clarity, legal and privacy, mobile; plus a server check of the PHP version against known security advisories.
- Lead form with first name, last name, e-mail, optional one-time contact permission and, with a licence, newsletter sign-up via the Brevo module or Contao's newsletter.
- Visual designer for e-mail and PDF report, with templates, previews and an advanced mode in Twig for administrators; every word editable.
- One configuration per language; texts in eleven languages, other languages translated once by the AI connection.
- Limits per visitor and in total, cache per domain, spam protection with ALTCHA (Contao 5.4 and later), retention period for all data.
- No worker needed: the steps run in the requests of the visitor's browser and the Contao cron.
- Neutral design: CSS variables for the element, colours and logo of the settings for PDF and e-mail.

## Requirements

- Contao 5.3 to 6.0 with PHP 8.2 to 8.5, as far as the Contao version supports it (Contao 5.7 needs PHP 8.3, Contao 6.0 PHP 8.4), with GD.
- `neo-digital/contao-basic-bundle`, installed automatically: keys, AI connection and licence on **Access keys and licences**.
- A Google PageSpeed Insights API key (free) and a working mailer.
- Optional: an OpenAI-compatible AI provider, Gotenberg for Chromium PDFs, the Brevo module or Contao's newsletter.

## Installation

### Contao Manager

1. Search for `neo-digital/contao-websitecheck-bundle`.
2. Click **Add** and then **Apply changes**.
3. Run the database update.

### Composer

```bash
composer require neo-digital/contao-websitecheck-bundle
php vendor/bin/contao-console contao:migrate
```

Then enter the PageSpeed key under **NEO Digital › Access keys and licences**: [Setup](docs/en/setup.md).

## Documentation

The full documentation comes with the extension: [docs/en/README.md](docs/en/README.md).

- [Installation](docs/en/installation.md) – requirements, updating, removing
- [Setup](docs/en/setup.md) – licence, keys, AI, settings, PDF, newsletter, `config.yaml`
- [Permissions](docs/en/permissions.md) – what editors need
- [User manual](docs/en/usage/README.md) – element, what is checked, designer, list of checks
- [Customising](docs/en/customising.md) – CSS variables, templates, analytics events
- [Privacy](docs/en/privacy.md) – what is stored and sent where
- [Troubleshooting](docs/en/troubleshooting.md)

## Development

The unit tests (PHPUnit 10.5 or 11) cover the licence editions, the credit line in the PDF report (its place on the page) and in the e-mails, the check of the address a visitor enters (public websites only), the choice of pages, the reading of a page, of the server and of robots.txt and llms.txt, the PHP advisories, the grading, the PageSpeed client, the safe HTML of the designs, the text version of the e-mail, the checks with their token and limits, the settings without licence and the migrations; the database is SQLite in memory with the tables of the DCA files.

```bash
composer install
composer test
```

The CI workflow `.github/workflows/tests.yml` runs the tests on PHP 8.2 to 8.5 (PHP 8.2 with Contao 5.3 and PHPUnit 10.5). To test against an existing Contao installation instead, point `NEO_TEST_AUTOLOAD` to its `vendor/autoload.php`.

## License

**Licence keys:** <https://store.neo-digital.at/extensions/website-check>

Without a licence key the extension is free on any number of websites, with the functions of the free edition; the element, the PDF and the e-mail then show the credit line, which must stay visible and unchanged, and the e-mail with the PDF goes to the website operator only. A licence key for the domain (**Access keys and licences › Licences**) removes the credit line, sends the e-mail with the PDF to the visitor and adds the newsletter sign-up. The key is checked with the licence service when it is saved and once a day; only the key, the extension code, the domains of the website and the versions are sent, no visitor data. The version available at the time of the order (for free use: of the download) for the then current Contao long-term support version works with it until its end of life; there is no entitlement to versions for later long-term support versions. Support by separate agreement. Full terms: [LICENSE](LICENSE).

The bundled font Arimo (`resources/fonts/arimo`) is under the SIL Open Font License 1.1 (`OFL.txt` in that folder).

---

# Website-Check für Contao 5 (Deutsch)

Ein Website-Check als Inhaltselement: Ein Besucher gibt die Adresse einer Website ein, die Erweiterung misst sie mit Google PageSpeed Insights, prüft HTML, Rechtsseiten, Sichtbarkeit in KI-Suchen und den Server, lässt die Seiten auf Wunsch von einer KI lesen wie von einem Erstbesucher, benotet sieben Bereiche mit Schulnoten und zeigt das Ergebnis auf der Seite, im Tausch gegen Vorname, Nachname und E-Mail-Adresse. Ein PDF-Bericht geht per E-Mail hinaus: mit Lizenz an den Besucher, ohne Lizenz an den Betreiber der Website.

Ohne Lizenzschlüssel kostenlos, mit einem Hinweis in Element, PDF und E-Mail, siehe [Lizenz](#lizenz).

## Funktionen

- Live-Fortschritt in sechs Schritten mit einem Screenshot der geprüften Website und Markierungen an den Befunden, 60 bis 120 Sekunden je Check.
- Sieben benotete Bereiche: Ladezeit, Barrierefreiheit, Auffindbarkeit bei Google, Sichtbarkeit in KI-Suchen, Inhalt und Klarheit, Recht und Datenschutz, Mobil; dazu eine Serverprüfung der PHP-Version gegen bekannte Sicherheitswarnungen.
- Kontaktformular mit Vorname, Nachname, E-Mail, freiwilliger einmaliger Kontakterlaubnis und, mit Lizenz, Newsletter-Anmeldung über das Brevo-Modul oder Contaos Newsletter.
- Visueller Designer für E-Mail und PDF-Bericht, mit Vorlagen, Vorschau und einem erweiterten Modus in Twig für Administratoren; jedes Wort änderbar.
- Eine Konfiguration je Sprache; Texte in elf Sprachen, andere Sprachen übersetzt die KI-Verbindung einmalig.
- Grenzen je Besucher und gesamt, Zwischenspeicher je Domain, Spam-Schutz mit ALTCHA (ab Contao 5.4), Aufbewahrungsfrist für alle Daten.
- Kein Worker nötig: Die Schritte laufen in den Anfragen des Browsers und im Contao-Cron.
- Neutrales Design: CSS-Variablen für das Element, Farben und Logo der Einstellungen für PDF und E-Mail.

## Voraussetzungen

- Contao 5.3 bis 6.0 mit PHP 8.2 bis 8.5, soweit die Contao-Fassung sie unterstützt (Contao 5.7 braucht PHP 8.3, Contao 6.0 PHP 8.4), mit GD.
- `neo-digital/contao-basic-bundle`, wird automatisch mitinstalliert: Schlüssel, KI-Verbindung und Lizenz unter **Zugänge und Lizenzen**.
- Ein Google-PageSpeed-Insights-API-Schlüssel (kostenlos) und ein funktionierender Mailversand.
- Optional: ein OpenAI-kompatibler KI-Anbieter, Gotenberg für PDFs aus Chromium, das Brevo-Modul oder Contaos Newsletter.

## Installation

### Contao Manager

1. Nach `neo-digital/contao-websitecheck-bundle` suchen.
2. Auf **Hinzufügen** und danach auf **Änderungen anwenden** klicken.
3. Die Datenbank aktualisieren.

### Composer

```bash
composer require neo-digital/contao-websitecheck-bundle
php vendor/bin/contao-console contao:migrate
```

Danach den PageSpeed-Schlüssel unter **NEO Digital › Zugänge und Lizenzen** eintragen: [Einrichtung](docs/de/setup.md).

## Dokumentation

Die vollständige Dokumentation liegt der Erweiterung bei: [docs/de/README.md](docs/de/README.md).

- [Installation](docs/de/installation.md) – Voraussetzungen, Aktualisieren, Entfernen
- [Einrichtung](docs/de/setup.md) – Lizenz, Schlüssel, KI, Einstellungen, PDF, Newsletter, `config.yaml`
- [Rechte](docs/de/permissions.md) – was Redakteure brauchen
- [Bedienungsanleitung](docs/de/usage/README.md) – Element, was geprüft wird, Designer, Liste der Checks
- [Anpassen](docs/de/customising.md) – CSS-Variablen, Templates, Tracking-Ereignisse
- [Datenschutz](docs/de/privacy.md) – was gespeichert und wohin gesendet wird
- [Fehlersuche](docs/de/troubleshooting.md)

## Entwicklung

Die Unit-Tests (PHPUnit 10.5 oder 11) prüfen die Lizenzstufen, den Hinweis „powered by“ im PDF-Bericht (seine Lage auf der Seite) und in den E-Mails, die Prüfung der Adresse, die ein Besucher eingibt (nur öffentliche Websites), die Auswahl der Seiten, das Auslesen einer Seite, des Servers, der robots.txt und der llms.txt, die PHP-Sicherheitswarnungen, die Benotung, den PageSpeed-Client, das sichere HTML der Designs, die Textfassung der E-Mail, die Prüfungen mit ihrem Token und ihren Grenzen, die Einstellungen ohne Lizenz und die Migrationen; die Datenbank ist SQLite im Arbeitsspeicher mit den Tabellen aus den DCA-Dateien.

```bash
composer install
composer test
```

Der CI-Ablauf `.github/workflows/tests.yml` führt die Tests unter PHP 8.2 bis 8.5 aus (PHP 8.2 mit Contao 5.3 und PHPUnit 10.5). Gegen eine bestehende Contao-Installation testen: `NEO_TEST_AUTOLOAD` auf deren `vendor/autoload.php` setzen.

## Lizenz

**Lizenzschlüssel:** <https://store.neo-digital.at/extensions/website-check>

Ohne Lizenzschlüssel ist die Erweiterung auf beliebig vielen Websites kostenlos, mit den Funktionen der Gratisfassung; Element, PDF und E-Mail zeigen dann den Hinweis, der sichtbar und unverändert bleiben muss, und die E-Mail mit dem PDF geht nur an den Betreiber der Website. Ein Lizenzschlüssel für die Domain (**Zugänge und Lizenzen › Lizenzen**) entfernt den Hinweis, schickt die E-Mail mit dem PDF an den Besucher und bringt die Newsletter-Anmeldung dazu. Der Schlüssel wird beim Speichern und einmal täglich beim Lizenzdienst geprüft; übertragen werden nur der Schlüssel, der Code der Erweiterung, die Domains der Website und die Versionen, keine Daten von Besuchern. Die bei der Bestellung (bei unentgeltlicher Nutzung: beim Herunterladen) für die dann aktuelle Long-Term-Support-Version von Contao bereitstehende Fassung funktioniert mit dieser bis zu deren End of Life; ein Anspruch auf Fassungen für spätere Long-Term-Support-Versionen besteht nicht. Support nach gesonderter Vereinbarung. Vollständige Bedingungen: [LICENSE](LICENSE).

Die mitgelieferte Schrift Arimo (`resources/fonts/arimo`) steht unter der SIL Open Font License 1.1 (`OFL.txt` in diesem Ordner).
