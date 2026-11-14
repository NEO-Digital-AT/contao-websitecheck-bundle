---
titel: Einrichtung
kurzfassung: Den Website-Check einrichten – Gratisfassung und Lizenz, PageSpeed-Schlüssel, KI-Verbindung und Modell, das Formular der Einstellungen, Verbindungsprüfung, E-Mail-Versand, PDF-Erzeugung, Newsletter, Server-Konfiguration in config.yaml, wie die Checks laufen und wie man sie auf der Kommandozeile testet.
zielgruppe: betrieb
sprache: de
stand: 2026-11-13
---

# Einrichtung

Die Schritte in dieser Reihenfolge erledigen. Die Schritte 2 und 3 laufen unter
**NEO Digital › Zugänge und Lizenzen** (Seite des Basic-Bundles), alle anderen
in den Einstellungen des Website-Checks.

## 1. Gratisfassung und Lizenz

**Lizenzschlüssel:** <https://store.neo-digital.at/extensions/website-check>

Die Erweiterung läuft ohne Lizenzschlüssel. Eine Lizenz (jede Stufe) ergänzt,
was die E-Mail an den Besucher und den Newsletter betrifft:

| | Ohne Lizenzschlüssel | Mit Lizenz |
| --- | --- | --- |
| Check, Live-Fortschritt, Ergebnis auf der Seite, Lesen durch die KI | ja | ja |
| Kontaktformular, Liste der Checks im Backend, PDF zum Herunterladen | ja | ja |
| PDF-Bericht und E-Mail, mit Designern und Vorschau | ja | ja |
| Wer die E-Mail mit dem PDF bekommt | nur Sie: die Adresse unter **Kopie an**, sonst die Absenderadresse | der Besucher; Sie eine Kopie (**Kopie an**) |
| Newsletter-Anmeldung im Kontaktformular | nein | ja |
| Hinweis „powered by“ in Element, PDF und E-Mail | sichtbar | entfällt |
| Bericht aus der Liste erneut an den Besucher senden | nein | ja |

**Ohne Lizenz** sieht der Besucher das Ergebnis nur auf der Seite. Das
Kontaktformular verspricht keine E-Mail (die Texte „… ohne Lizenz“ der
Konfiguration), die Seite zeigt immer das ausführliche Ergebnis samt den
Texten der Serverwarnungen, und es gibt kein Newsletter-Häkchen; der Server
meldet auch niemanden an. Die für den Besucher gestaltete E-Mail geht mit dem
PDF an Sie, mit einem Kasten oben, der den Besucher nennt; eine Antwort geht an
den Besucher. Einstellungen ohne Wirkung sind im Backend ausgeblendet und
behalten ihre gespeicherten Werte: der Newsletter einer Konfiguration, die
Antwortadresse der Besucher-E-Mail, Betreff und Vorlage Ihrer Kopie, die Wahl
Kurzfassung oder ausführlich und die Texte, die eine E-Mail versprechen.

**Der Hinweis.** Im Element zeichnet das gemeinsame Skript des Basic-Bundles
den Hinweis; er muss sichtbar bleiben. Das Element holt ihn vor dem Start eines
Checks und vor dem Absenden des Kontaktformulars ins Bild und nimmt keine
Eingabe an, solange der Hinweis abgeschnitten oder verdeckt ist. Das PDF trägt
ihn auf jeder Seite als „powered by“ mit der NEO-Wortmarke, um 90° gedreht (von
unten nach oben zu lesen) im rechten Rand nahe der Ecke rechts unten, auf einem
weißen Feld mit Link (mit Gotenberg als letzte Zeile des Berichts). Die E-Mail
endet mit derselben Zeile, die Wortmarke als in die E-Mail eingebettetes Bild
(kein Bild wird von einem Server geladen); ein E-Mail-Programm, das keine Bilder
zeigt, zeigt „powered by NEO Digital“, ebenso die Textfassung der E-Mail. Die
Erweiterung setzt den Hinweis nach dem Erzeugen ein; keine Gestaltung, keine
Vorlage und kein Twig-Code entfernt ihn.

