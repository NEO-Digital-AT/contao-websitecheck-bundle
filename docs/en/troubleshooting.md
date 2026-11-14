---
title: Troubleshooting
summary: Common problems with the Website Check – no checks accepted, PageSpeed quota, failed checks, no AI reading, no e-mail, characters missing in the PDF, spam protection, licence and credit line, missing permissions – with cause and solution.
audience: operations
language: en
updated: 2026-11-12
---

# Troubleshooting

**Test connections** in the settings calls PageSpeed, the AI route and Brevo
from the server and shows the exact answer, together with the errors of the
latest checks. Refused checks are written to the log file of the installation
(`var/logs/`), e.g. "Website check refused: no PageSpeed key …" or "Website
check refused: overall limit of new checks reached (settings → limits)."
Missing menu entries or fields are nearly always missing permissions; see
[Permissions](permissions.md).

## Checks

| What you see | Why | What to do |
| --- | --- | --- |
| Visitors get "The website check is not available right now. …" | No PageSpeed key; or the total limit of new checks per hour or day is reached | Enter the key ([Setup](setup.md#2-pagespeed-key)); raise **New checks in total per hour / per day** if the traffic is real. |
| "Quota exceeded" from PageSpeed | No or a wrong key; the public quota is shared with the whole world | Enter your own key. |
| The check fails at "Measure the start page" | The website blocks Google's crawler, needs a login or is not reachable from the internet | The step is retried once, then skipped; the report says so. Make the website reachable. |
| "This website could not be checked (not reachable or blocked)." | The address does not answer, or blocks the server of the check | Check the address in a browser. |
| A check stays at `running` or `queued` after the visitor left | The Contao cron does not run every minute | Set up the cron (`contao:cron` every minute), or run `neo:websitecheck:run`. |
| No AI step in the progress, *Content & clarity* without AI text | No AI connection, or no model chosen | Set the AI connection and choose an **AI model** ([Setup](setup.md#3-ai-connection-and-model)). |
| AI step fails with a model that does not read images | **send_screenshot** sends the phone screenshot | Choose a model that understands images, or set `ai.send_screenshot: false`. |
| A second check of the same domain shows the old result | The cache (**Cache in hours**, default 24) | Intended; set the cache to fewer hours. A check made without measurements is never reused. |
| "You have already started … checks in the last hour." | **Checks per visitor and hour** reached | Intended; raise the limit for tests, or test with `neo:websitecheck:run --url=…`. |
| The report says an AI crawler may read the website although robots.txt blocks it, or that every crawler is blocked although only one is | Before 1.0.0 the rules of the groups in robots.txt ran together: the rules of a later group (for example `User-agent: *` with `Allow: /`) also counted for the bot above it | Update the extension and run the check again; each group now has only its own rules. |

## E-mail and PDF

| What you see | Why | What to do |
| --- | --- | --- |
| No e-mail; the result page says "The e-mail could not be sent …" | Mailer not set up or refused | Check `MAILER_DSN` and `var/logs/`; then **Re-send report** in the list (licence). |
| The visitor gets no e-mail, but you do | No licence: the e-mail with the PDF goes only to **Copy to** or the sender address | Intended in the free edition; enter a licence key. |
| **Re-send report**: "The e-mail to the visitor is part of the licence. …" | No licence | Enter a licence key. |
| Umlauts or other characters missing in the PDF | A template uses a font Dompdf does not have, or **Helvetica** (Western European characters only) | Keep Arimo or DejaVu Sans, load an own `.ttf` from the files folder, or switch to Gotenberg ([Customising](customising.md#pdf-fonts-images-numbers-in-boxes)). |
| The logo is missing in the e-mail, the company name shows instead | The logo is an SVG without a PNG of the same name | Put `logo.png` next to `logo.svg`. |
| The design is not used | The configuration has Twig code from the advanced mode | An administrator switches back to **Visual** (the code is then discarded). |
| Saving a configuration fails with a Twig error | A syntax error in the advanced mode | The preview shows the line; correct it. |
| No PDF at all | `pdf.engine: none`, or Gotenberg is not reachable | Set `dompdf`, or check `pdf.gotenberg_url`. |

## Element and spam protection

| What you see | Why | What to do |
| --- | --- | --- |
| ALTCHA does not appear, or "The spam check did not go through. …" | ALTCHA needs HTTPS (or `localhost`) | Use HTTPS. On Contao 5.3 the hidden field is used automatically. |
| "Verification failed. Please reload the page." | The ALTCHA challenge expired | Reload the page. |
| The element takes no input | The credit line of the free edition is cut off or covered by the design | Make the line visible; it is part of the free licence. |
| The credit line shows although a licence exists | The website runs without HTTPS, or the licence does not cover the address the visitor is on | Use HTTPS; check the domains of the licence on *Access keys and licences*. |
| No button "Discuss the result" | No contact page in the element, the configuration or the settings | Set one. |
| No link to the privacy policy | No privacy page in the configuration or the settings | Set one. |
| No newsletter checkbox | No licence; no newsletter system installed; no confirmation page | See [Setup](setup.md#9-newsletter-licence). |
| The element looks unstyled or wrong after a template override | The own template lacks `data-nwc-*` attributes | Compare it with the template of the extension. |

## Back end

| What you see | Why | What to do |
| --- | --- | --- |
| No button **Settings** above the list | The user lacks **Website check settings** | Grant it ([Permissions](permissions.md)). |
| **AI model** is greyed out | No AI connection on *Access keys and licences* | Set the AI connection. |
| **AI model** is a text field, not a list | No AI key known yet, or the route gave no models | Enter the key; check with **Test connections**. |
| Reply-to, newsletter or "Result on the page" missing in a configuration | No licence: these settings have no effect and are hidden; their values stay | Intended. |
| No switch **Advanced (Twig)** | Only administrators see it | Intended. |
