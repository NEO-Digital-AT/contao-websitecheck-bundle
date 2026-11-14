---
titel: Die Liste der Checks
kurzfassung: Die Checks des Website-Checks im Backend nachverfolgen – die Liste mit Note, Status und Kontaktdaten, die Details, der PDF-Bericht, Bericht erneut senden und löschen.
zielgruppe: anwender
sprache: de
stand: 2026-11-12
---

# Die Liste der Checks

**NEO Digital › Website-Check** führt jeden Check, den neuesten zuerst, mit
Domain, Note, Status, Vorname, Nachname, E-Mail und Startzeit. Der Filter wählt
nach Startdatum, Status, Note, Kontakterlaubnis und Newsletter; die Suche findet
eine Domain, einen Vor- oder Nachnamen oder eine E-Mail-Adresse.

![Die Liste der Checks mit drei Beispiel-Checks. Rahmen 1 markiert rechts die
Leiste mit Suche und Filter, Rahmen 2 die Aktionen eines Checks (bearbeiten,
PDF-Bericht, löschen, Details), Rahmen 3 den Knopf „Einstellungen“ über der
Liste.](../bilder/checks-list.png)

Die Nummern im Bild: 1 Suche und Filter, 2 die Aktionen eines Checks (Tabelle
unten), 3 **Einstellungen**, öffnet die Einstellungen des Website-Checks.

| Status | Bedeutung |
| --- | --- |
| `queued` | wartet auf den nächsten Schritt |
| `running` | ein Schritt läuft |
| `done` | fertig |
| `failed` | abgebrochen; die Website ließ sich nicht prüfen |

Die Gesamtnote ist 1 (sehr gut) bis 5 (nicht genügend); 0 heißt nicht
abgeschlossen.

## Aktionen je Check

| Aktion | Was sie tut |
| --- | --- |
| **Bearbeiten** | Öffnet den Check: Domain, Start-URL, Status, Note, Zeiten; Vorname, Nachname, E-Mail, **Einmaliger Kontakt erlaubt**, **Newsletter gewünscht**, **Ergebnis angefordert** (wann der Besucher die Angaben gemacht hat); die Zusammenfassung mit den Noten je Bereich und den wichtigsten Maßnahmen |
| **PDF-Bericht** | Öffnet den PDF-Bericht des Checks |
| **Bericht erneut senden** | Schickt die Berichts-E-Mail nach einer Rückfrage („Bericht erneut an … senden?“) noch einmal an den Besucher. Braucht eine Lizenz: ohne sie „Die E-Mail an den Besucher gehört zur Lizenz. Ohne Lizenz wird sie nicht verschickt.“ |
| **Löschen** | Löscht den Check samt Kontaktdaten, Screenshots und PDF |
| **Details** | Zeigt alle gespeicherten Werte |

Checks entstehen nur durch Besucher (oder die Befehle); das Backend kann keinen
anlegen. Der tägliche Cron löscht jeden Check, der älter ist als **Aufbewahrung
in Tagen** (Vorgabe 90).

## Einen Kontakt nachverfolgen

1. Den Check mit **Bearbeiten** öffnen.
2. **Einmaliger Kontakt erlaubt** prüfen: Nur dann darf man den Besucher
   einmal zu diesem Ergebnis anschreiben.
3. **Newsletter gewünscht** sagt, dass der Besucher das Häkchen gesetzt hat;
   bestätigt wird die Anmeldung im Newsletter-System.
4. **PDF-Bericht** zeigt, was der Besucher bekommen hat.
