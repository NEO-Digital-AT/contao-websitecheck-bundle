---
title: The list of checks
summary: Following up the checks of the Website Check in the back end – the list with grade, status and lead data, the details, the PDF report, re-sending the report and deleting.
audience: users
language: en
updated: 2026-11-12
---

# The list of checks

**NEO Digital › Website check** lists every check, newest first, with domain,
grade, status, first name, last name, e-mail and start time. The filter selects
by start date, status, grade, contact permission and newsletter; the search
finds a domain, a first or last name or an e-mail address.

![The list of checks with three demo checks. Frame 1 marks the panel with
search and filter on the right, frame 2 the operations of a check (edit, PDF
report, delete, details), frame 3 the button "Settings" above the
list.](../bilder/checks-list.png)

The numbers in the picture: 1 search and filter, 2 the operations of a check
(table below), 3 **Settings**, which opens the settings of the Website Check.

| Status | Meaning |
| --- | --- |
| `queued` | Waiting for the next step |
| `running` | A step is running |
| `done` | Finished |
| `failed` | Stopped; the website could not be checked |

The overall grade is 1 (very good) to 5 (insufficient); 0 means not finished.

## Operations per check

| Operation | What it does |
| --- | --- |
| **Edit** | Opens the check: domain, start address, status, grade, times; first name, last name, e-mail, **One-time contact allowed**, **Newsletter requested**, **Result requested** (when the visitor entered the data); the summary with the grades per area and the top measures |
| **PDF report** | Opens the PDF report of the check |
| **Re-send report** | Sends the report e-mail to the visitor again, after a confirmation ("Re-send the report to …?"). Needs a licence: without one, "The e-mail to the visitor is part of the licence. Without a licence it is not sent." |
| **Delete** | Deletes the check with its lead data, screenshots and PDF |
| **Details** | Shows all stored values |

Checks are created only by visitors (or the commands); the back end cannot
create one. The daily cron deletes every check older than **Retention in days**
(default 90).

## Following up a lead

1. Open the check with **Edit**.
2. Check **One-time contact allowed**: only then may you write to the visitor
   once about this result.
3. **Newsletter requested** says that the visitor ticked the box; the
   subscription itself is confirmed in the newsletter system.
4. **PDF report** shows what the visitor received.
