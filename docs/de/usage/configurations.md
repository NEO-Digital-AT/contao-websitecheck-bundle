---
titel: Konfigurationen und Designer
kurzfassung: Eine Konfiguration je Sprache des Website-Checks – E-Mail, PDF-Bericht, Seiten, Newsletter, Texte des Elements – und der visuelle Designer für E-Mail und PDF mit Vorlagen, Bezeichnungen, Vorschau und erweitertem Modus; Sprachen ohne Sprachdatei.
zielgruppe: anwender
sprache: de
stand: 2026-11-12
---

# Konfigurationen und Designer

Eine Konfiguration hält alles, was ein Besucher in einer Sprache liest: die
E-Mail, den PDF-Bericht, die Texte des Elements, Kontakt- und Datenschutzseite
und den Newsletter. Je Sprache der Website eine anlegen, oder eine zweite
derselben Sprache für eine Kampagne. Der Check merkt sich seine Konfiguration;
E-Mail und PDF passen so zu der Seite, die der Besucher benutzt hat.

## Eine Konfiguration anlegen

1. **NEO Digital › Website-Check** öffnen, auf **Einstellungen** und dann auf
   **Konfigurationen bearbeiten (…)** klicken.
2. Auf **Neue Konfiguration** klicken.
3. **Bezeichnung** (intern, z. B. „Deutsch“ oder „Deutsch – Hotels“) eintragen
   und die **Sprache** wählen. Die Liste bietet jede Sprache, die Contao kennt.
4. Speichern. Beim ersten Speichern füllt die Erweiterung leere Gestaltungen
   mit der Vorlage *Standard* in der Sprache der Konfiguration; der
   PDF-Dateiname ist mit `website-check-{{domain}}-{{date}}.pdf` vorbelegt.

Die Liste zeigt jede Konfiguration als „Bezeichnung [SPRACHE]“.
**Duplizieren** kopiert eine Konfiguration, z. B. als Anfang einer weiteren
Sprache.

## Die Felder

| Abschnitt | Feld | Bedeutung |
| --- | --- | --- |
| Seiten dieser Sprache | **Kontaktseite** | Ziel von „Ergebnis besprechen“ in Formular, E-Mail und PDF. Leer: die Kontaktseite der Einstellungen. Das Element kann sie mit seiner **Link-Adresse** überschreiben. |
| | **Datenschutzseite** | Link im Kontaktformular, in E-Mail und PDF. Leer: die Datenschutzseite der Einstellungen. Ohne Seite erscheint kein Link. |
| E-Mail an den Besucher | **Betreff** | Platzhalter erlaubt. Vorgabe: „Ihr Website-Check für {{domain}}: Note {{grade}} · {{grade_word}}“ |
| | **Absendername**, **Absenderadresse** | Leer: die Einstellungen |
| | **Antwortadresse** (Lizenz) | Leer: die Einstellungen |
| E-Mail gestalten | **E-Mail** | Der E-Mail-Designer (unten) |
| PDF-Bericht | **PDF-Dateiname** | Mit Platzhaltern, z. B. `website-check-{{domain}}-{{date}}.pdf`; `{{grade}}`, `{{firstname}}`, `{{lastname}}`, `{{company}}` gehen auch |
| PDF-Bericht gestalten | **PDF-Bericht** | Der PDF-Designer (unten) |
| Newsletter dieser Sprache (Lizenz) | **Newsletter-System** | **Automatisch (Brevo, wenn installiert)**, **Brevo (Newsletter-Modul)** oder **Contao-Newsletter**. Nach dem Speichern erscheinen die passenden Felder. |
| | **Brevo-Verteiler**, **Double-Opt-in-Vorlage**, **Bestätigungsseite** | Mit Brevo. Leerer Verteiler oder leere Vorlage: der Rückfall aus `config.yaml`. Ohne Bestätigungsseite gibt es keine Newsletter-Anmeldung. |
| | **Newsletter-Verteiler (Contao)**, **Bestätigungsseite** | Mit Contaos Newsletter: Die Bestätigungsseite trägt Contaos Modul *Abonnieren*; der E-Mail-Text kommt aus diesem Modul. |
| Ihre Kopie (Lizenz) | **Betreff Ihrer Kopie** | Vorgabe: „[Website-Check] {{domain}} · Note {{grade}} · {{firstname}}“ |
| Das Element auf der Seite | **Ergebnis auf der Seite** (Lizenz) | **Kurzfassung (empfohlen)**: Note, ein Satz, Hinweis auf das PDF. **Ausführlich**: zusätzlich Messwerte, alle Bereiche, Maßnahmen und Stärken. Das PDF enthält immer alles. |
| | **Kontakterlaubnis abfragen** | Zeigt das freiwillige Häkchen „Sie dürfen mich einmal per E-Mail zu diesem Ergebnis kontaktieren“ |
| | Texte des Elements | Beschriftung über dem Adressfeld, Knopf „Jetzt prüfen“, Titel, Text, Datenschutzhinweis und Knopf des Kontaktformulars, Text des Häkchens „einmal kontaktieren“, Text des Newsletter-Häkchens, Hinweis und Knopf beim Ergebnis; dazu die Fassungen „… ohne Lizenz“, die keine E-Mail versprechen. Leer: die Vorgabe der Sprache. |
| Interne Kopie als Twig-Vorlage (Administratoren, Lizenz) | **Ihre Kopie als Twig-Vorlage** | Leer: die mitgelieferte Vorlage |

Ohne Lizenz sind die mit „Lizenz“ markierten Felder ausgeblendet; ihre Werte
bleiben.

## Der Designer

Der Designer ist für E-Mail und PDF gleich. Er hat drei Bereiche:

