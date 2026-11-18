// Screenshots of the documentation (docs/<lang>/bilder/*.png) in a test installation, marked with the NEO
// annotation layer. See README.md in this folder.
//
//   node capture.cjs <en|de>
//
// Environment: NB_BASE (default http://127.0.0.1:8000), NB_ADMIN_USER / NB_ADMIN_PASSWORD, NB_MYSQL (default
// "mysql contao"), NB_ANNOTATE (path to annotate.js of the plugin neo-doku), NB_CONFIG (id of the configuration
// whose PDF designer is shown, default 1).
const { chromium } = require('playwright');
const { execSync } = require('child_process');
const path = require('path');

const lang = process.argv[2] || 'en';
const env = (name, fallback) => process.env[name] || fallback;
const base = env('NB_BASE', 'http://127.0.0.1:8000');
const user = env('NB_ADMIN_USER', 'admin');
const password = env('NB_ADMIN_PASSWORD', '');
const mysql = env('NB_MYSQL', 'mysql contao');
const configId = Number(env('NB_CONFIG', '1'));
const out = path.resolve(__dirname, '../../docs', lang, 'bilder');
const annotate = env('NB_ANNOTATE', path.resolve(__dirname, '../../../NEO-Claude-Plugins/plugins/neo-doku/scripts/annotate.js'));
const sql = (query) => execSync(mysql + ' --default-character-set=utf8mb4 -N --raw -e "' + query.replace(/"/g, '\\"') + '"').toString().replace(/\r?\n$/, '');
const quote = (value) => "'" + String(value).replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'";
// Ordinary desktop browser: the check turns automated clients away.
const UA = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36';

// Neutral names per language.
const TEXT = { en: { brand: 'Example Ltd', config: 'English' }, de: { brand: 'Beispiel GmbH', config: 'Deutsch' } }[lang];

// An empty box in document coordinates for neoAnnotate.frame().
const BOX = `window.docBox = function (left, top, right, bottom) {
  var el = document.createElement('div');
  el.style.cssText = 'position:absolute;pointer-events:none;left:' + (left + scrollX) + 'px;top:' + (top + scrollY) + 'px;width:' + (right - left) + 'px;height:' + (bottom - top) + 'px';
  document.body.appendChild(el);
  return el;
};`;

async function layer(page) {
  await page.addStyleTag({ content: '.sf-toolbar,.sf-minitoolbar{display:none!important}' });
  await page.evaluate(() => document.fonts.ready);
  await page.addScriptTag({ path: annotate });
  await page.addScriptTag({ content: BOX });
}

(async () => {
  if (!password || !TEXT) {
    console.error('Set NB_ADMIN_PASSWORD; language en or de.');
    process.exit(2);
  }
  const language = sql("SELECT language FROM tl_user WHERE username = '" + user + "'");
  const rootLanguage = sql("SELECT language FROM tl_page WHERE type = 'root' ORDER BY sorting LIMIT 1");
  const brand = sql('SELECT brand_name FROM tl_neo_websitecheck_settings ORDER BY id LIMIT 1');
  const [title, configLanguage] = sql(`SELECT CONCAT_WS(CHAR(9), title, language) FROM tl_neo_websitecheck_config WHERE id = ${configId}`).split('\t');
  sql("UPDATE tl_user SET language = '" + lang + "' WHERE username = '" + user + "'");
  sql(`UPDATE tl_page SET language = '${lang}' WHERE type = 'root'`);
  sql(`UPDATE tl_neo_websitecheck_settings SET brand_name = ${quote(TEXT.brand)}`);
  sql(`UPDATE tl_neo_websitecheck_config SET title = ${quote(TEXT.config)}, language = '${lang}' WHERE id = ${configId}`);

  const browser = await chromium.launch();
  const options = { deviceScaleFactor: 2, reducedMotion: 'reduce', colorScheme: 'light', locale: lang === 'de' ? 'de-AT' : 'en-GB', timezoneId: 'Europe/Vienna' };

  // 1. The element on the page, as a visitor sees it before a check.
  const visitor = await (await browser.newContext({ ...options, viewport: { width: 1280, height: 900 }, userAgent: UA })).newPage();
  await visitor.goto(base + '/', { waitUntil: 'networkidle' });
  // The test website has no layout of its own: a font and a margin, as any website gives the element.
  await visitor.addStyleTag({ content: 'body{margin:0;padding:24px 48px;font-family:Arial,Helvetica,sans-serif}' });
  await visitor.waitForTimeout(800);
  await layer(visitor);
  await visitor.evaluate(() => {
    const frame = (el, number, pad = 6) => {
      const r = el.getBoundingClientRect();
      neoAnnotate.frame(docBox(r.left - pad, r.top - pad, r.right + pad, r.bottom + pad), { number, padding: 0 });
    };
    const main = document.querySelector('.nwc__main');
    // Label and address field with the button, as one frame.
    const label = main.querySelector('.nwc__label').getBoundingClientRect();
    const bar = main.querySelector('.nwc__bar').getBoundingClientRect();
    neoAnnotate.frame(docBox(bar.left - 22, label.top - 10, bar.right + 6, bar.bottom + 6), { number: 1, padding: 0 });
    frame(document.querySelector('.nwc__screen'), 2);
    // The credit line of the free edition: the last element of the main column (a closed shadow root).
    frame(main.lastElementChild, 3, 4);
  });
  const element = await visitor.locator('.nwc').first().boundingBox();
  await visitor.screenshot({ path: path.join(out, 'element-front.png'), clip: { x: element.x - 24, y: element.y - 24, width: element.width + 48, height: element.height + 28 } });

  const context = await browser.newContext({ ...options, viewport: { width: 1280, height: 800 } });
  const page = await context.newPage();
  await page.goto(base + '/contao/login');
  await page.fill('input[name="username"]', user);
  await page.fill('input[name="password"]', password);
  await Promise.all([page.waitForNavigation(), page.click('button[type="submit"]')]);

  // 2. The list of checks: filter and search, the operations of a check.
  await page.setViewportSize({ width: 1440, height: 800 });
  await page.goto(base + '/contao?do=neo_websitecheck', { waitUntil: 'networkidle' });
  await layer(page);
  const list = await page.evaluate(() => {
    const frame = (el, number, pad = 4) => {
      const r = el.getBoundingClientRect();
      neoAnnotate.frame(docBox(r.left - pad, r.top - pad, r.right + pad, r.bottom + pad), { number, padding: 0 });
    };
    const table = document.querySelector('.tl_listing').getBoundingClientRect();
    // The search and filter panel, as far as it is in the picture.
    const panel = document.querySelector('.tl_panel').getBoundingClientRect();
    neoAnnotate.frame(docBox(panel.left - 2, panel.top - 2, panel.right + 2, Math.min(panel.bottom, table.bottom + 14)), { number: 1, padding: 0 });
    const row = [...document.querySelectorAll('.tl_listing tr')].find((tr) => tr.querySelector('td'));
    frame(row.querySelector('td:last-child'), 2);
    frame(document.querySelector('.header_settings'), 3);
    return { left: document.querySelector('#main').getBoundingClientRect().left, bottom: table.bottom + scrollY };
  });
  const x = Math.max(0, list.left - 12);
  await page.screenshot({ path: path.join(out, 'checks-list.png'), clip: { x, y: 60, width: 1440 - x, height: Math.ceil(list.bottom) - 60 + 24 } });

  // 3. The PDF designer of a configuration: blocks, preview, properties.
  await page.goto(base + `/contao?do=neo_websitecheck_settings&table=tl_neo_websitecheck_config&act=edit&id=${configId}`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2500);
  const designer = page.locator('#pal_pdf_design_legend .nwd').first();
  // The standard template in the language of the configuration (loaded, not saved: the form is left unsaved).
  page.on('dialog', (dialog) => dialog.accept());
  await designer.locator('.nwd-toolbar .nwd-row .nwd-btn').first().click();
  await page.waitForTimeout(2500);
  // Tab "Structure", then a block: the properties pane shows its fields.
  await designer.locator('.nwd-tab[data-tab="structure"]').click();
  await designer.locator('.nwd-struct__item').nth(3).click();
  await page.waitForTimeout(1500);
  // Viewport as tall as the page: no sticky bar covers the designer.
  await page.setViewportSize({ width: 1440, height: await page.evaluate(() => document.documentElement.scrollHeight) });
  await page.waitForTimeout(800);
  await layer(page);
  await page.evaluate(() => {
    const d = document.querySelector('#pal_pdf_design_legend .nwd');
    const frame = (el, number) => {
      const r = el.getBoundingClientRect();
      // A little outside the pane, so the number covers none of its text.
      neoAnnotate.frame(docBox(r.left - 12, r.top - 2, r.right + 2, r.bottom - 3), { number, padding: 0 });
    };
    frame(d.querySelector('.nwd-pane--left'), 1);
    frame(d.querySelector('.nwd-preview'), 2);
    frame(d.querySelector('.nwd-pane--props'), 3);
  });
  const box = await designer.boundingBox();
  await page.screenshot({ path: path.join(out, 'designer.png'), clip: { x: box.x - 28, y: box.y - 8, width: box.width + 36, height: Math.min(box.height, 720) + 16 } });

  await browser.close();
  sql(`UPDATE tl_neo_websitecheck_config SET title = ${quote(title)}, language = ${quote(configLanguage)} WHERE id = ${configId}`);
  sql(`UPDATE tl_neo_websitecheck_settings SET brand_name = ${quote(brand)}`);
  sql(`UPDATE tl_page SET language = '${rootLanguage}' WHERE type = 'root'`);
  sql("UPDATE tl_user SET language = '" + language + "' WHERE username = '" + user + "'");
  console.log(['element-front.png', 'checks-list.png', 'designer.png'].map((f) => path.join(out, f)).join(' '));
})();
