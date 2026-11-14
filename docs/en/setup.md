---
title: Setup
summary: Setting up the Website Check – free edition and licence, PageSpeed key, AI connection and model, the settings form, connection test, e-mail delivery, PDF engine, newsletter, server configuration in config.yaml, how the checks run and how to test them on the command line.
audience: operations
language: en
updated: 2026-11-13
---

# Setup

Do the steps in this order. Steps 2 and 3 take place on **NEO Digital ›
Access keys and licences** (page of the Basic bundle), all others in the
settings of the Website Check.

## 1. Free edition and licence

**Licence keys:** <https://store.neo-digital.at/extensions/website-check>

The extension runs without a licence key. A licence (any edition) adds what
concerns the e-mail to the visitor and the newsletter:

| | Without licence key | With a licence |
| --- | --- | --- |
| Check, live progress, result on the page, AI reading | yes | yes |
| Lead form, list of checks in the back end, PDF download | yes | yes |
| PDF report and e-mail, with designers and previews | yes | yes |
| Who gets the e-mail with the PDF | you only: the address under **Copy to**, else the sender address | the visitor; you a copy (**Copy to**) |
| Newsletter sign-up in the lead form | no | yes |
| Credit line "powered by" in the element, the PDF and the e-mail | shown | left out |
| Re-sending the report to the visitor from the list | no | yes |

**Without a licence** the visitor sees the result on the page only. The lead
form promises no e-mail (the texts "… without licence" of the configuration),
the page always shows the full result including the texts of server warnings,
and there is no newsletter box; the server signs nobody up either. The e-mail
designed for the visitor goes with the PDF to you, with a box on top that
names the visitor; a reply goes to the visitor. Settings without effect are
hidden in the back end and keep their stored values: the newsletter of a
configuration, the reply-to address of the visitor e-mail, the subject and
template of your copy, the choice short or full result, and the texts that
promise an e-mail.

**The credit line.** In the element, the shared script of the Basic bundle
draws the credit line; it must stay visible. The element brings it into view
before a check starts and before the lead form is sent, and takes no input
while the line is cut off or covered. The PDF carries it on every page as
"powered by" and the NEO wordmark, turned by 90° (reads from bottom to top) in
the right margin near the bottom right corner, on a white field with a link
(with Gotenberg as the last line of the report). The e-mail ends with the same
line, the wordmark as an image embedded in the e-mail (no image is loaded from
a server); a mail program that shows no images shows "powered by NEO Digital",
and so does the text version of the e-mail. The extension adds the credit after
rendering, so no design, template or Twig code removes it.

**Entering the licence.** On *Access keys and licences*, section **Licences**,
enter the key in the row *Website-Check* and save. The licence is decided per
website: the signed confirmation of the licence service must cover the address
the visitor is on. The browser checks the same confirmation before it leaves
out the credit line; that needs HTTPS. The status of the row says what works
without a licence.

## 2. PageSpeed key

Without a PageSpeed key the form on the website accepts no checks. The key is
free.

