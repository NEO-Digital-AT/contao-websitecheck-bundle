---
title: Customising
summary: Adapting the Website Check to the website – CSS variables of the element, overriding and extending templates, fonts, images and logos in PDF and e-mail, the analytics events and the services for developers.
audience: operations
language: en
updated: 2026-11-12
---

# Customising

Element, PDF and e-mail come in a neutral design. The element on the page takes
no colours from the back end: it is styled only with CSS variables in the
stylesheet of the website. PDF and e-mail take their colours from **Primary
colour** and **Accent colour** of the settings and are designed in the
designer ([User manual](usage/configurations.md#the-designer)).

## CSS variables of the element

All variables sit on `.nwc`. Override them in a stylesheet of the website, for
example a dark design:

```css
.content-neo-websitecheck .nwc {
    --nwc-bg: #102a43;
    --nwc-bg-2: #163a5c;
    --nwc-fg: #ffffff;
    --nwc-muted: #c9d6e3;
    --nwc-accent: #ffb000;
    --nwc-accent-ink: #102a43;
    --nwc-field-bg: rgb(0 0 0 / .3);
    --nwc-field-border: rgb(255 255 255 / .45);
    --nwc-radius: 22px;
}
```

| Variable | Default | Use |
| --- | --- | --- |
| `--nwc-bg`, `--nwc-bg-2` | `#ffffff`, `#ffffff` | panel, as a gradient from `bg-2` to `bg` |
| `--nwc-fg`, `--nwc-muted` | `#1f2328`, `#59636e` | text, secondary text (at least 4.5:1 on the panel) |
| `--nwc-card`, `--nwc-border`, `--nwc-faint` | `#f6f8fa`, `#d1d9e0`, `rgba(31,35,40,.12)` | cards, borders, faint lines |
| `--nwc-accent`, `--nwc-accent-ink` | `#1f2328`, `#ffffff` | buttons, progress, focus; text on the accent |
| `--nwc-ring`, `--nwc-glow` | `rgba(9,105,218,.3)`, `rgba(31,35,40,.25)` | focus ring, glow of the progress |
| `--nwc-field-bg`, `--nwc-field-border`, `--nwc-placeholder` | `#ffffff`, `#818b98`, `#6e7781` | input fields (border at least 3:1) |
| `--nwc-err` | `#cf222e` | error text and border of an invalid field |
| `--nwc-good`, `--nwc-warn`, `--nwc-bad`, `--nwc-grade-ink` | `#4ac26b`, `#d4a72c`, `#ff8182`, `#1f2328` | grade colours, text on the grade |
| `--nwc-alert-bg`, `--nwc-alert-line`, `--nwc-alert-ink`, `--nwc-alert-text`, `--nwc-alert-tag-ink` | `#fef3f2`, `#b42318`, `#7a271a`, `#1f2328`, `#ffffff` | critical server warning |
| `--nwc-notice-bg`, `--nwc-notice-line`, `--nwc-notice-ink` | `#fffaeb`, `#b54708`, `#7a2e0e` | other server warnings |
| `--nwc-sent-bg`, `--nwc-sent-border`, `--nwc-failed-bg`, `--nwc-failed-border` | `#dafbe1`, `#4ac26b`, `#ffebe9`, `#ff8182` | e-mail sent / not sent |
| `--nwc-os-chrome`, `--nwc-os-ink`, `--nwc-os-shadow` | `#24292f`, `#d1d9e0`, shadow | browser frame of the preview |
| `--nwc-skel`, `--nwc-skel-hue`, `--nwc-idle` | `#e6e9ed`, `#c9d1d9`, `rgba(31,35,40,.66)` | placeholder page and overlay before the check |
| `--nwc-check-icon` | white tick as SVG | tick of finished steps (colour of `accent-ink`) |
| `--nwc-shadow` | `0 1px 3px rgba(31,35,40,.08)` | shadow of the panel |
| `--nwc-radius`, `--nwc-radius-s` | `16px`, `10px` | corners |
| `--nwc-font-display`, `--nwc-font-mono` | `inherit`, `ui-monospace, …` | headings, numbers |

Keep text at 4.5:1 and field borders at 3:1 against the panel. The credit line
of the free edition takes its colours from `--nwc-bg`, `--nwc-muted` and
`--nwc-border`; unreadable values fall back to fixed ones.

## Templates

Copy a file from `contao/templates/` of the extension into `templates/` of the
website, keeping the path, and change it there.

| Template | What it renders |
| --- | --- |
| `content_element/neo_websitecheck.html.twig` | The element; keep the `data-nwc-*` attributes the script uses |
| `neo_websitecheck/blocks/pdf/<block>.html.twig`, `neo_websitecheck/blocks/mail/<block>.html.twig` | One block of the designer, e.g. `blocks/pdf/cover.html.twig` |
| `neo_websitecheck/pdf_frame.html.twig`, `neo_websitecheck/mail_frame.html.twig` | The frame of PDF and e-mail |
| `neo_websitecheck/mail_owner.html.twig` | Your copy of the e-mail |

Templates receive `result`, `areas`, `brand`, `images`, `crops`, `date` and
`vars`; blocks also `block` (its properties) and `t` (the design). Without a
saved design, report and e-mail use the bundled template *Standard* in the
language of the check.

To change only parts of the element, extend it and override its blocks
`nwc_intro`, `nwc_submit_button`, `nwc_gate_button` and `nwc_cta_buttons`:

```twig
{% extends "@Contao_NeoWebsitecheckBundle/content_element/neo_websitecheck.html.twig" %}

{% block nwc_submit_button %}
    <button type="submit" class="nwc__btn nwc__btn--primary"><span>Check my website</span></button>
{% endblock %}
```

Keep `type="submit"` on the buttons and `data-nwc-reset` on the reset button.

## PDF: fonts, images, numbers in boxes

The report templates are written in HTML that Dompdf understands (tables, no
flexbox).

- **Own fonts and images**: a template may load TrueType fonts and images from
  the files folder of Contao with a relative path, e.g.
  `@font-face { font-family: 'Brand'; src: url('files/fonts/Brand-Regular.ttf'); }`
  or `<img src="files/images/badge.png">`. Use static `.ttf` files (no WOFF2,
  no variable fonts) and register normal and bold separately.
- **Numbers in coloured boxes**: use the macro `badge()` from
  `_pdf_macros.html.twig`; Dompdf centres text vertically only with that
  structure.
- **Gotenberg** renders the same templates with Chromium (see
  [Setup](setup.md#8-pdf-engine)).

## Logos in e-mails

Mail programs show no SVG. Put a PNG with the same name next to an SVG logo
(`logo.svg` → `logo.png`, about 600 px wide): e-mails use the PNG, the PDF
keeps the SVG. Without a PNG, the e-mail shows the company name instead of the
logo.

## Texts

The words of e-mail and PDF are edited in the designer. The default words, the
texts of the findings and of the back end are in
`translations/neo_websitecheck.<language>.yaml` of the extension; a file of the
same name in `translations/` of the project overrides single keys (e.g. to
deliver the findings in other words). The checkbox text of the newsletter can
also be set with `newsletter.label` in `config.yaml` or the key
`fe.newsletter`.

## Analytics events

The script dispatches the DOM event `neo_websitecheck` on `document`
(`event.detail = {name, params, element}`). With `tracking.data_layer: true`
(default) it also pushes `{event: name, …params}` into `window.dataLayer`.

| Event | When | Parameters |
| --- | --- | --- |
| `check_start` | address submitted | `domain`, `cached` |
| `check_gate` | result ready, lead form shown | `domain` |
| `check_abandon` | page left while the lead form was open | `domain` |
| `check_lead` | lead form submitted | `domain`, `newsletter` (0/1), `contact` (0/1) |
| `check_result` | result shown | `domain`, `grade` |

Example without Google Tag Manager, sending to Google Analytics only after
consent:

```js
document.addEventListener('neo_websitecheck', function (e) {
    if (window.cookieConsent && window.cookieConsent.allows('statistics') && typeof gtag === 'function') {
        gtag('event', e.detail.name, e.detail.params);
    }
});
```

Replace `window.cookieConsent.allows(…)` with the function of the consent tool
of the website.

## For developers

The service `Neo\WebsitecheckBundle\Check\Grader` holds the scoring,
`Neo\WebsitecheckBundle\Check\CheckRunner` the steps of a check. Decorate or
replace them as usual in Symfony.
