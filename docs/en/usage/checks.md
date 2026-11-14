---
title: What is checked
summary: The seven graded areas of the Website Check and their sources, the grading scale, the server and PHP check without a grade of its own, and which pages of a website are read.
audience: users
language: en
updated: 2026-11-12
---

# What is checked

## The seven areas

| Area | Source |
| --- | --- |
| **Speed** | PageSpeed Insights, phone and desktop for the start page, up to two sub-pages: LCP, CLS, TBT, INP (field data), page weight, largest image, render-blocking files, image formats, server response time |
| **Accessibility** | Lighthouse accessibility audits with the elements concerned; contrast findings are cut out of the full-page screenshot and marked in red in the report |
| **Findability on Google** | Lighthouse SEO and own checks per page: length of title and description, exactly one H1, order of headings, sitemap, canonical address, redirect from http to https, structured data |
| **Visibility in AI search** | AI crawlers (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, CCBot, Applebot-Extended) allowed in `robots.txt` (30 points), identity in structured data (Organization, LocalBusiness …, 25), offer stated in the first sentences (25), `llms.txt` (15), `llms-full.txt` (5). An `llms.txt` is a recommendation, not a requirement: a website without it reaches grade 2. |
| **Content & clarity** | The AI reads the visible text and the phone screenshot: is it clear within five seconds what is offered, for whom, where, and what the next step is? Strengths and issues in plain language. Without an AI connection: fixed rules. |
| **Legal & privacy** | Legal notice and privacy policy linked, reachable and not empty. The content of the legal pages is read: a privacy policy inside the legal notice counts, rated lower than a page of its own; linked terms without text are reported. Consent tool present; tracking and embedded content loaded before consent (from the network list of Lighthouse). Fonts from other servers are found in the network list, the HTML, inline styles and linked stylesheets (`@import`, `@font-face`) and reported with or without a consent tool. The wording says: technical check, not legal advice. |
| **Mobile** | Viewport, tap targets, font size, content width |

With an AI connection the AI also chooses which marked screenshot excerpts the
report shows, explains each in one sentence, and may add up to two sections for
topics outside the seven areas (e.g. prices, booking, trust); the PDF block
*AI sections* prints nothing when there is nothing to add.

## Grades

Each area gets 0 to 100 points and a school grade:

| Grade | Points |
| --- | --- |
| 1 | 90 or more |
| 2 | 78 or more |
| 3 | 65 or more |
| 4 | 50 or more |
| 5 | below 50 |

The overall grade is the average of the areas, but never better than the worst
area plus one. With a critical server warning (below) the overall grade is 4 at
best.

## Server and PHP (no grade of its own)

The check reads which PHP version the web server announces: in the headers
`X-Powered-By` and `Server` of every page it fetches (error pages included) and
on a publicly reachable `phpinfo()` page under eight common file names
(`/phpinfo.php`, `/info.php`, `/php_info.php`, `/phpinfo`, `/test.php`,
`/i.php`, `/php.php`, `/pi.php`). It only reads ordinary answers; nothing is
attacked or tried out. The version is compared with the PHP security advisories
the extension ships (at present PHP2Shell, CVE-2026-109618 and CVE-2026-109619,
fixed in 8.2.35, 8.3.36, 8.4.27 and 8.5.12) and with the security support of
the PHP branches. New advisories come with updates of the extension.

| Result | Report | Grade |
| --- | --- | --- |
| Affected version (the number of PHP itself, or a branch without fixes such as 7.4 or 8.1) | Critical warning before everything else, first measure | Overall grade 4 at best |
| Version of a Linux distribution (package suffix, or a version long-term distributions keep and patch, e.g. 8.3.6 of Ubuntu 24.04) | Warning "have the host confirm the update" | unchanged |
| Branch without security updates, no known vulnerability | Warning | unchanged |
| Public `phpinfo()` page | Warning with the path, measure "delete the file" | unchanged |
| Current version | Result line "up to date" | unchanged |
| Version hidden, or no PHP found | Result line "cannot be checked" with the safe releases | unchanged; hiding the version (`expose_php = Off`) is recommended |

The result page shows one short notice before the grade ("Security
vulnerability found on the server — more in the PDF report and the e-mail");
the full warning with what to do is in the PDF and both e-mails. Designs get
the block *Server security*; a design without it shows the warnings after the
cover (PDF) or the header (e-mail) anyway.

## Which pages are read

The start page plus up to four key sub-pages found in the navigation and the
sitemap (offer, about, contact, legal pages), at most **Pages per check**
(default 5, at most 8). A website with 50 pages is represented by five, so the
check stays within about two minutes.

If PageSpeed cannot measure the start page (the website blocks Google's
crawler, needs a login or is not reachable from the internet), the step is
retried once and then skipped; the report says so.