- **Links**: **Blöcke** zum Anklicken oder Hineinziehen, **Aufbau** zum
  Umordnen per Ziehen, **Design** für Farben, Schrift, Eckenradius, Ränder,
  Kopf- und Fußzeile, und **Bezeichnungen**.
- **Mitte**: **Vorschau** mit dem letzten fertigen Check oder Beispieldaten.
  Einen Block in der Vorschau anklicken, um ihn zu bearbeiten.
- **Rechts**: **Eigenschaften** des gewählten Blocks: Texte mit Platzhaltern,
  formatierter Text (fett, kursiv, Link, Liste), Bilder aus der Dateiverwaltung
  oder mit **Hochladen**, Ausrichtung, Abstände, Hintergrund, was gezeigt wird.

![Der PDF-Designer mit der Standard-Vorlage; der Block „Maßnahmen“ ist gewählt.
Rahmen 1 markiert den linken Bereich mit den Reitern Blöcke, Aufbau und Design,
Rahmen 2 die Vorschau in der Mitte, Rahmen 3 rechts die Eigenschaften des
gewählten Blocks.](../bilder/designer.png)

Die Nummern im Bild: 1 der linke Bereich (hier **Aufbau**), 2 die **Vorschau**,
3 die **Eigenschaften** des gewählten Blocks.

| Blöcke des PDF | Blöcke der E-Mail |
| --- | --- |
| Deckblatt, Überschrift, Text, Bild, Hinweis, So geht es weiter, Signatur, Gesamtnote, Kennzahlen, Screenshots, Google-Ringe, Bereiche im Überblick, Maßnahmen, Was bereits passt, Bereich im Detail, Messwerte Ladezeit, KI-Abschnitte, Tabelle der Seiten, Server-Sicherheit, Quellen, Abstand, Trennlinie, Seitenumbruch | Kopf, Überschrift, Text, Bild, Gesamtnote, Bereiche im Überblick, Maßnahmen, Was bereits passt, Server-Sicherheit, Knopf, PDF-Hinweis, Newsletter-Hinweis, Signatur, Abstand, Trennlinie, Fußzeile |

**Platzhalter** (Knopf **Platzhalter einfügen**): `{{firstname}}`,
`{{lastname}}`, `{{domain}}`, `{{grade}}`, `{{grade_word}}`, `{{pages}}`,
`{{company}}`, `{{owner}}`, `{{phone}}`, `{{website}}`, `{{days}}`,
`{{date}}`.

**Jedes Wort gehört Ihnen.** Außer dem, was der Check findet (das Lesen durch
die KI, Befunde, Zusammenfassungen der Bereiche, Maßnahmen und Messwerte),
kommt alles in E-Mail und PDF aus der Gestaltung: Überschriften,
Beschriftungen, Tabellenspalten, Legende, Notenwörter, Namen der Bereiche,
Aufwandsangaben, Namen der Messwerte, Kopf- und Fußzeile und das Wort vor der
Seitenzahl. Jeder Block trägt seine Wörter als Eigenschaften; Wörter, die
mehrere Blöcke nutzen, stehen unter **Design › Bezeichnungen**.

### Vorlagen

**Vorlage laden** ersetzt die Gestaltung nach einer Rückfrage durch eine
fertige in der Sprache der Konfiguration:

| PDF | E-Mail |
| --- | --- |
| Standard (Deckblatt mit Note, alle Bereiche) | Standard (Note, Maßnahmen, PDF-Hinweis, Knopf) |
| Mit Deckblatt (Deckblatt in Hauptfarbe, Ergebnis auf Seite 2) | Kurz (Note, PDF-Hinweis, Knopf) |
| Kompakt (eine Seite) | |

**PDF-Vorschau** öffnet das echte PDF des aktuellen Entwurfs; für die E-Mail
zeigt **In neuem Tab öffnen** sie im Browser. Die Knöpfe **PDF-Vorschau** und
**E-Mail-Vorschau** neben den Speicherknöpfen tun dasselbe für die gespeicherte
Konfiguration.

### Erweiterter Modus (Administratoren)

Der Schalter **Visuell | Erweitert (Twig)** über dem Designer zeigt das ganze
Dokument als Twig-Code: den Rahmen mit seinem CSS, das HTML jedes Blocks und
jedes Wort. Änderungen erscheinen in der Vorschau; Syntaxfehler zeigt sie mit
der Zeile, und das Speichern wird abgelehnt. Solange Code gespeichert ist, gilt
er statt der visuellen Gestaltung („Für diese Konfiguration ist im erweiterten
Modus Twig-Code hinterlegt. Er hat Vorrang, diese Gestaltung wird dann nicht
verwendet.“). Zurück auf **Visuell** verwirft den Code nach einer Rückfrage.
Andere Benutzer sehen weder den Schalter noch die Code-Felder.

## Sprachen ohne Sprachdatei

Sprachdateien liegen für elf Sprachen bei. Für jede andere Sprache übersetzt
die KI-Verbindung die Texte für Besucher (Befunde, Maßnahmen, Formular,
Bericht, E-Mail) einmalig: beim Speichern einer Konfiguration in dieser
Sprache, beim ersten Aufruf einer Seite dieser Sprache mit dem Element (nach
dem Ausliefern der Seite) oder spätestens vor der Benotung des ersten Checks.
Das dauert Sekunden und kostet rund einen Cent. Ohne KI-Verbindung gilt die
Standardsprache der Installation.

Der Befehl `php vendor/bin/contao-console neo:websitecheck:translate <sprache>
--export` schreibt die Texte zum Korrigieren nach
`translations/neo_websitecheck.<sprache>.yaml`; eine Projektdatei hat immer
Vorrang vor den Texten der KI. `--force` übersetzt alle Texte neu. Datum und
Zahlen folgen dem Format der Sprache.
