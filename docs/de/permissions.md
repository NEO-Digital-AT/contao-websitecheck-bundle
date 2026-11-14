---
titel: Rechte
kurzfassung: Welche Contao-Rechte eine Benutzergruppe für die Liste der Checks, die Einstellungen, die Konfigurationen und den Designer, das Inhaltselement und die Schlüssel des Website-Checks braucht.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Rechte

Administratoren können alles ohne weitere Einstellung. Für andere Benutzer
werden alle Rechte im Backend unter **Benutzergruppen** (oder **Benutzer**)
gesetzt, mit den Bezeichnungen, die Contao dort zeigt. Die Erweiterung hat
keine eigenen Schalter.

## Je Funktion

| Funktion | Contao-Recht | Einstellung |
| --- | --- | --- |
| Liste der Checks: öffnen, Details, **PDF-Bericht**, **Bericht erneut senden** | **Backend-Module** | *NEO Digital*: **Website-Check** |
| | **Erlaubte Felder**, Tabelle `tl_neo_websitecheck` | die Felder, die die Gruppe in einem Check sehen soll (Liste unten) |
| Checks löschen | **Tabellenrechte** (ab Contao 5.7), `tl_neo_websitecheck` | Löschen |
| Einstellungen, Konfigurationen, Designer, Vorschau, **Verbindung prüfen** | **Backend-Module** | *NEO Digital*: **Website-Check Einstellungen**. Das Modul hat keinen eigenen Menüeintrag; die Schaltfläche **Einstellungen** über der Liste erscheint nur mit diesem Recht. |
| | **Erlaubte Felder**, Tabellen `tl_neo_websitecheck_settings` und `tl_neo_websitecheck_config` | die Felder unten |
| | **Tabellenrechte** (ab Contao 5.7), `tl_neo_websitecheck_settings` | Bearbeiten |
| | **Tabellenrechte** (ab Contao 5.7), `tl_neo_websitecheck_config` | Anlegen, Bearbeiten, Löschen nach Bedarf |
| **Bild wählen** im Designer | **Filemounts** | die Ordner mit den Bildern |
| **Hochladen** im Designer | – | Hochgeladenes kommt nach `files/neo-websitecheck/` |
| Erweiterter Modus (Twig) und **Ihre Kopie als Twig-Vorlage** | – | **nur Administratoren**; allen anderen fehlen Schalter und Felder |
| Element einbinden | **Inhaltselemente** | **Website-Check** |
| | Contaos Rechte für Artikel und Inhaltselemente | wie bei jedem Inhaltselement |
| PageSpeed-Schlüssel, KI-Verbindung und Lizenzschlüssel eintragen | **Backend-Module** | *NEO Digital*: **Zugänge und Lizenzen** |
| | **Erlaubte Felder**, Tabelle `tl_neo_access` | PageSpeed-Schlüssel `[websitecheck_psi_key]`, die Felder der KI-Verbindung, die Lizenzzeile des Website-Checks (siehe Dokumentation des Basic-Bundles) |
| | **Tabellenrechte** (ab Contao 5.7), `tl_neo_access` | Bearbeiten |

Wie bei Contaos eigenen Rechten bekommt ein Benutzer die Rechte des Benutzers,
die der Gruppen oder beide, je nach **Rechtevererbung**.

## Die Felder

| Tabelle | Felder |
| --- | --- |
| `tl_neo_websitecheck` (ein Check) | Domain `[domain]`, Start-URL `[url]`, Status `[status]`, Gesamtnote `[grade]`, Gestartet `[created]`, Fertig `[finished]`, Vorname `[firstname]`, Nachname `[lastname]`, E-Mail `[email]`, Einmaliger Kontakt erlaubt `[contact]`, Newsletter gewünscht `[newsletter]`, Ergebnis angefordert `[claimed]`, Zusammenfassung `[summary]` |
| `tl_neo_websitecheck_settings` | Firma / Agentur `[brand_name]`, Ansprechperson `[owner_name]`, Telefon `[phone]`, Website `[website_url]`, Kontaktseite `[contact_page]`, Datenschutzseite `[privacy_page]`, Logo `[logo]`, Logo für dunklen Hintergrund `[logo_dark]`, Porträtfoto `[portrait]`, Hauptfarbe `[color_primary]`, Akzentfarbe `[color_accent]`, Absendername `[from_name]`, Absenderadresse `[from_email]`, Antwortadresse `[reply_to]`, Kopie an `[notify_email]`, die sieben Grenzen (`[checks_per_ip_hour]`, `[checks_per_ip]`, `[checks_total_hour]`, `[checks_total_day]`, `[cache_hours]`, `[retention_days]`, `[max_pages]`), KI-Modell `[ai_model]` |
| `tl_neo_websitecheck_config` | Bezeichnung `[title]`, Sprache `[language]`, Kontaktseite `[contact_page]`, Datenschutzseite `[privacy_page]`, Betreff `[mail_subject]`, Absendername `[mail_from_name]`, Absenderadresse `[mail_from_email]`, Antwortadresse `[mail_reply_to]`, E-Mail `[mail_design]`, PDF-Dateiname `[pdf_filename]`, PDF-Bericht `[pdf_design]`, Newsletter-System `[nl_provider]`, Brevo-Verteiler `[nl_list_id]`, Double-Opt-in-Vorlage `[nl_template_id]`, Bestätigungsseite `[nl_redirect_page]`, Betreff Ihrer Kopie `[mail_owner_subject]`, Ergebnis auf der Seite `[fe_result]`, Kontakterlaubnis abfragen `[ask_contact]` und die Texte des Elements (`[fe_…]`) |

**Bezeichnung** und **Sprache** einer Konfiguration sind Pflichtfelder: sie
erlauben, sonst lässt sich die Konfiguration nicht speichern. **E-Mail**
`[mail_design]` und **PDF-Bericht** `[pdf_design]` tragen die Designer; ohne sie
sieht die Gruppe keinen Designer.

## Beispielgruppe „Marketing: Website-Check“

Für die Person, die E-Mail und PDF gestaltet und die Kontakte nachverfolgt; die
Schlüssel trägt ein Administrator ein:

1. **Backend-Module**: **Website-Check**, **Website-Check Einstellungen**.
2. **Erlaubte Felder** → `tl_neo_websitecheck`: alle Felder;
   `tl_neo_websitecheck_settings`: alle Felder; `tl_neo_websitecheck_config`:
   alle Felder.
3. **Tabellenrechte** (ab Contao 5.7): `tl_neo_websitecheck` → Bearbeiten,
   Löschen; `tl_neo_websitecheck_settings` → Bearbeiten;
   `tl_neo_websitecheck_config` → Anlegen, Bearbeiten, Löschen.
4. **Filemounts**: der Ordner mit Logos und Bildern.
5. Auf Wunsch **Inhaltselemente**: **Website-Check**.
6. Speichern.

Dann mit einem Testbenutzer anmelden, **Einstellungen** öffnen, eine
Konfiguration öffnen und prüfen, dass beide Designer eine Vorschau zeigen.

## Mehrere Websites in einer Installation

Einstellungen und Liste der Checks gibt es einmal für die Installation; wer sie
öffnen darf, sieht die Checks und Kontakte aller Websites. Konfigurationen
trennen die Sprachen, nicht die Websites. Die Lizenz wird je Website geprüft
(die Adresse, auf der der Besucher ist).
