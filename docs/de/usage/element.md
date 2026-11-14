---
titel: Das Element auf der Seite
kurzfassung: Das Inhaltselement Website-Check einbinden, seine Felder, was Besucher vom Adressfeld über den Fortschritt und das Kontaktformular bis zum Ergebnis sehen, und das Element auf Websites in mehreren Sprachen.
zielgruppe: anwender
sprache: de
stand: 2026-11-12
---

# Das Element auf der Seite

## Das Element einbinden

1. **Inhalte › Artikel** öffnen, den Artikel der Seite (eine Landingpage, eine
   Seite über Webdesign oder eine eigene Seite wie `/website-check`).
2. Ein Inhaltselement vom Typ **Website-Check** anlegen (Gruppe *NEO
   Digital*).
3. Die Felder ausfüllen und speichern.

| Abschnitt | Feld | Bedeutung |
| --- | --- | --- |
| Elementtyp | **Überschrift** | Überschrift über dem Check |
| Text/HTML/Code | **Text** | Einleitung über dem Adressfeld: was geprüft wird, wie lange es dauert |
| Konfiguration | **Konfiguration** | Welche Konfiguration (Texte, E-Mail, PDF) das Element nutzt. **Automatisch (nach Seitensprache)** (Vorgabe): die Konfiguration mit der Sprache der Seite. Ausdrücklich wählen für eine zweite Konfiguration derselben Sprache, z. B. für eine Kampagne. |
| Hyperlink-Einstellungen | **Link-Adresse** | Ziel des Knopfs „Ergebnis besprechen“. Leer: die Kontaktseite der Konfiguration, sonst der Einstellungen. Ohne Kontaktseite entfällt der Knopf. |

Die übrigen Abschnitte (Template, Zugriffsschutz, Experteneinstellungen,
Sichtbarkeit) sind Contaos eigene Felder jedes Inhaltselements.

## Was Besucher sehen

![Das Element auf einer Seite vor einem Check, in der Gratisfassung. Rahmen 1
markiert die Beschriftung „Adresse Ihrer Website“ mit dem Adressfeld und dem
Knopf „Jetzt prüfen“, Rahmen 2 den Browserrahmen, in dem während des Checks die
geprüfte Website erscheint, Rahmen 3 den Hinweis „powered by“ mit der
NEO-Wortmarke unter den Themen.](../bilder/element-front.png)

Die Nummern im Bild: 1 das Adressfeld mit **Jetzt prüfen**, 2 die Fläche für den
Screenshot der geprüften Website, 3 der Hinweis der Gratisfassung (entfällt mit
Lizenz).

1. **Adressfeld** mit dem Knopf **Jetzt prüfen**. Der Spam-Schutz läuft
   unsichtbar: ALTCHA ab Contao 5.4, unter Contao 5.3 ein verstecktes Feld und
   eine Mindestzeit zum Ausfüllen.
2. **Live-Fortschritt**: sechs Schritte mit Häkchen („Startseite aufrufen,
   Sitemap und wichtige Unterseiten finden“, „Startseite am Handy messen (Google
   PageSpeed)“, „Unterseiten messen“, „Barrierefreiheit, Rechtliches und
   KI-Sichtbarkeit prüfen“, „Texte lesen und Einschätzung formulieren“,
   „Benoten und Bericht zusammenstellen“), die verbleibenden Sekunden und ein
   Screenshot der geprüften Website mit Markierungen an den Befunden. Ein Check
   dauert 60 bis 120 Sekunden.
3. **Kontaktformular**: Vorname, Nachname und E-Mail-Adresse (Pflicht); das
   freiwillige Häkchen „Ja, Sie dürfen mich einmal per E-Mail zu diesem Ergebnis
   kontaktieren.“ (Wortlaut und Zeitpunkt werden beim Check gespeichert); mit
   Lizenz das freiwillige Newsletter-Häkchen. Der Link zur Datenschutzseite
   wird an den Datenschutzhinweis angehängt. Der Wortlaut ist offen: Ohne
   diese Angaben gibt es kein Ergebnis.
4. **Ergebnis auf der Seite**: mit **Kurzfassung (empfohlen)** die Note, ein
   Satz und der Hinweis auf das PDF; mit **Ausführlich** zusätzlich die vier
   Lighthouse-Werte, die sieben Bereiche mit Noten, die drei wichtigsten
   Maßnahmen mit Aufwand, was bereits passt, und der Knopf „Ergebnis
   besprechen, kostenlos“. Ohne Lizenz zeigt die Seite immer das ausführliche
   Ergebnis.
5. **E-Mail mit dem PDF-Bericht**: mit Lizenz an den Besucher, ohne Lizenz an
   den Betreiber der Website (siehe [Einrichtung](../setup.md#7-e-mail-versand)).

Wurde eine Serverwarnung gefunden (z. B. eine PHP-Fassung mit bekannter
Sicherheitslücke), zeigt die Ergebnisseite vor der Note einen kurzen Hinweis;
die vollständige Warnung mit dem, was zu tun ist, steht im PDF und in den
E-Mails.

Ein zweiter Besucher, der dieselbe Domain in derselben Sprache innerhalb des
Zwischenspeichers (Vorgabe 24 Stunden) eingibt, bekommt das gespeicherte
Ergebnis sofort und muss trotzdem das Kontaktformular ausfüllen.

### Meldungen an Besucher

| Meldung (Vorgabe) | Wann |
| --- | --- |
| „Bitte geben Sie eine gültige Adresse ein, z. B. ihre-firma.at.“ | Die Adresse lässt sich nicht lesen |
| „Sie haben in der letzten Stunde schon … Checks gestartet. …“ | **Checks je Besucher und Stunde** erreicht |
| „Sie haben heute schon … Checks gestartet. …“ | **Checks je Besucher und Tag** erreicht |
| „Die Spam-Prüfung ist nicht durchgegangen. …“ | ALTCHA oder das versteckte Feld ist fehlgeschlagen |
| „Der Website-Check ist gerade nicht verfügbar. …“ | Kein PageSpeed-Schlüssel gespeichert, oder die Gesamtgrenze neuer Checks je Stunde oder Tag erreicht |
| „Diese Website konnte nicht geprüft werden (nicht erreichbar oder gesperrt).“ | Die geprüfte Website antwortet nicht oder sperrt den Check |
| „Die E-Mail konnte nicht gesendet werden, das Ergebnis bleibt hier sichtbar.“ | Der Mailversand ist fehlgeschlagen |

## Websites in mehreren Sprachen

Ein Startpunkt je Sprache, wie es Contao vorsieht:

1. **Eine Konfiguration je Sprache** anlegen (siehe
   [Konfigurationen und Designer](configurations.md)), mit Kontakt- und
   Datenschutzseite und dem Newsletter dieser Sprache.
2. Das Element auf den Seiten jeder Sprache einbinden und **Konfiguration** auf
   *Automatisch* lassen oder die Konfiguration wählen.
3. Alles folgt der Sprache der Seite: Texte auf der Seite, der Check, das Lesen
   durch die KI (in der Sprache der Seite geschrieben), E-Mail, PDF und
   Newsletter. Eine Seite, für deren Sprache es keine Konfiguration gibt,
   bekommt die mitgelieferte Vorlage in ihrer eigenen Sprache, nie die
   Konfiguration einer anderen Sprache.

Die Erweiterung wählt nie selbst eine Seite der Website: Ohne Kontaktseite
entfällt der Knopf, ohne Bestätigungsseite das Newsletter-Häkchen, ohne
Datenschutzseite der Link.
