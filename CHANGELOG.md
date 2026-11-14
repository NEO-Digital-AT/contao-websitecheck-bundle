# Changelog

All notable changes to this extension are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow
[Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-11-13

Initial release. Runs on Contao 5.3 to 6.0 with PHP 8.2 to 8.5, as far as the Contao version supports it (Contao 5.7 needs PHP 8.3, Contao 6.0 PHP 8.4).

### Added
- Content element **Website check**: a visitor enters the address of a website; the extension measures it with Google PageSpeed Insights, runs its own checks of HTML, legal pages, AI visibility and the server, optionally lets an AI read the pages like a first-time visitor, and grades seven areas with school grades: speed, accessibility, findability on Google, visibility in AI search, content and clarity, legal and privacy, mobile; plus a server check of the PHP version against known security advisories.
- Live progress in six steps with a screenshot of the checked website and markers on the findings, 60 to 120 seconds per check; no worker needed (the steps run in the visitor's requests and the Contao cron).
- Result on the page in exchange for first name, last name and e-mail address; PDF report by e-mail; optional one-time contact permission and, with a licence, newsletter sign-up via the Brevo module or Contao's newsletter.
- Visual designer for e-mail and PDF report with templates, previews and an advanced mode in Twig for administrators; every word editable; colours and logo from the settings.
- One configuration per language; texts in eleven languages, other languages translated once by the AI connection.
- Limits per visitor and in total, cache per domain, spam protection with ALTCHA (Contao 5.4 and later), retention period for all data; back-end list of the checks with grades and lead data.
- Free without a licence key, with a credit line in the element, the PDF and the e-mail; neutral design through CSS variables; back end in eleven languages; documentation in English and German; unit tests.

After installing: update the database, enter the PageSpeed key under **NEO Digital › Access keys and licences** and add the content element **Website check** to a page.
