---
titel: Was geprüft wird
kurzfassung: Die sieben benoteten Bereiche des Website-Checks und ihre Quellen, die Notenskala, die Server- und PHP-Prüfung ohne eigene Note und welche Seiten einer Website gelesen werden.
zielgruppe: anwender
sprache: de
stand: 2026-11-12
---

# Was geprüft wird

## Die sieben Bereiche

| Bereich | Quelle |
| --- | --- |
| **Ladezeit** | PageSpeed Insights, Handy und Desktop für die Startseite, bis zu zwei Unterseiten: LCP, CLS, TBT, INP (Felddaten), Seitengewicht, größtes Bild, blockierende Dateien, Bildformate, Antwortzeit des Servers |
| **Barrierefreiheit** | Lighthouse-Prüfungen zur Barrierefreiheit mit den betroffenen Elementen; Kontrastbefunde werden aus dem ganzseitigen Screenshot ausgeschnitten und im Bericht rot markiert |
| **Auffindbarkeit bei Google** | Lighthouse-SEO und eigene Prüfungen je Seite: Länge von Titel und Beschreibung, genau eine H1, Reihenfolge der Überschriften, Sitemap, kanonische Adresse, Weiterleitung von http auf https, strukturierte Daten |
| **Sichtbarkeit in KI-Suchen** | KI-Crawler (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, CCBot, Applebot-Extended) in `robots.txt` erlaubt (30 Punkte), Identität in strukturierten Daten (Organization, LocalBusiness …, 25), Angebot in den ersten Sätzen genannt (25), `llms.txt` (15), `llms-full.txt` (5). Eine `llms.txt` ist eine Empfehlung, keine Pflicht: Eine Website ohne sie erreicht die Note 2. |
| **Inhalte & Klarheit** | Die KI liest den sichtbaren Text und den Handy-Screenshot: Ist binnen fünf Sekunden klar, was angeboten wird, für wen, wo, und was der nächste Schritt ist? Stärken und Probleme in einfacher Sprache. Ohne KI-Verbindung: feste Regeln. |
| **Recht & Datenschutz** | Impressum und Datenschutzerklärung verlinkt, erreichbar und nicht leer. Der Inhalt der Rechtsseiten wird gelesen: Eine Datenschutzerklärung im Impressum zählt, wird aber schlechter bewertet als eine eigene Seite; verlinkte AGB ohne Text werden gemeldet. Einwilligungswerkzeug vorhanden; Tracking und eingebettete Inhalte vor der Einwilligung geladen (aus der Netzwerkliste von Lighthouse). Schriften von anderen Servern werden in der Netzwerkliste, im HTML, in Inline-Stilen und verlinkten Stylesheets (`@import`, `@font-face`) gefunden und gemeldet, mit oder ohne Einwilligungswerkzeug. Der Wortlaut sagt: technische Prüfung, keine Rechtsberatung. |
| **Mobil** | Viewport, Tippflächen, Schriftgröße, Inhaltsbreite |

Mit KI-Verbindung wählt die KI auch, welche markierten Ausschnitte der
Screenshots der Bericht zeigt, erklärt jeden in einem Satz und ergänzt bis zu
zwei Abschnitte zu Themen außerhalb der sieben Bereiche (z. B. Preise,
Buchung, Vertrauen); der PDF-Block *KI-Abschnitte* druckt nichts, wenn es
nichts zu ergänzen gibt.

## Noten

Jeder Bereich bekommt 0 bis 100 Punkte und eine Schulnote:

| Note | Punkte |
| --- | --- |
| 1 | 90 oder mehr |
| 2 | 78 oder mehr |
| 3 | 65 oder mehr |
| 4 | 50 oder mehr |
| 5 | unter 50 |

Die Gesamtnote ist der Durchschnitt der Bereiche, aber nie besser als der
schlechteste Bereich plus eins. Mit einer kritischen Serverwarnung (unten) ist
die Gesamtnote höchstens 4.

## Server und PHP (ohne eigene Note)

Der Check liest, welche PHP-Fassung der Webserver nennt: in den Kopfzeilen
`X-Powered-By` und `Server` jeder Seite, die er abruft (Fehlerseiten
eingeschlossen), und auf einer öffentlich erreichbaren `phpinfo()`-Seite unter
acht üblichen Dateinamen (`/phpinfo.php`, `/info.php`, `/php_info.php`,
`/phpinfo`, `/test.php`, `/i.php`, `/php.php`, `/pi.php`). Er liest nur
gewöhnliche Antworten; nichts wird angegriffen oder ausprobiert. Die Fassung
wird mit den PHP-Sicherheitswarnungen verglichen, die die Erweiterung
mitbringt (derzeit PHP2Shell, CVE-2026-109618 und CVE-2026-109619, behoben in
8.2.35, 8.3.36, 8.4.27 und 8.5.12), und mit der Sicherheitsunterstützung der
PHP-Zweige. Neue Warnungen kommen mit Updates der Erweiterung.

| Ergebnis | Bericht | Note |
| --- | --- | --- |
| Betroffene Fassung (die Nummer von PHP selbst, oder ein Zweig ohne Korrekturen wie 7.4 oder 8.1) | Kritische Warnung vor allem anderen, erste Maßnahme | Gesamtnote höchstens 4 |
| Fassung einer Linux-Distribution (Paketzusatz, oder eine Fassung, die Distributionen mit Langzeitpflege behalten und flicken, z. B. 8.3.6 von Ubuntu 24.04) | Warnung „das Update vom Hoster bestätigen lassen“ | unverändert |
| Zweig ohne Sicherheitsupdates, keine bekannte Lücke | Warnung | unverändert |
| Öffentliche `phpinfo()`-Seite | Warnung mit dem Pfad, Maßnahme „die Datei löschen“ | unverändert |
| Aktuelle Fassung | Ergebniszeile „aktuell“ | unverändert |
| Fassung verborgen, oder kein PHP gefunden | Ergebniszeile „nicht prüfbar“ mit den sicheren Fassungen | unverändert; das Verbergen (`expose_php = Off`) ist empfohlen |

Die Ergebnisseite zeigt vor der Note einen kurzen Hinweis; die vollständige
Warnung mit dem, was zu tun ist, steht im PDF und in beiden E-Mails.
Gestaltungen bekommen den Block *Server-Sicherheit*; eine Gestaltung ohne ihn
zeigt die Warnungen trotzdem nach dem Deckblatt (PDF) oder dem Kopf (E-Mail).

## Welche Seiten gelesen werden

Die Startseite plus bis zu vier wichtige Unterseiten aus Navigation und
Sitemap (Angebot, über uns, Kontakt, Rechtsseiten), höchstens **Seiten je
Check** (Vorgabe 5, höchstens 8). Eine Website mit 50 Seiten wird durch fünf
vertreten; so bleibt der Check bei etwa zwei Minuten.

Kann PageSpeed die Startseite nicht messen (die Website sperrt Googles Crawler,
braucht eine Anmeldung oder ist aus dem Internet nicht erreichbar), wird der
Schritt einmal wiederholt und dann übersprungen; der Bericht sagt es.
