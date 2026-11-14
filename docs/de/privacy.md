---
titel: Datenschutz
kurzfassung: Was der Website-Check speichert und wie lange, welche Dienste welche Daten bekommen, was der Lizenzdienst bekommt und was in die Datenschutzerklärung der Website gehört.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Datenschutz

## Was gespeichert wird

| Was | Wo | Wie lange |
| --- | --- | --- |
| Domain und Startadresse der geprüften Website, Messdaten, Ergebnis, Note | Tabelle `tl_neo_websitecheck` | **Aufbewahrung in Tagen** (Vorgabe 90), dann löscht der tägliche Cron |
| Vorname, Nachname, E-Mail-Adresse des Besuchers, Zeitpunkt der Anforderung | ebenda | ebenso |
| Newsletter-Entscheidung; die einmalige Kontakterlaubnis mit Wortlaut und Zeitpunkt | ebenda | ebenso |
| Ein Hash der IP-Adresse, der jeden Tag wechselt (für die Grenzen je Besucher) | ebenda | ebenso |
| Screenshots der geprüften Website und der PDF-Bericht | `var/neo-websitecheck/<id>/`, außerhalb des Web-Verzeichnisses | ebenso |
| Im Designer hochgeladene Bilder | `files/neo-websitecheck/` | bis zum Löschen in der Dateiverwaltung |
| Schlüssel (PageSpeed, KI) | *Zugänge und Lizenzen*, verschlüsselt mit dem App-Secret | bis zur Änderung |

Ein Check lässt sich in der Liste der Checks früher löschen (**Löschen**).

## Welche Dienste was bekommen

| Dienst | Bekommt | Wann |
| --- | --- | --- |
| Google PageSpeed Insights | die Adressen der geprüften Seiten | bei jedem neuen Check |
| Die geprüfte Website | Anfragen des Servers wie von einem Besucher (Seiten, `robots.txt`, `llms.txt`, Sitemap, acht übliche `phpinfo`-Adressen) | bei jedem neuen Check |
| Der KI-Anbieter der KI-Verbindung (z. B. Requesty, OpenAI) | den sichtbaren Text von bis zu fünf geprüften Seiten und den Handy-Screenshot der Startseite; keine Daten des Besuchers | bei jedem neuen Check mit KI-Verbindung |
| Der Mailversand der Website (`MAILER_DSN`) | die E-Mail mit dem PDF: an den Besucher (Lizenz) und an **Kopie an** mit Name, E-Mail, Domain, Note und Newsletter-Entscheidung | nach dem Kontaktformular |
| Brevo (Newsletter-Modul) | E-Mail-Adresse, Vor- und Nachname (Attribute `VORNAME`, `NACHNAME`, `FIRSTNAME`, `LASTNAME`), Verteiler, Vorlage, Bestätigungsseite | nur wenn der Besucher das Newsletter-Häkchen setzt (Lizenz) |
| Contaos Newsletter-Modul | E-Mail-Adresse, Verteiler | ebenso |
| Der Lizenzdienst | den Lizenzschlüssel, den Code der Erweiterung, die Domains der Website und die Versionen; nie Daten von Besuchern | beim Speichern des Schlüssels und einmal täglich |

Ist der Lizenzdienst nicht erreichbar, gilt die letzte Bestätigung bis zum Ende
ihrer Schonfrist weiter.

Die Wortmarke des Hinweises in der E-Mail (Gratisfassung) ist in die Nachricht
eingebettet; sie anzuzeigen lädt nichts von einem Server.

Der Spam-Schutz ALTCHA gehört zu Contao und schickt nichts an andere Dienste.

## Datenschutzerklärung

Den Website-Check in die Datenschutzerklärung der Website aufnehmen:

- Zweck: Zusendung des angeforderten Berichts (und, mit der Erlaubnis, eine
  E-Mail zum Ergebnis; mit gesetztem Häkchen der Newsletter);
- die Daten des Kontaktformulars und ihre Speicherdauer (**Aufbewahrung in
  Tagen**);
- die Auftragsverarbeiter: Google (PageSpeed Insights), der KI-Anbieter, der
  Mailanbieter, Brevo, wenn der Newsletter darüber läuft;
- dass Screenshots und Messungen die eingegebene Website betreffen, nicht den
  Besucher.

Die **Datenschutzseite** in den Einstellungen (oder je Konfiguration) setzen:
Das Kontaktformular verlinkt sie. Die Ergebnisse sind eine technische
Einschätzung; der Wortlaut des rechtlichen Bereichs sagt „technische Prüfung,
keine Rechtsberatung“.
