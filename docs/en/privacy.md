---
title: Privacy
summary: What the Website Check stores and for how long, which services receive which data, what the licence service receives, and what belongs in the privacy policy of the website.
audience: operations
language: en
updated: 2026-11-12
---

# Privacy

## What is stored

| What | Where | How long |
| --- | --- | --- |
| Domain and start address of the checked website, measurement data, result, grade | table `tl_neo_websitecheck` | **Retention in days** (default 90), then deleted by the daily cron |
| First name, last name, e-mail address of the visitor, time of the request | same | same |
| Newsletter choice; the one-time contact permission with its wording and time | same | same |
| A hash of the IP address that changes every day (for the limits per visitor) | same | same |
| Screenshots of the checked website and the PDF report | `var/neo-websitecheck/<id>/`, outside the web root | same |
| Images uploaded in the designer | `files/neo-websitecheck/` | until deleted in the file manager |
| Keys (PageSpeed, AI) | *Access keys and licences*, encrypted with the app secret | until changed |

A check can be deleted earlier in the list of checks (**Delete**).

## Which services receive what

| Service | Receives | When |
| --- | --- | --- |
| Google PageSpeed Insights | the addresses of the checked pages | every new check |
| The checked website | requests of the server, as a visitor (pages, `robots.txt`, `llms.txt`, sitemap, eight common `phpinfo` addresses) | every new check |
| The AI provider of the AI connection (e.g. Requesty, OpenAI) | the visible text of up to five checked pages and the phone screenshot of the start page; no data of the visitor | every new check with an AI connection |
| The mail provider of the website (`MAILER_DSN`) | the e-mail with the PDF: to the visitor (licence) and to **Copy to** with name, e-mail, domain, grade and newsletter choice | after the lead form |
| Brevo (newsletter module) | e-mail address, first and last name (attributes `VORNAME`, `NACHNAME`, `FIRSTNAME`, `LASTNAME`), list, template, confirmation page | only when the visitor ticks the newsletter box (licence) |
| Contao's newsletter module | e-mail address, channel | same |
| The licence service | the licence key, the code of the extension, the domains of the website and the versions; never data of visitors | when the key is saved and once a day |

If the licence service cannot be reached, the last confirmation stays valid
until its grace period ends.

The wordmark of the credit line in the e-mail (free edition) is embedded in the
message; showing it loads nothing from a server.

The spam protection ALTCHA is part of Contao and sends nothing to other
services.

## Privacy policy

Add the Website Check to the privacy policy of the website:

- purpose: sending the requested report (and, with the permission, one e-mail
  about the result; with the box ticked, the newsletter);
- the data of the lead form and their storage period (**Retention in days**);
- the processors: Google (PageSpeed Insights), the AI provider, the mail
  provider, Brevo if the newsletter runs through it;
- that the screenshots and measurements concern the website entered, not the
  visitor.

Set the **Privacy policy page** in the settings (or per configuration): the
lead form links it. The results are a technical assessment; the wording of the
legal area says "technical check, not legal advice".
