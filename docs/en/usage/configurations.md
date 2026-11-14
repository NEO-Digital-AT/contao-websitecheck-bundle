---
title: Configurations and designer
summary: One configuration per language of the Website Check – e-mail, PDF report, pages, newsletter, texts of the element – and the visual designer for e-mail and PDF with templates, wording, previews and the advanced mode; languages without a translation file.
audience: users
language: en
updated: 2026-11-12
---

# Configurations and designer

A configuration holds everything a visitor reads in one language: the e-mail,
the PDF report, the texts of the element, the contact and privacy pages and the
newsletter. Create one per language of the website, or a second one of the same
language for a campaign. The check remembers its configuration, so e-mail and
PDF match the page the visitor used.

## Creating a configuration

1. Open **NEO Digital › Website check**, click **Settings** and then **Edit
   configurations (…)**.
2. Click **New configuration**.
3. Enter **Name** (internal, e.g. "English" or "English – hotels") and choose
   the **Language**. The list offers every language Contao knows.
4. Save. On the first save, the extension fills empty designs with the
   template *Standard* in the language of the configuration; the PDF file name
   is preset to `website-check-{{domain}}-{{date}}.pdf`.

The list shows each configuration as "Name [LANGUAGE]". **Duplicate** copies a
configuration, e.g. as the start of another language.

## The fields

| Section | Field | Meaning |
| --- | --- | --- |
| Pages of this language | **Contact page** | Target of "Discuss the result" in form, e-mail and PDF. Empty: the contact page of the settings. The element can override it with its **Link target**. |
| | **Privacy page** | Link in the lead form, the e-mail and the PDF. Empty: the privacy page of the settings. Without a page, no link is shown. |
| E-mail to the visitor | **Subject** | Placeholders allowed. Default: "Your website check for {{domain}}: grade {{grade}} · {{grade_word}}" |
| | **Sender name**, **Sender address** | Empty: the settings |
| | **Reply-to address** (licence) | Empty: the settings |
| Design the e-mail | **E-mail** | The e-mail designer (below) |
| PDF report | **PDF file name** | With placeholders, e.g. `website-check-{{domain}}-{{date}}.pdf`; `{{grade}}`, `{{firstname}}`, `{{lastname}}`, `{{company}}` work too |
| Design the PDF report | **PDF report** | The PDF designer (below) |
| Newsletter of this language (licence) | **Newsletter system** | **Automatic (Brevo when installed)**, **Brevo (newsletter module)** or **Contao newsletter**. Save to see the matching fields. |
| | **Brevo list**, **Double opt-in template**, **Confirmation page** | With Brevo. Empty list or template: the fallback of `config.yaml`. Without a confirmation page there is no newsletter sign-up. |
| | **Newsletter channel (Contao)**, **Confirmation page** | With Contao's newsletter: the confirmation page carries Contao's module *Subscribe*; the e-mail text comes from that module. |
| Your copy (licence) | **Subject of your copy** | Default: "[Website check] {{domain}} · grade {{grade}} · {{firstname}}" |
| The element on the page | **Result on the page** (licence) | **Short (recommended)**: grade, one sentence, hint to the PDF. **Full**: also scores, all areas, measures and strengths. The PDF always has everything. |
| | **Ask for contact permission** | Shows the optional box "You may contact me once by e-mail about this result" |
| | texts of the element | Label above the address field, "Check now" button, contact form title, text, privacy note and button, text of the "contact me once" box, newsletter checkbox text, result note, result button; and the variants "… without licence" that promise no e-mail. Empty: the default of the language. |
| Internal copy as Twig template (administrators, licence) | **Your copy as Twig template** | Empty: the bundled template |

Without a licence, the fields marked "licence" are hidden; their values stay.

## The designer

The designer is the same for e-mail and PDF. It has three panes:

- **Left**: **Blocks** to click or drag in, **Structure** to reorder by drag
  and drop, **Design** for colours, font, corner radius, margins, header and
  footer, and **Wording**.
- **Centre**: **Preview** with the latest finished check or demo data. Click a
  block in the preview to edit it.
- **Right**: **Properties** of the selected block: texts with placeholders,
  formatted text (bold, italic, link, list), images from the file manager or
  uploaded with **Upload**, alignment, spacing, background, what to show.

![The PDF designer with the standard template; the block "Measures" is
selected. Frame 1 marks the left pane with the tabs Blocks, Structure and
Design, frame 2 the preview in the centre, frame 3 the properties of the
selected block on the right.](../bilder/designer.png)

The numbers in the picture: 1 the left pane (here **Structure**), 2 the
**Preview**, 3 the **Properties** of the selected block.

| Blocks of the PDF | Blocks of the e-mail |
| --- | --- |
| Cover page, Heading, Text, Image, Note, Next steps, Signature, Overall grade, Key figures, Screenshots, Google rings, Areas at a glance, Measures, What already works, Area in detail, Speed metrics, AI sections, Table of pages, Server security, Sources, Spacer, Divider, Page break | Header, Heading, Text, Image, Overall grade, Areas at a glance, Measures, What already works, Server security, Button, PDF note, Newsletter note, Signature, Spacer, Divider, Footer |

**Placeholders** (button **Insert placeholder**): `{{firstname}}`,
`{{lastname}}`, `{{domain}}`, `{{grade}}`, `{{grade_word}}`, `{{pages}}`,
`{{company}}`, `{{owner}}`, `{{phone}}`, `{{website}}`, `{{days}}`,
`{{date}}`.

**Every word is yours.** Apart from what the check finds (the AI reading,
findings, area summaries, measures and measured values), everything in e-mail
and PDF comes from the design: headings, labels, table columns, legend, grade
words, area names, effort labels, names of the measured values, header, footer
and the word before the page number. Each block carries its words as
properties; words used by several blocks are under **Design › Wording**.

### Templates

**Load template** replaces the design with a ready-made one in the language of
the configuration (after a confirmation):

| PDF | E-mail |
| --- | --- |
| Standard (cover with grade, all areas) | Standard (grade, measures, PDF note, button) |
| With cover page (cover in primary colour, result on page 2) | Short (grade, PDF note, button) |
| Compact (one page) | |

**PDF preview** opens the real PDF of the current draft; for the e-mail,
**Open in new tab** shows it in the browser. The buttons **PDF preview** and
**E-mail preview** next to the save buttons do the same for the saved
configuration.

### Advanced mode (administrators)

The switch **Visual | Advanced (Twig)** above the designer shows the complete
document as Twig code: the frame with its CSS, the HTML of every block and
every word. Changes show in the preview; syntax errors are shown there with the
line, and saving is refused. As long as code is stored, it is used instead of
the visual design ("This configuration has Twig code from the advanced mode.
It takes precedence, so this design is not used."). Switching back to
**Visual** discards the code after a confirmation. Other users see neither the
switch nor the code fields.

## Languages without a translation file

Translation files ship for eleven languages. For every other language, the AI
connection translates the visitor-facing texts (findings, measures, form,
report, e-mail) once: when a configuration in that language is saved, when a
page in that language first shows the element (after the page was delivered),
or at the latest before the first check is graded. It takes a few seconds and
costs about a cent. Without an AI connection the default language of the
installation is used.

The command `php vendor/bin/contao-console neo:websitecheck:translate <lang>
--export` writes the texts to `translations/neo_websitecheck.<lang>.yaml` for
review; a project file always wins over the AI texts. `--force` translates all
texts again. Dates and numbers follow the format of the language.
