#!/usr/bin/env node

import { createRequire } from 'module';

const require = createRequire(import.meta.url);

if (process.platform === 'android') {
  console.error('PLAYWRIGHT_PLATFORM=ENV_BLOCKED_ANDROID');
  console.error('Run this smoke from Linux, WSL, macOS, Windows, or GitHub Actions. Termux/Android cannot launch Playwright Chromium.');
  process.exit(12);
}

let chromium;
try {
  ({ chromium } = require('playwright'));
} catch (error) {
  console.error('PLAYWRIGHT_IMPORT=FAIL');
  console.error('Install on a supported runner: npm install --no-save playwright && npx playwright install chromium');
  process.exit(12);
}

const URL = 'https://maskzero.site/character-generator/?dreamos_smoke=112';

const FORBIDDEN_VISIBLE = [
  'raw scores',
  'manifest_threshold_exported',
  'flavor_vectors_exported',
  'showwork',
  'odds:',
  'raw_roll'
];

function requirePass(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

async function visibleText(page) {
  return await page.locator('body').innerText({ timeout: 15000 });
}

async function assertNoVisibleLeaks(page, label) {
  const text = (await visibleText(page)).toLowerCase();
  const leaks = FORBIDDEN_VISIBLE.filter((marker) => text.includes(marker));
  requirePass(leaks.length === 0, `${label} leaked visible markers: ${leaks.join(', ')}`);
}

async function fillVisibleControls(page) {
  const filled = {
    radios: 0,
    selects: 0,
    textareas: 0,
    textInputs: 0,
    checked: 0
  };

  const selects = await page.locator('select:visible').all();
  for (const select of selects) {
    const options = await select.locator('option').evaluateAll((nodes) =>
      nodes
        .map((node) => node.getAttribute('value') || node.textContent || '')
        .filter((value) => value && value.trim() !== '')
    );

    if (options.length) {
      await select.selectOption(options[Math.min(1, options.length - 1)]);
      filled.selects += 1;
    }
  }

  const radioGroups = await page.locator('input[type="radio"]').evaluateAll((nodes) => {
    const names = new Set();
    for (const node of nodes) {
      const style = window.getComputedStyle(node);
      const rect = node.getBoundingClientRect();
      if (style.display !== 'none' && style.visibility !== 'hidden' && rect.width >= 0 && rect.height >= 0) {
        names.add(node.getAttribute('name') || node.getAttribute('id') || '');
      }
    }
    return Array.from(names).filter(Boolean);
  });

  for (const name of radioGroups) {
    const group = page.locator(`input[type="radio"][name="${name.replace(/"/g, '\\"')}"]`);
    const count = await group.count();
    if (count > 0) {
      await group.nth(Math.min(1, count - 1)).check({ force: true });
      filled.radios += 1;
    }
  }

  const checkboxes = await page.locator('input[type="checkbox"]:visible').all();
  for (const checkbox of checkboxes.slice(0, 10)) {
    await checkbox.check({ force: true });
    filled.checked += 1;
  }

  const textInputs = await page.locator('input:visible').all();
  for (const input of textInputs) {
    const type = (await input.getAttribute('type')) || 'text';
    if (!['text', 'search', 'email', 'url'].includes(type.toLowerCase())) {
      continue;
    }

    const id = ((await input.getAttribute('id')) || '').toLowerCase();
    const name = ((await input.getAttribute('name')) || '').toLowerCase();
    const placeholder = ((await input.getAttribute('placeholder')) || '').toLowerCase();

    if (id.includes('costume') || name.includes('costume') || placeholder.includes('costume')) {
      await input.fill('armored hooded suit with luminous prism seams');
      filled.textInputs += 1;
    } else if (id.includes('personality') || name.includes('personality') || placeholder.includes('personality')) {
      await input.fill('stoic protector with calm controlled presence');
      filled.textInputs += 1;
    } else if (id.includes('name') || name.includes('name') || placeholder.includes('name') || id.includes('alias') || name.includes('alias')) {
      await input.fill('The Prism Warden');
      filled.textInputs += 1;
    }
  }

  const textareas = await page.locator('textarea:visible').all();
  for (const textarea of textareas.slice(0, 4)) {
    const readonly = await textarea.getAttribute('readonly');
    if (readonly !== null) {
      continue;
    }

    await textarea.fill('A controlled heroic presence with visible hard-light effects.');
    filled.textareas += 1;
  }

  return filled;
}

async function answeredControlCount(page) {
  return await page.evaluate(() => {
    let count = 0;

    document.querySelectorAll('input, select, textarea').forEach((field) => {
      if (field.type === 'radio' || field.type === 'checkbox') {
        if (field.checked) count += 1;
        return;
      }

      if ((field.value || '').trim()) {
        count += 1;
      }
    });

    return count;
  });
}

async function findScanButton(page) {
  const candidates = [
    /scan spark/i,
    /scan/i,
    /continue/i,
    /unlock/i,
    /next/i,
    /submit/i,
    /generate/i
  ];

  for (const pattern of candidates) {
    const button = page.getByRole('button', { name: pattern }).first();
    if (await button.count()) {
      if (await button.isVisible().catch(() => false)) {
        return button;
      }
    }
  }

  const fallback = page.locator('button:visible, input[type="button"]:visible, input[type="submit"]:visible').first();
  requirePass(await fallback.count(), 'no visible action button found');
  return fallback;
}

async function passTwoAppears(page, beforeText) {
  await page.waitForTimeout(2500);

  const after = await visibleText(page);
  const lower = after.toLowerCase();

  const markers = [
    'pass 2',
    'follow-up',
    'follow up',
    'unlocked',
    'flavor',
    'totality observation',
    'create final dossier',
    'spark pattern',
    'dossier'
  ];

  const markerHit = markers.some((marker) => lower.includes(marker));
  const grew = after.length > beforeText.length + 80;

  return { markerHit, grew, after };
}

async function main() {
  console.log('== LAUNCH BROWSER ==');
  const browser = await chromium.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-dev-shm-usage']
  });

  const context = await browser.newContext({
    viewport: { width: 1366, height: 1200 },
    userAgent: 'DreamOS-Playwright-ScanNoReload/1.0'
  });

  await context.addInitScript(() => {
    window.__dreamosReloadFlags = {
      beforeunload: false,
      pagehide: false,
      unload: false
    };

    window.addEventListener('beforeunload', () => { window.__dreamosReloadFlags.beforeunload = true; });
    window.addEventListener('pagehide', () => { window.__dreamosReloadFlags.pagehide = true; });
    window.addEventListener('unload', () => { window.__dreamosReloadFlags.unload = true; });
  });

  const page = await context.newPage();

  let mainFrameNavigations = 0;
  page.on('framenavigated', (frame) => {
    if (frame === page.mainFrame()) {
      mainFrameNavigations += 1;
    }
  });

  console.log('== OPEN CHARACTER PAGE ==');
  await page.goto(URL, { waitUntil: 'networkidle', timeout: 60000 });
  const initialUrl = page.url();
  console.log(`INITIAL_URL=${initialUrl}`);
  requirePass(mainFrameNavigations >= 1, 'initial navigation not counted');

  await assertNoVisibleLeaks(page, 'initial page');

  console.log('== FILL VISIBLE SCAN CONTROLS ==');
  const filled = await fillVisibleControls(page);
  const answeredBefore = await answeredControlCount(page);

  console.log(`FILLED_RADIOS=${filled.radios}`);
  console.log(`FILLED_SELECTS=${filled.selects}`);
  console.log(`FILLED_TEXT_INPUTS=${filled.textInputs}`);
  console.log(`FILLED_TEXTAREAS=${filled.textareas}`);
  console.log(`ANSWERED_BEFORE=${answeredBefore}`);

  requirePass(answeredBefore > 0, 'no answers were filled before scan');

  const beforeText = await visibleText(page);
  const navCountBeforeClick = mainFrameNavigations;

  console.log('== CLICK SCAN / CONTINUE BUTTON ==');
  const button = await findScanButton(page);
  const buttonText = await button.innerText().catch(async () => await button.getAttribute('value') || '');
  console.log(`CLICKED_BUTTON=${buttonText.replace(/\s+/g, ' ').trim()}`);

  await button.click({ timeout: 20000 });

  const passTwo = await passTwoAppears(page, beforeText);
  const finalUrl = page.url();
  const navCountAfterClick = mainFrameNavigations;
  const answeredAfter = await answeredControlCount(page);
  const flags = await page.evaluate(() => window.__dreamosReloadFlags || {});

  console.log(`FINAL_URL=${finalUrl}`);
  console.log(`MAIN_FRAME_NAV_BEFORE_CLICK=${navCountBeforeClick}`);
  console.log(`MAIN_FRAME_NAV_AFTER_CLICK=${navCountAfterClick}`);
  console.log(`ANSWERED_AFTER=${answeredAfter}`);
  console.log(`RELOAD_FLAGS=${JSON.stringify(flags)}`);

  requirePass(finalUrl === initialUrl, `URL changed after scan click: ${initialUrl} -> ${finalUrl}`);
  requirePass(navCountAfterClick === navCountBeforeClick, 'main frame navigation occurred after scan click');
  requirePass(flags.beforeunload !== true && flags.pagehide !== true && flags.unload !== true, 'reload/unload flag fired after scan click');
  requirePass(answeredAfter >= Math.max(1, Math.floor(answeredBefore * 0.6)), `answers were not preserved: before=${answeredBefore} after=${answeredAfter}`);
  requirePass(passTwo.markerHit || passTwo.grew, 'pass 2 / follow-up content did not appear after scan click');

  await assertNoVisibleLeaks(page, 'after scan click');

  console.log('SCAN_CLICK_NO_URL_CHANGE=PASS');
  console.log('SCAN_CLICK_NO_MAIN_FRAME_RELOAD=PASS');
  console.log('SCAN_CLICK_NO_UNLOAD_FLAG=PASS');
  console.log('SCAN_ANSWERS_PRESERVED=PASS');
  console.log('SCAN_PASS_TWO_APPEARS=PASS');
  console.log('SCAN_NO_RAW_SCORE_LEAK=PASS');
  console.log('EMERGENCE_SCAN_NO_RELOAD_BROWSER_SMOKE=PASS');

  await browser.close();
}

main().catch((error) => {
  console.error(error && error.stack ? error.stack : String(error));
  process.exit(1);
});