**Lizenz eintragen.** Unter *Zugänge und Lizenzen*, Abschnitt **Lizenzen**, den
Schlüssel in der Zeile *Website-Check* eintragen und speichern. Die Lizenz gilt
je Website: Der signierte Nachweis des Lizenzdiensts muss die Adresse abdecken,
auf der der Besucher ist. Der Browser prüft denselben Nachweis, bevor er den
Hinweis weglässt; dafür braucht es HTTPS. Der Stand der Zeile sagt, was ohne
Lizenz funktioniert.

## 2. PageSpeed-Schlüssel

Ohne PageSpeed-Schlüssel nimmt das Formular auf der Website keine Prüfung an.
Der Schlüssel ist kostenlos.

1. Die [Google Cloud Console](https://console.cloud.google.com/) öffnen und ein
   Projekt anlegen oder wählen.
2. **APIs & Dienste › Bibliothek**: **PageSpeed Insights API** suchen und auf
   **Aktivieren** klicken.
3. **APIs & Dienste › Anmeldedaten › Anmeldedaten erstellen › API-Schlüssel**.
   Den Schlüssel auf die PageSpeed Insights API einschränken.
4. Unter *Zugänge und Lizenzen*, Abschnitt **Google PageSpeed**, unter
   **PageSpeed-Schlüssel** eintragen und speichern.

Das kostenlose Kontingent sind 25 000 Abfragen am Tag; ein Check braucht drei
bis vier. Der Schlüssel wird verschlüsselt gespeichert und nie wieder
angezeigt.

## 3. KI-Verbindung und Modell

Das Lesen durch die KI ist optional. Ohne KI-Verbindung läuft der Check ohne
sie: Der Bereich *Inhalte & Klarheit* wird nach festen Regeln benotet, und der
Schritt fehlt in der Fortschrittsliste.

1. Unter *Zugänge und Lizenzen*, Abschnitt **KI-Verbindung**, die **Route**
   wählen und den **Schlüssel** eintragen (siehe Dokumentation des
   Basic-Bundles). Die Verbindung teilen sich alle Erweiterungen.
   - **Requesty**: auf [requesty.ai](https://www.requesty.ai/) ein Konto und
     unter *API keys* einen Schlüssel anlegen.
   - **OpenAI**: Route *OpenAI (direkt)*.
   - **Ollama oder ein anderer lokaler Dienst**: Route *Eigene Adresse
     (OpenAI-kompatibel)*, z. B. `http://ollama:11434/v1`, Schlüssel `ollama`.
2. In den Einstellungen des Website-Checks (Schritt 4), Abschnitt **KI-Modell
   und Zugänge**, das **KI-Modell** wählen. Sobald ein Schlüssel bekannt ist,
   zeigt das Feld die Modelle der Route. Ein Modell mit Bildverständnis wählen:
   Der Check schickt den Handy-Screenshot der Startseite mit.

Ein Modell ist nicht eingebaut. Ohne Modell, hier oder in `config.yaml`, wird
der KI-Schritt übersprungen. Ohne KI-Verbindung ist das Feld **KI-Modell**
ausgegraut: „Noch keine KI-Verbindung unter „Zugänge und Lizenzen“ (Schlüssel
und Adresse): Ohne sie gibt es keine KI-Bewertung, und das Modell wird nicht
verwendet.“

Ein Check schickt den sichtbaren Text von bis zu fünf Seiten und einen
Screenshot, etwa 15 000 bis 25 000 Eingabe-Token; mit einem Modell der
Mittelklasse kostet das wenige Cent.

## 4. Einstellungen

**NEO Digital › Website-Check** öffnen und über der Liste auf **Einstellungen**
klicken. Es gibt einen Datensatz mit Einstellungen für die Installation.

| Abschnitt | Feld | Bedeutung |
| --- | --- | --- |
| Wer den Bericht verschickt | **Firma / Agentur** | Berichtskopf, E-Mail, Signatur. Leer: der Titel der Website. |
| | **Ansprechperson** | Die Person, die den Bericht unterschreibt, z. B. „Max Mustermann“ |
| | **Telefon** | Bericht und E-Mail-Signatur |
| | **Website** | z. B. `https://example.com` |
| | **Kontaktseite** | Ziel der Knöpfe „Ergebnis besprechen“ in Bericht, E-Mail und Element. Ohne Kontaktseite (hier, in der Konfiguration oder am Element) entfallen die Knöpfe. |
| | **Datenschutzseite** | Verlinkt unter dem Kontaktformular, in E-Mail und PDF. Ohne sie erscheint kein Link: setzen. |
| Design | **Logo** | PNG, JPG oder SVG für Berichtskopf und E-Mail, dunkel auf hellem Grund |
| | **Logo für dunklen Hintergrund** | Optionale Variante für den farbigen E-Mail-Kopf |
| | **Porträtfoto** | Optional, im Kasten „So geht es weiter“ und in der E-Mail-Signatur |
| | **Hauptfarbe**, **Akzentfarbe** | Nur PDF und E-Mail (Hex, z. B. `1f2328`, `0969da`). Das Element auf der Website gestaltet das CSS (siehe [Anpassen](customising.md)). |
| Konfigurationen | **Konfigurationen bearbeiten (…)** | Öffnet die Konfigurationen je Sprache (siehe [Bedienungsanleitung](usage/configurations.md)) |
| E-Mail | **Absendername** | Leer: Firmenname |
| | **Absenderadresse** | Leer: `config.yaml` oder die Administrator-Adresse von Contao |
| | **Antwortadresse** | Antworten auf die Berichts-E-Mail (Lizenz) |
| | **Kopie an** | Bekommt jeden Bericht als Kopie samt Name, E-Mail und Newsletter-Entscheidung des Besuchers. Ohne Lizenz geht der Bericht nur hierher (leer: an die Absenderadresse). |
| Grenzen | **Checks je Besucher und Stunde** | 0 = `config.yaml` (Vorgabe 2) |
| | **Checks je Besucher und Tag** | 0 = `config.yaml` (Vorgabe 10) |
| | **Neue Checks gesamt je Stunde** | Alle Besucher zusammen; schützt das KI-Budget. Gespeicherte Ergebnisse zählen nicht. 0 = Vorgabe 20. |
| | **Neue Checks gesamt je Tag** | 0 = Vorgabe 60 |
| | **Zwischenspeicher in Stunden** | Dieselbe Domain in derselben Sprache innerhalb dieser Zeit bekommt das gespeicherte Ergebnis. 0 = Vorgabe 24. |
| | **Aufbewahrung in Tagen** | Danach werden Checks samt Kontaktdaten, Screenshots und PDFs gelöscht. 0 = Vorgabe 90. |
| | **Seiten je Check** | Startseite plus wichtige Unterseiten, höchstens 8. 0 = Vorgabe 5. |
| KI-Modell und Zugänge | Statuszeilen | Woher PageSpeed- und KI-Schlüssel kommen, das verwendete Modell, ein Link **Öffnen** zu *Zugänge und Lizenzen* |
| | **KI-Modell** | Siehe Schritt 3 |

Einen Besucher erkennt der Check an einem Hash der IP-Adresse, der jeden Tag
wechselt.

## 5. Verbindungen prüfen

Neben den Speicherknöpfen der Einstellungen stehen **Vorschau PDF**,
**Vorschau E-Mail** und **Verbindung prüfen**. **Verbindung prüfen** ruft jeden
Dienst, den der Check braucht, vom Server aus auf – PageSpeed, die KI-Route
(ihre Modellliste) und, wenn das Brevo-Newsletter-Modul installiert ist, Brevo –
und zeigt die genaue Antwort oder den Fehler, dazu die Fehler der letzten
Checks. Schlüssel sind in jeder Meldung maskiert.

Die Vorschauen erzeugen PDF und E-Mail mit dem letzten fertigen Check, oder mit
Beispieldaten, wenn es keinen gibt.

## 6. Konfigurationen

Je Sprache der Website eine Konfiguration anlegen: Sie hält die Gestaltung von
E-Mail und PDF, die Texte des Elements, Kontakt- und Datenschutzseite und den
Newsletter dieser Sprache. Siehe
[Bedienungsanleitung](usage/configurations.md). Ohne Konfiguration nutzen
Bericht und E-Mail die mitgelieferte Vorlage *Standard* in der Sprache des
Checks.

## 7. E-Mail-Versand

Berichte gehen über den Mailversand von Contao (`MAILER_DSN`).

- **Mit Lizenz** bekommt der Besucher die E-Mail mit dem PDF im Anhang. **Kopie
  an** bekommt eine zweite E-Mail mit demselben PDF und den Kontaktdaten (Name,
  E-Mail, Domain, Note, Newsletter-Entscheidung) in Betreff und Text; eine Bcc
  könnte diese Angaben nicht tragen.
- **Ohne Lizenz** geht die für den Besucher gestaltete E-Mail mit dem PDF nur an
  **Kopie an** (leer: an die Absenderadresse), mit einem Kasten oben, der den
  Besucher nennt.

Schlägt der Versand fehl, sagt es die Ergebnisseite, und das Ergebnis bleibt
sichtbar; die Liste der Checks bietet **Bericht erneut senden** (Lizenz).

## 8. PDF-Erzeugung

Eingestellt in `config/config.yaml` (technisch, nicht redaktionell):

| `pdf.engine` | Wirkung |
| --- | --- |
| `dompdf` (Vorgabe) | Reines PHP, kein weiterer Dienst. Standardschrift Arimo (mitgeliefert; sieht aus und läuft wie Arial; lateinische, griechische, kyrillische Zeichen). DejaVu Sans, DejaVu Serif und Helvetica stehen im Designer unter *Design › Schrift*; Helvetica kennt nur westeuropäische Zeichen. Dompdf wandelt Arimo einmal nach `var/neo-websitecheck/fonts` um. |
| `gotenberg` | Chromium als Dienst erzeugt dieselbe Vorlage, mit Ihren Schriften. `pdf.gotenberg_url` setzen, z. B. `http://gotenberg:3000`; den Dienst mit `docker run --rm -p 3000:3000 gotenberg/gotenberg:8` starten. |
| `none` | Kein PDF; die E-Mail enthält trotzdem Note und Maßnahmen. |

## 9. Newsletter (Lizenz)

Das Kontaktformular zeigt ein Newsletter-Häkchen nur mit Lizenz und nur, wenn
ein Newsletter-System und eine Bestätigungsseite eingestellt sind. Kein
Schlüssel gehört in eine Konfigurationsdatei oder die Umgebung: Der Check nutzt
das Newsletter-System der Website.

| System | Was die Konfiguration braucht |
| --- | --- |
| **Brevo (Newsletter-Modul)**, `neo-digital/contao-brevo-bundle` | **Brevo-Verteiler**, **Double-Opt-in-Vorlage** und **Bestätigungsseite**, live aus dem Brevo-Konto, mit dem API-Schlüssel dieses Moduls (Abschnitt *Brevo* unter *Zugänge und Lizenzen*) |
| **Contao-Newsletter**, `contao/newsletter-bundle` | **Newsletter-Verteiler (Contao)** und **Bestätigungsseite**: eine Seite mit Contaos Modul *Abonnieren* (Newsletter). Der Besucher bekommt Contaos Bestätigungs-E-Mail (Text aus der Abonnementbestätigung dieses Moduls, sonst Contaos Vorgabe) und wird über deren Link freigeschaltet. |

Jede Konfiguration wählt ihr **Newsletter-System**; leer heißt automatisch:
Brevo, wenn das Brevo-Modul installiert ist, sonst Contaos Newsletter. Ist
keines installiert oder steht `newsletter.provider: none`, gibt es kein
Häkchen. Eine Anmeldung beginnt nur, wenn der Besucher das Häkchen setzt;
Abonnent ist er erst nach dem Klick auf den Bestätigungslink.

## 10. Server-Konfiguration (`config.yaml`)

Jeder Schlüssel ist optional. Werte aus dem Backend haben Vorrang;
`config.yaml` ist der Rückfall für leere Felder. Schlüssel aus `config.yaml`
oder der Umgebung übernimmt die Datenbankaktualisierung einmal nach *Zugänge
und Lizenzen*.

```yaml
neo_websitecheck:
    pagespeed:
        api_key: '%env(NEO_WEBSITECHECK_PSI_KEY)%'   # Rückfall; der Schlüssel gehört unter „Zugänge und Lizenzen“
        timeout: 55                                  # Sekunden, 20 bis 170
    ai:
        base_url: 'https://router.requesty.ai/v1'   # Rückfall für die KI-Verbindung
        api_key: '%env(NEO_WEBSITECHECK_AI_KEY)%'    # Rückfall; leer = kein KI-Schritt
        model: ''                                    # Modell-ID der Route; leer = in den Einstellungen gewählt
        max_tokens: 3000
        timeout: 90
        send_screenshot: true                        # den Handy-Screenshot mitschicken (braucht ein Modell mit Bildverständnis)
    mail:
        from_email: 'office@example.com'             # leer = Administrator-Adresse von Contao
        from_name: 'Beispiel GmbH'
        reply_to: 'office@example.com'
        notify_email: 'office@example.com'           # Ihre Kopie; leer = keine
    brand:
        name: 'Beispiel GmbH'                        # leer = Titel der Website
        owner_name: 'Max Mustermann'
        phone: ''
        website_url: 'https://example.com'
        contact_url: 'https://example.com/kontakt'   # Rückfall-Ziel von „Ergebnis besprechen“
        privacy_url: 'https://example.com/datenschutz'
        logo: 'files/theme/logo.png'                 # relativ zum Projektordner
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
        max_pages: 5                                 # 1 bis 8
        retention_days: 90
    pdf:
        engine: dompdf                               # dompdf | gotenberg | none
        gotenberg_url: ''
    newsletter:
        provider: auto                               # auto | brevo | contao | none
        label: ''                                    # Text des Häkchens; leer = Standardtext
        brevo_list_id: 0                             # Rückfall, wenn eine Konfiguration keinen setzt
        brevo_template_id: 0
        brevo_redirect_url: ''
    tracking:
        data_layer: true                             # Ereignisse in window.dataLayer schreiben
    storage_dir: '%kernel.project_dir%/var/neo-websitecheck'
    user_agent: 'Mozilla/5.0 (compatible; NeoWebsitecheck/1.0)'   # so meldet sich der Check bei der geprüften Website
```

Schlüssel für den Rückfall gehören in `.env.local`, nie in `config.yaml`:

```
NEO_WEBSITECHECK_PSI_KEY=…
NEO_WEBSITECHECK_AI_KEY=…
```

## 11. Wie die Checks laufen

Der Check ist eine Folge von Schritten, die sich fortsetzen lässt: Startseite
öffnen und Unterseiten finden, Startseite messen, Unterseiten messen,
Barrierefreiheit, Rechtsseiten und KI-Sichtbarkeit prüfen, Lesen durch die KI,
benoten und zusammenstellen. Jeder Schritt ist kurz genug für eine HTTP-Anfrage.
Der Browser des Besuchers fragt alle zwei Sekunden den Stand ab, und die
Anfrage, die den Check untätig vorfindet, führt den nächsten Schritt aus. Ein
Worker ist nicht nötig; das läuft auch auf Shared Hosting.

- Der Contao-Cron (jede Minute) stellt Checks fertig, die ein Besucher verlassen
  hat.
- Der tägliche Cron löscht Checks, die älter sind als **Aufbewahrung in Tagen**.
- Ein echter Worker ist möglich: `neo:websitecheck:run` per Cron oder
  Prozessverwaltung aufrufen.

Screenshots und PDFs liegen in `var/neo-websitecheck/<id>/`, außerhalb des
Web-Verzeichnisses; die Live-Vorschau bekommt sie über eine Adresse, die das
Token des Checks schützt.

## 12. Auf der Kommandozeile testen

Ohne Browser, ohne Grenzen:

```bash
# ein neuer Check einer Website, als Test der Einrichtung
php vendor/bin/contao-console neo:websitecheck:run --url=https://example.com --locale=de
# einen Check ausführen oder ansehen
php vendor/bin/contao-console neo:websitecheck:run 42 --json
# alle offenen Checks weiterführen
php vendor/bin/contao-console neo:websitecheck:run
# ein fertiger Beispiel-Check ohne API-Schlüssel; --send verschickt auch seine E-Mails
php vendor/bin/contao-console neo:websitecheck:demo --locale=de --send --email=office@example.com
```

`--email` setzt die Besucheradresse des Beispiel-Checks; mit `--send` geht die
Berichts-E-Mail dorthin (und Ihre Kopie an **Kopie an**, wenn gesetzt).
