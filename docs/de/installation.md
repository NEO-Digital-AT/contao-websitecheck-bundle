---
titel: Installation
kurzfassung: Voraussetzungen, Installation des Website-Checks mit dem Contao Manager oder Composer, was die Installation ergänzt, Backend-Sprachen, Aktualisieren und Entfernen.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Installation

## Voraussetzungen

| Was | Wofür |
| --- | --- |
| Contao 5.3 bis 6.0 (`contao/core-bundle ^5.3 \|\| ^6.0`) | alles; unter Contao 4 lehnt Composer ab |
| PHP 8.2 bis 8.5, soweit die Contao-Fassung sie unterstützt (Contao 5.7 braucht 8.3, Contao 6.0 braucht 8.4), mit den Erweiterungen `dom`, `gd` (mit JPEG und WebP), `json`, `libxml`, `mbstring` und `simplexml` | alles; GD schneidet und skaliert die Screenshots |
| `neo-digital/contao-basic-bundle` | wird automatisch mitinstalliert; es hält Schlüssel, KI-Verbindung und Lizenz unter **Zugänge und Lizenzen** |
| Ein Google-PageSpeed-Insights-API-Schlüssel | jeder Check; ohne ihn nimmt das Formular auf der Website keine Prüfung an (siehe [Einrichtung](setup.md#2-pagespeed-schlüssel)) |
| Ein funktionierender Mailversand von Contao (`MAILER_DSN`) | die E-Mail mit dem PDF-Bericht |
| Der Contao-Cron (Web-Cron oder `contao:cron`, jede Minute) | Checks fertigstellen, die Besucher verlassen haben; alte Checks löschen |
| Optional: ein OpenAI-kompatibler KI-Anbieter (Requesty, OpenAI, eine eigene Adresse wie Ollama) | das Lesen der Seiten durch die KI |
| Optional: Gotenberg (Chromium als Dienst) | PDFs aus Chromium statt aus Dompdf |
| Optional: `neo-digital/contao-brevo-bundle` oder `contao/newsletter-bundle` | die Newsletter-Anmeldung im Kontaktformular (Lizenz) |
| Contao 5.4 oder neuer | der Spam-Schutz ALTCHA, der ab Contao 5.4 zu Contao gehört (keine Erweiterung nötig; Contao 6 bringt ALTCHA 3 mit, das Element nutzt es von selbst); Contao 5.3 hat kein ALTCHA, dort nutzt das Element ein verstecktes Feld und eine Mindestzeit zum Ausfüllen |

Dompdf (`dompdf/dompdf`) wird mit der Erweiterung installiert.

## Installation mit dem Contao Manager

1. Den Contao Manager öffnen.
2. Nach `neo-digital/contao-websitecheck-bundle` suchen.
3. Auf **Hinzufügen** und danach auf **Änderungen anwenden** klicken.
4. Wenn der Contao Manager fragt, die Datenbank aktualisieren und alle
   vorgeschlagenen Änderungen ausführen.

## Installation mit Composer

```bash
composer require neo-digital/contao-websitecheck-bundle
php vendor/bin/contao-console contao:migrate
```

`contao:migrate` legt die Tabellen `tl_neo_websitecheck` (die Checks),
`tl_neo_websitecheck_settings` und `tl_neo_websitecheck_config` an und die
Felder der Erweiterung unter *Zugänge und Lizenzen*. Composer kopiert Skript,
Stylesheet und Symbole nach `public/bundles/neowebsitecheck/`; falls das nicht
geschehen ist:

```bash
php vendor/bin/contao-console assets:install
```

## Was die Installation ergänzt

- das Inhaltselement **Website-Check** (Gruppe *NEO Digital*);
- das Backend-Modul **NEO Digital › Website-Check** (die Liste der Checks), mit
  der Schaltfläche **Einstellungen** über der Liste;
- unter **Zugänge und Lizenzen**: den Abschnitt **Google PageSpeed**, die
  Nutzung der gemeinsamen KI-Verbindung und die Lizenzzeile *Website-Check*;
- die Befehle `neo:websitecheck:run`, `neo:websitecheck:demo` und
  `neo:websitecheck:translate`;
- den Ordner `var/neo-websitecheck/` für Screenshots und PDFs, angelegt beim
  ersten Check.

Weiter mit [Einrichtung](setup.md).

## Backend-Sprachen

Backend und Standardtexte (Element, E-Mail, PDF, Befunde) gibt es auf
Englisch, Deutsch, Französisch, Niederländisch, Italienisch, Polnisch,
Tschechisch, Ungarisch, Slowenisch, Kroatisch und Spanisch. Andere Sprachen
übersetzt die KI-Verbindung einmalig (siehe
[Bedienungsanleitung](usage/configurations.md#sprachen-ohne-sprachdatei)). Eine
Projektdatei `translations/neo_websitecheck.<sprache>.yaml` überschreibt
einzelne Texte. Ungarisch und Kroatisch bietet Contao als Backend-Sprache erst
an, wenn sie in `config/config.yaml` freigeschaltet sind:

```yaml
contao:
    intl:
        enabled_locales: ['+hu', '+hr']
```

## Aktualisieren

1. Das Paket mit dem Contao Manager aktualisieren oder mit
   `composer update neo-digital/contao-websitecheck-bundle`.
2. Die Datenbank aktualisieren (`contao:migrate`).
3. `CHANGELOG.md` der neuen Fassung lesen. Braucht eine Fassung neue Rechte
   oder einen Schritt nach dem Aktualisieren, steht es dort.

Schlüssel, die eine frühere Fassung in `config/config.yaml` oder in den
Einstellungen des Website-Checks hielt, übernimmt die Datenbankaktualisierung
einmal nach *Zugänge und Lizenzen*. Ein dort schon gespeicherter Wert bleibt.

Neue PHP-Sicherheitswarnungen für die Serverprüfung kommen mit Updates der
Erweiterung.

## Entfernen

1. Die Inhaltselemente **Website-Check** aus der Website entfernen.
2. Vor dem Entfernen: den Lizenzschlüssel des Website-Checks unter *Zugänge und
   Lizenzen* leeren und speichern, damit die Lizenz von der Website genommen
   wird.
3. Das Paket mit dem Contao Manager entfernen oder mit
   `composer remove neo-digital/contao-websitecheck-bundle`.
4. Die Datenbank aktualisieren. Contao bietet dann an, die drei Tabellen der
   Erweiterung zu löschen, mit allen Checks und Kontaktdaten.
5. Den Ordner `var/neo-websitecheck/` löschen (Screenshots und PDFs).
