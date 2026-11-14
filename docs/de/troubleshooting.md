---
titel: Fehlersuche
kurzfassung: Häufige Probleme mit dem Website-Check – keine Prüfung angenommen, PageSpeed-Kontingent, abgebrochene Checks, kein Lesen durch die KI, keine E-Mail, fehlende Zeichen im PDF, Spam-Schutz, Lizenz und Hinweis, fehlende Rechte – mit Ursache und Lösung.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Fehlersuche

**Verbindung prüfen** in den Einstellungen ruft PageSpeed, die KI-Route und
Brevo vom Server aus auf und zeigt die genaue Antwort, dazu die Fehler der
letzten Checks. Abgelehnte Checks stehen in der Protokolldatei der Installation
(`var/logs/`), z. B. „Website check refused: no PageSpeed key …“ oder „Website
check refused: overall limit of new checks reached (settings → limits).“
Fehlende Menüeinträge oder Felder sind fast immer fehlende Rechte; siehe
[Rechte](permissions.md).

## Checks

| Was man sieht | Warum | Was zu tun ist |
| --- | --- | --- |
| Besucher bekommen „Der Website-Check ist gerade nicht verfügbar. …“ | Kein PageSpeed-Schlüssel; oder die Gesamtgrenze neuer Checks je Stunde oder Tag ist erreicht | Den Schlüssel eintragen ([Einrichtung](setup.md#2-pagespeed-schlüssel)); **Neue Checks gesamt je Stunde / je Tag** erhöhen, wenn die Nachfrage echt ist. |
| „Quota exceeded“ von PageSpeed | Kein oder ein falscher Schlüssel; das öffentliche Kontingent teilt sich die ganze Welt | Einen eigenen Schlüssel eintragen. |
| Der Check bricht bei „Startseite am Handy messen“ ab | Die Website sperrt Googles Crawler, braucht eine Anmeldung oder ist aus dem Internet nicht erreichbar | Der Schritt wird einmal wiederholt, dann übersprungen; der Bericht sagt es. Die Website erreichbar machen. |
| „Diese Website konnte nicht geprüft werden (nicht erreichbar oder gesperrt).“ | Die Adresse antwortet nicht oder sperrt den Server des Checks | Die Adresse im Browser prüfen. |
| Ein Check bleibt auf `running` oder `queued`, nachdem der Besucher gegangen ist | Der Contao-Cron läuft nicht jede Minute | Den Cron einrichten (`contao:cron` jede Minute) oder `neo:websitecheck:run` ausführen. |
| Kein KI-Schritt im Fortschritt, *Inhalte & Klarheit* ohne KI-Text | Keine KI-Verbindung oder kein Modell gewählt | KI-Verbindung einrichten und ein **KI-Modell** wählen ([Einrichtung](setup.md#3-ki-verbindung-und-modell)). |
| Der KI-Schritt scheitert mit einem Modell, das keine Bilder liest | `send_screenshot` schickt den Handy-Screenshot mit | Ein Modell mit Bildverständnis wählen oder `ai.send_screenshot: false` setzen. |
| Ein zweiter Check derselben Domain zeigt das alte Ergebnis | Der Zwischenspeicher (**Zwischenspeicher in Stunden**, Vorgabe 24) | So vorgesehen; weniger Stunden einstellen. Ein Check ohne Messwerte wird nie wiederverwendet. |
| „Sie haben in der letzten Stunde schon … Checks gestartet.“ | **Checks je Besucher und Stunde** erreicht | So vorgesehen; für Tests die Grenze erhöhen oder mit `neo:websitecheck:run --url=…` testen. |
| Der Bericht sagt, ein KI-Crawler darf die Website lesen, obwohl die robots.txt ihn sperrt, oder alle Crawler seien gesperrt, obwohl es nur einer ist | Vor 1.0.0 liefen die Regeln der Gruppen in der robots.txt zusammen: Die Regeln einer späteren Gruppe (zum Beispiel `User-agent: *` mit `Allow: /`) galten auch für den Bot darüber | Die Erweiterung aktualisieren und den Check neu starten; jede Gruppe hat jetzt nur ihre eigenen Regeln. |

## E-Mail und PDF

| Was man sieht | Warum | Was zu tun ist |
| --- | --- | --- |
| Keine E-Mail; die Ergebnisseite sagt „Die E-Mail konnte nicht gesendet werden …“ | Mailversand nicht eingerichtet oder abgelehnt | `MAILER_DSN` und `var/logs/` prüfen; danach **Bericht erneut senden** in der Liste (Lizenz). |
| Der Besucher bekommt keine E-Mail, Sie schon | Keine Lizenz: Die E-Mail mit dem PDF geht nur an **Kopie an** oder die Absenderadresse | In der Gratisfassung so vorgesehen; einen Lizenzschlüssel eintragen. |
| **Bericht erneut senden**: „Die E-Mail an den Besucher gehört zur Lizenz. …“ | Keine Lizenz | Einen Lizenzschlüssel eintragen. |
| Umlaute oder andere Zeichen fehlen im PDF | Eine Vorlage nutzt eine Schrift, die Dompdf nicht hat, oder **Helvetica** (nur westeuropäische Zeichen) | Arimo oder DejaVu Sans behalten, eine eigene `.ttf` aus dem Dateiordner laden oder auf Gotenberg umstellen ([Anpassen](customising.md#pdf-schriften-bilder-zahlen-in-kästen)). |
| Das Logo fehlt in der E-Mail, dort steht der Firmenname | Das Logo ist ein SVG ohne PNG gleichen Namens | `logo.png` neben `logo.svg` legen. |
| Die Gestaltung wird nicht verwendet | Die Konfiguration hat Twig-Code aus dem erweiterten Modus | Ein Administrator schaltet zurück auf **Visuell** (der Code wird dann verworfen). |
| Speichern einer Konfiguration scheitert mit einem Twig-Fehler | Ein Syntaxfehler im erweiterten Modus | Die Vorschau zeigt die Zeile; berichtigen. |
| Gar kein PDF | `pdf.engine: none`, oder Gotenberg ist nicht erreichbar | `dompdf` einstellen oder `pdf.gotenberg_url` prüfen. |

## Element und Spam-Schutz

| Was man sieht | Warum | Was zu tun ist |
| --- | --- | --- |
| ALTCHA erscheint nicht, oder „Die Spam-Prüfung ist nicht durchgegangen. …“ | ALTCHA braucht HTTPS (oder `localhost`) | HTTPS nutzen. Unter Contao 5.3 greift automatisch das versteckte Feld. |
| „Die Prüfung ist fehlgeschlagen. Bitte die Seite neu laden.“ | Die ALTCHA-Aufgabe ist abgelaufen | Die Seite neu laden. |
| Das Element nimmt keine Eingabe an | Der Hinweis der Gratisfassung ist durch die Gestaltung abgeschnitten oder verdeckt | Den Hinweis sichtbar machen; er gehört zur kostenlosen Lizenz. |
| Der Hinweis erscheint, obwohl es eine Lizenz gibt | Die Website läuft ohne HTTPS, oder die Lizenz deckt die Adresse nicht ab, auf der der Besucher ist | HTTPS nutzen; die Domains der Lizenz unter *Zugänge und Lizenzen* prüfen. |
| Kein Knopf „Ergebnis besprechen“ | Keine Kontaktseite am Element, in der Konfiguration oder in den Einstellungen | Eine setzen. |
| Kein Link zur Datenschutzerklärung | Keine Datenschutzseite in der Konfiguration oder den Einstellungen | Eine setzen. |
| Kein Newsletter-Häkchen | Keine Lizenz; kein Newsletter-System installiert; keine Bestätigungsseite | Siehe [Einrichtung](setup.md#9-newsletter-lizenz). |
| Das Element sieht nach dem Überschreiben eines Templates falsch aus oder reagiert nicht | Dem eigenen Template fehlen `data-nwc-*`-Attribute | Mit dem Template der Erweiterung vergleichen. |

## Backend

| Was man sieht | Warum | Was zu tun ist |
| --- | --- | --- |
| Keine Schaltfläche **Einstellungen** über der Liste | Dem Benutzer fehlt **Website-Check Einstellungen** | Erlauben ([Rechte](permissions.md)). |
| **KI-Modell** ist ausgegraut | Keine KI-Verbindung unter *Zugänge und Lizenzen* | Die KI-Verbindung einrichten. |
| **KI-Modell** ist ein Textfeld, keine Liste | Noch kein KI-Schlüssel bekannt, oder die Route hat keine Modelle geliefert | Den Schlüssel eintragen; mit **Verbindung prüfen** nachsehen. |
| Antwortadresse, Newsletter oder „Ergebnis auf der Seite“ fehlen in einer Konfiguration | Keine Lizenz: Diese Einstellungen wirken nicht und sind ausgeblendet; ihre Werte bleiben | So vorgesehen. |
| Kein Schalter **Erweitert (Twig)** | Nur Administratoren sehen ihn | So vorgesehen. |
