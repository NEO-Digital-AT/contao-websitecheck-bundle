---
titel: Anpassen
kurzfassung: Den Website-Check an die Website anpassen – CSS-Variablen des Elements, Templates überschreiben und erweitern, Schriften, Bilder und Logos in PDF und E-Mail, die Tracking-Ereignisse und die Dienste für Entwickler.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Anpassen

Element, PDF und E-Mail kommen im neutralen Grunddesign. Das Element auf der
Seite übernimmt keine Farben aus dem Backend: Es wird nur über CSS-Variablen im
Stylesheet der Website gestaltet. PDF und E-Mail nehmen ihre Farben aus
**Hauptfarbe** und **Akzentfarbe** der Einstellungen und werden im Designer
gestaltet ([Bedienungsanleitung](usage/configurations.md#der-designer)).

## CSS-Variablen des Elements

Alle Variablen sitzen auf `.nwc`. Sie in einem Stylesheet der Website
überschreiben, zum Beispiel für ein dunkles Design:

```css
.content-neo-websitecheck .nwc {
    --nwc-bg: #102a43;
    --nwc-bg-2: #163a5c;
    --nwc-fg: #ffffff;
    --nwc-muted: #c9d6e3;
    --nwc-accent: #ffb000;
    --nwc-accent-ink: #102a43;
    --nwc-field-bg: rgb(0 0 0 / .3);
    --nwc-field-border: rgb(255 255 255 / .45);
    --nwc-radius: 22px;
}
```

| Variable | Vorgabe | Wofür |
| --- | --- | --- |
| `--nwc-bg`, `--nwc-bg-2` | `#ffffff`, `#ffffff` | Fläche, als Verlauf von `bg-2` nach `bg` |
| `--nwc-fg`, `--nwc-muted` | `#1f2328`, `#59636e` | Text, Nebentext (mindestens 4,5:1 auf der Fläche) |
| `--nwc-card`, `--nwc-border`, `--nwc-faint` | `#f6f8fa`, `#d1d9e0`, `rgba(31,35,40,.12)` | Karten, Rahmen, feine Linien |
| `--nwc-accent`, `--nwc-accent-ink` | `#1f2328`, `#ffffff` | Knöpfe, Fortschritt, Fokus; Text auf dem Akzent |
| `--nwc-ring`, `--nwc-glow` | `rgba(9,105,218,.3)`, `rgba(31,35,40,.25)` | Fokusrahmen, Schein des Fortschritts |
| `--nwc-field-bg`, `--nwc-field-border`, `--nwc-placeholder` | `#ffffff`, `#818b98`, `#6e7781` | Eingabefelder (Rahmen mindestens 3:1) |
| `--nwc-err` | `#cf222e` | Fehlertext und Rahmen eines ungültigen Felds |
| `--nwc-good`, `--nwc-warn`, `--nwc-bad`, `--nwc-grade-ink` | `#4ac26b`, `#d4a72c`, `#ff8182`, `#1f2328` | Notenfarben, Text auf der Note |
| `--nwc-alert-bg`, `--nwc-alert-line`, `--nwc-alert-ink`, `--nwc-alert-text`, `--nwc-alert-tag-ink` | `#fef3f2`, `#b42318`, `#7a271a`, `#1f2328`, `#ffffff` | kritische Serverwarnung |
| `--nwc-notice-bg`, `--nwc-notice-line`, `--nwc-notice-ink` | `#fffaeb`, `#b54708`, `#7a2e0e` | andere Serverwarnungen |
| `--nwc-sent-bg`, `--nwc-sent-border`, `--nwc-failed-bg`, `--nwc-failed-border` | `#dafbe1`, `#4ac26b`, `#ffebe9`, `#ff8182` | E-Mail gesendet / nicht gesendet |
| `--nwc-os-chrome`, `--nwc-os-ink`, `--nwc-os-shadow` | `#24292f`, `#d1d9e0`, Schatten | Browserrahmen der Vorschau |
| `--nwc-skel`, `--nwc-skel-hue`, `--nwc-idle` | `#e6e9ed`, `#c9d1d9`, `rgba(31,35,40,.66)` | Platzhalterseite und Überlagerung vor dem Check |
| `--nwc-check-icon` | weißes Häkchen als SVG | Häkchen fertiger Schritte (Farbe von `accent-ink`) |
| `--nwc-shadow` | `0 1px 3px rgba(31,35,40,.08)` | Schatten der Fläche |
| `--nwc-radius`, `--nwc-radius-s` | `16px`, `10px` | Ecken |
| `--nwc-font-display`, `--nwc-font-mono` | `inherit`, `ui-monospace, …` | Überschriften, Zahlen |

Text bei 4,5:1 und Feldrahmen bei 3:1 gegen die Fläche halten. Der Hinweis der
Gratisfassung nimmt seine Farben aus `--nwc-bg`, `--nwc-muted` und
`--nwc-border`; unleserliche Werte ersetzt er durch feste.

## Templates

Eine Datei aus `contao/templates/` der Erweiterung mit ihrem Pfad nach
`templates/` der Website kopieren und dort ändern.

| Template | Was es ausgibt |
| --- | --- |
| `content_element/neo_websitecheck.html.twig` | Das Element; die `data-nwc-*`-Attribute behalten, das Skript nutzt sie |
| `neo_websitecheck/blocks/pdf/<block>.html.twig`, `neo_websitecheck/blocks/mail/<block>.html.twig` | Einen Block des Designers, z. B. `blocks/pdf/cover.html.twig` |
| `neo_websitecheck/pdf_frame.html.twig`, `neo_websitecheck/mail_frame.html.twig` | Den Rahmen von PDF und E-Mail |
| `neo_websitecheck/mail_owner.html.twig` | Ihre Kopie der E-Mail |

Templates bekommen `result`, `areas`, `brand`, `images`, `crops`, `date` und
`vars`; Blöcke zusätzlich `block` (seine Eigenschaften) und `t` (das Design).
Ohne gespeicherte Gestaltung nutzen Bericht und E-Mail die mitgelieferte
Vorlage *Standard* in der Sprache des Checks.

Um nur Teile des Elements zu ändern, es erweitern und seine Blöcke
`nwc_intro`, `nwc_submit_button`, `nwc_gate_button` und `nwc_cta_buttons`
überschreiben:

```twig
{% extends "@Contao_NeoWebsitecheckBundle/content_element/neo_websitecheck.html.twig" %}

{% block nwc_submit_button %}
    <button type="submit" class="nwc__btn nwc__btn--primary"><span>Meine Website prüfen</span></button>
{% endblock %}
```

`type="submit"` an den Knöpfen und `data-nwc-reset` am Knopf zum Neustart
behalten.

## PDF: Schriften, Bilder, Zahlen in Kästen

Die Vorlagen des Berichts sind in HTML geschrieben, das Dompdf versteht
(Tabellen, kein Flexbox).

- **Eigene Schriften und Bilder**: Eine Vorlage kann TrueType-Schriften und
  Bilder mit relativem Pfad aus dem Dateiordner von Contao laden, z. B.
  `@font-face { font-family: 'Marke'; src: url('files/fonts/Marke-Regular.ttf'); }`
  oder `<img src="files/bilder/siegel.png">`. Statische `.ttf`-Dateien nutzen
  (kein WOFF2, keine variablen Schriften) und Normal und Fett getrennt
  anmelden.
- **Zahlen in farbigen Kästen**: das Makro `badge()` aus
  `_pdf_macros.html.twig` nutzen; Dompdf zentriert Text nur mit diesem Aufbau
  senkrecht.
- **Gotenberg** erzeugt dieselben Vorlagen mit Chromium (siehe
  [Einrichtung](setup.md#8-pdf-erzeugung)).

## Logos in E-Mails

Mailprogramme zeigen kein SVG. Neben ein SVG-Logo eine PNG-Datei gleichen
Namens legen (`logo.svg` → `logo.png`, etwa 600 px breit): E-Mails nutzen das
PNG, das PDF bleibt beim SVG. Ohne PNG zeigt die E-Mail statt des Logos den
Firmennamen.

## Texte

Die Wörter von E-Mail und PDF werden im Designer bearbeitet. Die
Standardwörter, die Texte der Befunde und des Backends stehen in
`translations/neo_websitecheck.<sprache>.yaml` der Erweiterung; eine Datei
gleichen Namens in `translations/` des Projekts überschreibt einzelne Schlüssel
(z. B. um die Befunde mit anderen Worten zu liefern). Den Text des
Newsletter-Häkchens setzt auch `newsletter.label` in `config.yaml` oder der
Schlüssel `fe.newsletter`.

## Tracking-Ereignisse

Das Skript löst das DOM-Ereignis `neo_websitecheck` auf `document` aus
(`event.detail = {name, params, element}`). Mit `tracking.data_layer: true`
(Vorgabe) schreibt es außerdem `{event: name, …params}` in `window.dataLayer`.

| Ereignis | Wann | Parameter |
| --- | --- | --- |
| `check_start` | Adresse abgeschickt | `domain`, `cached` |
| `check_gate` | Ergebnis fertig, Kontaktformular gezeigt | `domain` |
| `check_abandon` | Seite verlassen, während das Kontaktformular offen war | `domain` |
| `check_lead` | Kontaktformular abgeschickt | `domain`, `newsletter` (0/1), `contact` (0/1) |
| `check_result` | Ergebnis gezeigt | `domain`, `grade` |

Beispiel ohne Google Tag Manager, an Google Analytics nur nach Einwilligung:

```js
document.addEventListener('neo_websitecheck', function (e) {
    if (window.cookieConsent && window.cookieConsent.allows('statistics') && typeof gtag === 'function') {
        gtag('event', e.detail.name, e.detail.params);
    }
});
```

`window.cookieConsent.allows(…)` durch die Funktion des Einwilligungswerkzeugs
der Website ersetzen.

## Für Entwickler

Der Dienst `Neo\WebsitecheckBundle\Check\Grader` hält die Bewertung,
`Neo\WebsitecheckBundle\Check\CheckRunner` die Schritte eines Checks. Beide
lassen sich wie in Symfony üblich dekorieren oder ersetzen.