1. Open the [Google Cloud Console](https://console.cloud.google.com/) and
   create a project or choose one.
2. **APIs & Services › Library**: search **PageSpeed Insights API** and click
   **Enable**.
3. **APIs & Services › Credentials › Create credentials › API key**. Restrict
   the key to the PageSpeed Insights API.
4. On *Access keys and licences*, section **Google PageSpeed**, enter it in
   **PageSpeed key** and save.

The free quota is 25,000 queries per day; one check uses three to four. The key
is stored encrypted and never shown again.

## 3. AI connection and model

The AI reading is optional. Without an AI connection the check runs without
it: the area *Content & clarity* is graded by fixed rules, and the step is
left out of the progress list.

1. On *Access keys and licences*, section **AI connection**, choose the
   **Route** and enter the **Key** (see the documentation of the Basic bundle).
   The connection is shared by all extensions.
   - **Requesty**: create an account at [requesty.ai](https://www.requesty.ai/)
     and a key under *API keys*.
   - **OpenAI**: route *OpenAI (direct)*.
   - **Ollama or another local service**: route *Own address
     (OpenAI-compatible)*, e.g. `http://ollama:11434/v1`, key `ollama`.
2. In the settings of the Website Check (step 4), section **AI model and
   access keys**, choose the **AI model**. Once a key is known, the field
   lists the models of the route. Choose a model that understands images: the
   check sends the phone screenshot of the start page along.

There is no built-in model. Without a model, here or in `config.yaml`, the AI
step is skipped. Without an AI connection the field **AI model** is greyed
out: "No AI connection on "Access keys and licences" yet (key and address):
without it the check has no AI review and the model is not used."

One check sends the visible text of up to five pages and one screenshot,
about 15,000 to 25,000 input tokens; with a mid-range model that costs a few
cents.

## 4. Settings

Open **NEO Digital › Website check** and click **Settings** above the list.
There is one record of settings for the installation.

| Section | Field | Meaning |
| --- | --- | --- |
| Who sends the report | **Company / agency** | Report header, e-mail, signature. Empty: the title of the website. |
| | **Contact person** | The person who signs the report, e.g. "John Smith" |
| | **Phone** | Report and e-mail signature |
| | **Website** | e.g. `https://example.com` |
| | **Contact page** | Target of the buttons "Discuss the result" in report, e-mail and element. Without a contact page (here, in the configuration or at the element) the buttons are left out. |
| | **Privacy policy page** | Linked under the lead form, in e-mail and PDF. Without it, no link is shown: set it. |
| Design | **Logo** | PNG, JPG or SVG for the report header and the e-mail, dark on a light background |
| | **Logo for dark background** | Optional variant for the coloured e-mail header |
| | **Portrait photo** | Optional, in the box "What happens next" and the e-mail signature |
| | **Primary colour**, **Accent colour** | PDF and e-mail only (hex, e.g. `1f2328`, `0969da`). The element on the website is styled by CSS (see [Customising](customising.md)). |
| Configurations | **Edit configurations (…)** | Opens the configurations per language (see [User manual](usage/configurations.md)) |
| E-mail | **Sender name** | Empty: company name |
| | **Sender address** | Empty: `config.yaml` or the administrator e-mail of Contao |
| | **Reply-to address** | Replies to the report e-mail (licence) |
| | **Copy to** | Receives a copy of every report with name, e-mail and newsletter choice of the visitor. Without a licence the report goes only here (empty: to the sender address). |
| Limits | **Checks per visitor and hour** | 0 = `config.yaml` (default 2) |
| | **Checks per visitor and day** | 0 = `config.yaml` (default 10) |
| | **New checks in total per hour** | All visitors together; protects the AI budget. Cached results do not count. 0 = default 20. |
| | **New checks in total per day** | 0 = default 60 |
| | **Cache in hours** | The same domain in the same language again within this time gets the stored result. 0 = default 24. |
| | **Retention in days** | Checks with lead data, screenshots and PDFs are deleted afterwards. 0 = default 90. |
| | **Pages per check** | Start page plus key sub-pages, at most 8. 0 = default 5. |
| AI model and access keys | status lines | Where the PageSpeed key and the AI key come from, the model in use, a link **Open** to *Access keys and licences* |
| | **AI model** | See step 3 |

A visitor is recognised by a hash of the IP address that changes every day.

## 5. Test the connections

Next to the save buttons of the settings are **Preview PDF**, **Preview
e-mail** and **Test connections**. **Test connections** calls every service
the check needs from this server – PageSpeed, the AI route (its model list)
and, when the Brevo newsletter module is installed, Brevo – and shows the
exact answer or error, together with the errors recorded by the latest checks.
Keys are masked in every message.

The previews render the PDF and the e-mail with the latest finished check, or
with demo data when there is none.

## 6. Configurations

Create one configuration per language of the website: it holds the e-mail and
the PDF design, the texts of the element, the contact and privacy pages and
the newsletter of that language. See
[User manual](usage/configurations.md). Without a configuration, report and
e-mail use the bundled template *Standard* in the language of the check.

## 7. E-mail delivery

Reports go through the mailer of Contao (`MAILER_DSN`).

- **With a licence** the visitor receives the e-mail with the PDF attached.
  **Copy to** receives a second e-mail with the same PDF and the lead data
  (name, e-mail, domain, grade, newsletter choice) in subject and body; a Bcc
  could not carry that information.
- **Without a licence** the e-mail designed for the visitor, with the PDF,
  goes only to **Copy to** (empty: to the sender address), with a box on top
  that names the visitor.

If sending fails, the result page says so and the result stays visible; the
list of checks offers **Re-send report** (licence).

## 8. PDF engine

Set in `config/config.yaml` (technical, not editorial):

| `pdf.engine` | Effect |
| --- | --- |
| `dompdf` (default) | Pure PHP, no extra service. Default font Arimo (bundled; looks and measures like Arial; Latin, Greek, Cyrillic). DejaVu Sans, DejaVu Serif and Helvetica can be chosen in the designer under *Design › Font*; Helvetica knows Western European characters only. Dompdf converts Arimo once into `var/neo-websitecheck/fonts`. |
| `gotenberg` | Chromium as a service renders the same template, with your fonts. Set `pdf.gotenberg_url`, e.g. `http://gotenberg:3000`; start the service with `docker run --rm -p 3000:3000 gotenberg/gotenberg:8`. |
| `none` | No PDF; the e-mail still contains the grade and the measures. |

## 9. Newsletter (licence)

The lead form offers a newsletter checkbox only with a licence and only when a
newsletter system and a confirmation page are set. No key belongs in a
configuration file or the environment: the check uses the newsletter system of
the website.

| System | What the configuration needs |
| --- | --- |
| **Brevo (newsletter module)**, `neo-digital/contao-brevo-bundle` | **Brevo list**, **Double opt-in template** and **Confirmation page**, live from the Brevo account, with the API key of that module (section *Brevo* on *Access keys and licences*) |
| **Contao newsletter**, `contao/newsletter-bundle` | **Newsletter channel (Contao)** and **Confirmation page**: a page with Contao's module *Subscribe*. The visitor gets Contao's confirmation e-mail (text from that module's subscription message, else Contao's default) and is activated by its link. |

Each configuration chooses its **Newsletter system**; empty means automatic:
Brevo when the Brevo module is installed, otherwise Contao's newsletter. With
neither installed, or with `newsletter.provider: none`, there is no checkbox.
A sign-up starts only when the visitor ticks the box; the visitor becomes a
subscriber after clicking the confirmation link.

## 10. Server configuration (`config.yaml`)

Every key is optional. Values entered in the back end win; `config.yaml` is
the fallback for empty fields. Keys taken from `config.yaml` or the
environment are taken over to *Access keys and licences* once by the database
update.

```yaml
neo_websitecheck:
    pagespeed:
        api_key: '%env(NEO_WEBSITECHECK_PSI_KEY)%'   # fallback; the key belongs on "Access keys and licences"
        timeout: 55                                  # seconds, 20 to 170
    ai:
        base_url: 'https://router.requesty.ai/v1'   # fallback for the AI connection
        api_key: '%env(NEO_WEBSITECHECK_AI_KEY)%'    # fallback; empty = no AI step
        model: ''                                    # model id of the route; empty = chosen in the settings
        max_tokens: 3000
        timeout: 90
        send_screenshot: true                        # send the phone screenshot (needs a vision model)
    mail:
        from_email: 'office@example.com'             # empty = administrator e-mail of Contao
        from_name: 'Example Ltd'
        reply_to: 'office@example.com'
        notify_email: 'office@example.com'           # your copy; empty = none
    brand:
        name: 'Example Ltd'                          # empty = website title
        owner_name: 'John Smith'
        phone: ''
        website_url: 'https://example.com'
        contact_url: 'https://example.com/contact'   # fallback target of "Discuss the result"
        privacy_url: 'https://example.com/privacy'
        logo: 'files/theme/logo.png'                 # relative to the project folder
        logo_dark: ''
        portrait: 'files/theme/portrait.jpg'
        color_primary: '#1f2328'
        color_accent: '#0969da'
    limits:
        checks_per_ip_per_hour: 2
        checks_per_ip_per_day: 10
        checks_total_per_hour: 20
        checks_total_per_day: 60
        cache_hours: 24
        max_pages: 5                                 # 1 to 8
        retention_days: 90
    pdf:
        engine: dompdf                               # dompdf | gotenberg | none
        gotenberg_url: ''
    newsletter:
        provider: auto                               # auto | brevo | contao | none
        label: ''                                    # checkbox text; empty = default text
        brevo_list_id: 0                             # fallback when a configuration sets none
        brevo_template_id: 0
        brevo_redirect_url: ''
    tracking:
        data_layer: true                             # push the events into window.dataLayer
    storage_dir: '%kernel.project_dir%/var/neo-websitecheck'
    user_agent: 'Mozilla/5.0 (compatible; NeoWebsitecheck/1.0)'   # sent when the check opens the checked website
```

Secrets for the fallback go into `.env.local`, never into `config.yaml`:

```
NEO_WEBSITECHECK_PSI_KEY=…
NEO_WEBSITECHECK_AI_KEY=…
```

## 11. How the checks run

The check is a sequence of steps that can be resumed: open the start page and
find the sub-pages, measure the start page, measure the sub-pages, check
accessibility, legal pages and AI visibility, AI reading, grade and compile.
Each step is short enough for one HTTP request. The browser of the visitor asks
for the status every two seconds, and the request that finds the check idle
runs the next step. No worker process is needed; this works on shared hosting.

- The Contao cron (every minute) finishes checks that a visitor left.
- The daily cron deletes checks older than **Retention in days**.
- A real worker is possible: run `neo:websitecheck:run` from a cron job or a
  process manager.

Screenshots and PDFs are stored in `var/neo-websitecheck/<id>/`, outside the
web root; the live preview gets them through an address protected by the
token of the check.

## 12. Test on the command line

No browser, no limits:

```bash
# a new check of a website, as a test of the setup
php vendor/bin/contao-console neo:websitecheck:run --url=https://example.com --locale=en
# run or inspect one check
php vendor/bin/contao-console neo:websitecheck:run 42 --json
# advance all pending checks
php vendor/bin/contao-console neo:websitecheck:run
# a finished demo check without any API key; --send also sends its e-mails
php vendor/bin/contao-console neo:websitecheck:demo --locale=en --send --email=office@example.com
```

`--email` sets the visitor address of the demo check; with `--send` the report
e-mail goes there (and your copy to **Copy to**, if set).
