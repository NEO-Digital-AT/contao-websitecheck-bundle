---
title: Permissions
summary: Which Contao permissions a user group needs for the list of checks, the settings, the configurations and the designer, the content element and the keys of the Website Check.
audience: operations
language: en
updated: 2026-11-12
---

# Permissions

Administrators can do everything without any further setting. For other users,
all permissions are set in the back end under **User groups** (or **Users**),
with the names Contao shows there. The extension has no switches of its own.

## Per function

| Function | Contao permission | Setting |
| --- | --- | --- |
| List of checks: open, details, **PDF report**, **Re-send report** | **Back end modules** | *NEO Digital*: **Website check** |
| | **Allowed fields**, table `tl_neo_websitecheck` | the fields the group may see in a check (list below) |
| Deleting checks | **Table permissions** (Contao 5.7 and later), `tl_neo_websitecheck` | Delete |
| Settings, configurations, designer, previews, **Test connections** | **Back end modules** | *NEO Digital*: **Website check settings**. The module has no menu entry of its own; the button **Settings** above the list appears only with this permission. |
| | **Allowed fields**, tables `tl_neo_websitecheck_settings` and `tl_neo_websitecheck_config` | the fields below |
| | **Table permissions** (Contao 5.7 and later), `tl_neo_websitecheck_settings` | Update |
| | **Table permissions** (Contao 5.7 and later), `tl_neo_websitecheck_config` | Create, Update, Delete as needed |
| **Choose image** in the designer | **Filemounts** | the folders with the images |
| **Upload** in the designer | – | uploads go to `files/neo-websitecheck/` |
| Advanced mode (Twig) and **Your copy as Twig template** | – | **administrators only**; the switch and the fields are missing for every other user |
| Placing the element | **Content elements** | **Website check** |
| | Contao's permissions for articles and content elements | as for every content element |
| Entering the PageSpeed key, the AI connection and the licence key | **Back end modules** | *NEO Digital*: **Access keys and licences** |
| | **Allowed fields**, table `tl_neo_access` | PageSpeed key `[websitecheck_psi_key]`, the fields of the AI connection, the licence row of the Website Check (see the documentation of the Basic bundle) |
| | **Table permissions** (Contao 5.7 and later), `tl_neo_access` | Update |

As with Contao's own permissions, a user gets the permissions of the user, those
of the groups, or both, depending on **Permission inheritance**.

## The fields

| Table | Fields |
| --- | --- |
| `tl_neo_websitecheck` (a check) | Domain `[domain]`, Start URL `[url]`, Status `[status]`, Overall grade `[grade]`, Started `[created]`, Finished `[finished]`, First name `[firstname]`, Last name `[lastname]`, E-mail `[email]`, One-time contact allowed `[contact]`, Newsletter requested `[newsletter]`, Result requested `[claimed]`, Summary `[summary]` |
| `tl_neo_websitecheck_settings` | Company / agency `[brand_name]`, Contact person `[owner_name]`, Phone `[phone]`, Website `[website_url]`, Contact page `[contact_page]`, Privacy policy page `[privacy_page]`, Logo `[logo]`, Logo for dark background `[logo_dark]`, Portrait photo `[portrait]`, Primary colour `[color_primary]`, Accent colour `[color_accent]`, Sender name `[from_name]`, Sender address `[from_email]`, Reply-to address `[reply_to]`, Copy to `[notify_email]`, the seven limits (`[checks_per_ip_hour]`, `[checks_per_ip]`, `[checks_total_hour]`, `[checks_total_day]`, `[cache_hours]`, `[retention_days]`, `[max_pages]`), AI model `[ai_model]` |
| `tl_neo_websitecheck_config` | Name `[title]`, Language `[language]`, Contact page `[contact_page]`, Privacy page `[privacy_page]`, Subject `[mail_subject]`, Sender name `[mail_from_name]`, Sender address `[mail_from_email]`, Reply-to address `[mail_reply_to]`, E-mail `[mail_design]`, PDF file name `[pdf_filename]`, PDF report `[pdf_design]`, Newsletter system `[nl_provider]`, Brevo list `[nl_list_id]`, Double opt-in template `[nl_template_id]`, Confirmation page `[nl_redirect_page]`, Subject of your copy `[mail_owner_subject]`, Result on the page `[fe_result]`, Ask for contact permission `[ask_contact]` and the texts of the element (`[fe_…]`) |

**Name** and **Language** of a configuration are required fields: allow them,
otherwise the configuration cannot be saved. **E-mail** `[mail_design]` and
**PDF report** `[pdf_design]` carry the designers; without them the group sees
no designer.

## Example group "Marketing: website check"

For the person who designs e-mail and PDF and follows up the leads; the keys
are entered by an administrator:

1. **Back end modules**: **Website check**, **Website check settings**.
2. **Allowed fields** → `tl_neo_websitecheck`: all fields;
   `tl_neo_websitecheck_settings`: all fields; `tl_neo_websitecheck_config`:
   all fields.
3. **Table permissions** (Contao 5.7 and later): `tl_neo_websitecheck` →
   Update, Delete; `tl_neo_websitecheck_settings` → Update;
   `tl_neo_websitecheck_config` → Create, Update, Delete.
4. **Filemounts**: the folder with logos and images.
5. Optionally **Content elements**: **Website check**.
6. Save.

Then log in with a test user, open **Settings**, open a configuration and check
that both designers show a preview.

## Several websites in one installation

The settings and the list of checks exist once for the installation; whoever
may open them sees the checks and leads of all websites. Configurations
separate the languages, not the websites. The licence is checked per website
(the address the visitor is on).
