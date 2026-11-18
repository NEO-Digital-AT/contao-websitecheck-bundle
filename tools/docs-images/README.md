# Documentation images

The images in `docs/<lang>/bilder/` are screenshots of a test installation,
marked with the NEO annotation layer (frames and numbers): the element on a
page before a check (`element-front.png`), the list of checks
(`checks-list.png`) and the PDF designer of a configuration (`designer.png`).
`capture.cjs` takes them again, for example after a change to the element, the
list or the designer. This folder is not part of the Composer package
(`.gitattributes`: `export-ignore`). `../credit-wordmark/` makes the PNG of the
credit line in the e-mail.

## What the test installation needs

- Contao with this bundle, the content element **Website check** on the start
  page, no licence (the element shows the credit line).
- A PageSpeed key on *Access keys and licences* (any value; without one the
  list shows a warning on top).
- A few checks in the list without real data:
  `php vendor/bin/contao-console neo:websitecheck:demo --locale=en --firstname=John --lastname=Smith --email=john.smith@example.com`,
  the same with `--locale=de --firstname=Max --lastname=Mustermann --email=max.mustermann@example.com`.
- A configuration (its id in `NB_CONFIG`).
- An administrator (`NB_ADMIN_USER`).
- Node.js with Playwright and its Chromium, and the plugin `neo-doku` of
  `NEO-Claude-Plugins` (`annotate.js`) next to this repository, or its path in
  `NB_ANNOTATE`.

## Taking the images

```bash
NB_ADMIN_PASSWORD=… node tools/docs-images/capture.cjs en
NB_ADMIN_PASSWORD=… node tools/docs-images/capture.cjs de
```

The script sets the back-end language of the administrator, the language of
the website root and of the configuration to the language of the images, gives
the settings and the configuration neutral names ("Example Ltd" / "Beispiel
GmbH") and restores all of it at the end. In the designer it loads the standard
template of that language without saving it. Do not run two captures at the
same time.

| Variable | Default | Meaning |
| --- | --- | --- |
| `NB_BASE` | `http://127.0.0.1:8000` | Address of the test installation |
| `NB_ADMIN_USER`, `NB_ADMIN_PASSWORD` | `admin`, – | Administrator |
| `NB_MYSQL` | `mysql contao` | Command for the database of the test installation |
| `NB_CONFIG` | `1` | Id of the configuration whose PDF designer is shown |
| `NB_ANNOTATE` | `../NEO-Claude-Plugins/plugins/neo-doku/scripts/annotate.js` | Annotation layer |

Check every image after taking it: frames on the right places, no real names,
addresses or keys.
