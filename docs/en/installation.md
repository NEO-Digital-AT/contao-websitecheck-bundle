---
title: Installation
summary: Requirements, installing the Website Check with the Contao Manager or Composer, what the installation adds, back-end languages, updating and removing.
audience: operations
language: en
updated: 2026-11-12
---

# Installation

## Requirements

| What | Needed for |
| --- | --- |
| Contao 5.3 to 6.0 (`contao/core-bundle ^5.3 \|\| ^6.0`) | everything; Composer refuses Contao 4 |
| PHP 8.2 to 8.5, as far as the Contao version supports it (Contao 5.7 needs 8.3, Contao 6.0 needs 8.4), with the extensions `dom`, `gd` (with JPEG and WebP), `json`, `libxml`, `mbstring` and `simplexml` | everything; GD cuts and scales the screenshots |
| `neo-digital/contao-basic-bundle` | installed automatically; it holds the keys, the AI connection and the licence on **Access keys and licences** |
| A Google PageSpeed Insights API key | every check; without it the form on the website accepts no checks (see [Setup](setup.md#2-pagespeed-key)) |
| A working mailer of Contao (`MAILER_DSN`) | the e-mail with the PDF report |
| The Contao cron (web cron or `contao:cron`, every minute) | finishing checks that visitors left, deleting old checks |
| Optional: an OpenAI-compatible AI provider (Requesty, OpenAI, an own address such as Ollama) | the AI reading of the pages |
| Optional: Gotenberg (Chromium as a service) | PDFs rendered by Chromium instead of Dompdf |
| Optional: `neo-digital/contao-brevo-bundle` or `contao/newsletter-bundle` | the newsletter sign-up in the lead form (licence) |
| Contao 5.4 or later | the spam protection ALTCHA, which is part of Contao from 5.4 (no extension needed; Contao 6 brings ALTCHA 3, the element uses it by itself); Contao 5.3 has no ALTCHA, there the element uses a hidden field and a minimum fill time |

Dompdf (`dompdf/dompdf`) is installed with the extension.

## Installing with the Contao Manager

1. Open the Contao Manager.
2. Search for `neo-digital/contao-websitecheck-bundle`.
3. Click **Add** and then **Apply changes**.
4. When the Contao Manager asks, run the database update and apply all changes
   it proposes.

## Installing with Composer

```bash
composer require neo-digital/contao-websitecheck-bundle
php vendor/bin/contao-console contao:migrate
```

`contao:migrate` creates the tables `tl_neo_websitecheck` (the checks),
`tl_neo_websitecheck_settings` and `tl_neo_websitecheck_config`, and the
fields of the extension on *Access keys and licences*. Composer copies script,
stylesheet and icons to `public/bundles/neowebsitecheck/`; if that did not
happen, run:

```bash
php vendor/bin/contao-console assets:install
```

## What the installation adds

- the content element **Website check** (group *NEO Digital*);
- the back-end module **NEO Digital › Website check** (the list of checks),
  with the button **Settings** above the list;
- on **Access keys and licences**: the section **Google PageSpeed**, the use
  of the shared AI connection and the licence row *Website-Check*;
- the commands `neo:websitecheck:run`, `neo:websitecheck:demo` and
  `neo:websitecheck:translate`;
- the folder `var/neo-websitecheck/` for screenshots and PDFs, created at the
  first check.

Continue with [Setup](setup.md).

## Back-end languages

The back end and the default texts (element, e-mail, PDF, findings) are
available in English, German, French, Dutch, Italian, Polish, Czech,
Hungarian, Slovenian, Croatian and Spanish. Other languages are translated
once by the AI connection (see
[User manual](usage/configurations.md#languages-without-a-translation-file)).
A project file `translations/neo_websitecheck.<language>.yaml` overrides
single texts. Contao offers Hungarian and Croatian as back-end languages only
once they are enabled in `config/config.yaml`:

```yaml
contao:
    intl:
        enabled_locales: ['+hu', '+hr']
```

## Updating

1. Update the package with the Contao Manager or with
   `composer update neo-digital/contao-websitecheck-bundle`.
2. Run the database update (`contao:migrate`).
3. Read `CHANGELOG.md` of the new version. When a version needs new
   permissions or a step after the update, the changelog says so.

Keys that an earlier version kept in `config/config.yaml` or in the settings of
the Website Check are taken over to *Access keys and licences* once by the
database update. A value already stored there is kept.

New PHP security advisories for the server check come with updates of the
extension.

## Removing

1. Remove the content elements **Website check** from the website.
2. Before removing: empty the licence key of the Website Check on *Access
   keys and licences* and save, to remove the licence from the website.
3. Remove the package with the Contao Manager or with
   `composer remove neo-digital/contao-websitecheck-bundle`.
4. Run the database update. Contao then offers to delete the three tables of
   the extension, with all checks and lead data.
5. Delete the folder `var/neo-websitecheck/` (screenshots and PDFs).
