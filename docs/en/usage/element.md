---
title: The element on the page
summary: Placing the content element Website check, its fields, what visitors see from the address field through the progress and the lead form to the result, and the element on websites in several languages.
audience: users
language: en
updated: 2026-11-12
---

# The element on the page

## Placing the element

1. Open **Content › Articles**, the article of the page (a landing page, a
   page about web design or an own page such as `/website-check`).
2. Create a content element of the type **Website check** (group *NEO
   Digital*).
3. Fill in the fields and save.

| Section | Field | Meaning |
| --- | --- | --- |
| Element type | **Headline** | Heading above the check |
| Text/HTML/Code | **Text** | Introduction above the address field: what is checked, how long it takes |
| Configuration | **Configuration** | Which configuration (texts, e-mail, PDF) the element uses. **Automatic (by page language)** (preset): the configuration whose language matches the page. Choose one explicitly for a second configuration of the same language, e.g. for a campaign. |
| Hyperlink settings | **Link target** | Target of the button "Discuss the result". Empty: the contact page of the configuration, else of the settings. Without any contact page the button is left out. |

The other sections (template, protection, expert settings, visibility) are
Contao's own fields of every content element.

## What visitors see

![The element on a page before a check, in the free edition. Frame 1 marks
the label "Address of your website" with the address field and the button
"Check now", frame 2 the browser frame where the checked website appears
during the check, frame 3 the credit line "powered by" with the NEO wordmark
below the topics.](../bilder/element-front.png)

The numbers in the picture: 1 the address field with **Check now**, 2 the area
for the screenshot of the checked website, 3 the credit line of the free
edition (left out with a licence).

1. **Address field** with the button **Check now**. The spam protection runs
   unseen: ALTCHA from Contao 5.4, a hidden field and a minimum fill time on
   Contao 5.3.
2. **Live progress**: six steps with ticks ("Open start page, find sitemap and
   key sub-pages", "Measure the start page on a phone (Google PageSpeed)",
   "Measure the sub-pages", "Check accessibility, legal pages and AI
   visibility", "Read the texts and form an opinion", "Grade and compile the
   report"), the remaining seconds and a screenshot of the checked website
   with markers on the findings. A check takes 60 to 120 seconds.
3. **Lead form**: first name, last name and e-mail address (required); the
   optional box "You may contact me once by e-mail about this result" (wording
   and time are stored with the check); with a licence the optional newsletter
   checkbox. The link to the privacy page is appended to the privacy note. The
   wording is plain: without these data there is no result.
4. **Result on the page**: with **Short (recommended)** the grade, one sentence
   and the hint to the PDF; with **Full** also the four Lighthouse scores, the
   seven areas with grades, the three most important measures with effort, what
   already works and the button "Discuss the result, free of charge". Without
   a licence the page always shows the full result.
5. **E-mail with the PDF report**: with a licence to the visitor, without one to
   the website operator (see [Setup](../setup.md#7-e-mail-delivery)).

When a server warning was found (e.g. a PHP version with a known
vulnerability), the result page shows a short notice before the grade; the full
warning with what to do is in the PDF and in the e-mails.

A second visitor who enters the same domain in the same language within the
cache time (default 24 hours) gets the stored result at once and still has to
fill in the lead form.

### Messages to visitors

| Message (default) | When |
| --- | --- |
| "Please enter a valid address, e.g. your-company.com." | The address cannot be read |
| "You have already started … checks in the last hour. Please try again in an hour." | **Checks per visitor and hour** reached |
| "You have already run … checks today. Please try again tomorrow." | **Checks per visitor and day** reached |
| "The spam check did not go through. Please reload the page and try again." | ALTCHA or the hidden field failed |
| "The website check is not available right now. Please try again later." | No PageSpeed key stored, or the total limit of new checks per hour or day reached |
| "This website could not be checked (not reachable or blocked)." | The checked website does not answer or blocks the check |
| "The e-mail could not be sent — the result stays visible here." | The mailer failed |

## Websites in several languages

One root page per language, as Contao does it:

1. Create **one configuration per language** (see
   [Configurations and designer](configurations.md)), with the contact and
   privacy pages and the newsletter of that language.
2. Place the element on the pages of each language and leave **Configuration**
   on *Automatic*, or choose the configuration.
3. Everything follows the page language: the texts on the page, the check, the
   AI reading (written in the page language), e-mail, PDF and newsletter. A page
   whose language has no configuration gets the bundled template in its own
   language, never the configuration of another language.

The extension never picks a page of the website by itself: without a contact
page the button is left out, without a confirmation page there is no
newsletter checkbox, without a privacy page no link.
