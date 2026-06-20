import { chromium } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const TARGET = process.env.MASKZERO_TARGET || 'https://maskzero.site';
const OUT = process.env.MASKZERO_OUT || 'runtime/reports/maskzero_site_audit_manual';
const pagesToCheck = [
  '/',
  '/spark-signup/',
  '/spark-login/',
  '/spark-generator/',
  '/spark-dashboard/',
  '/quiz/',
  '/missions/',
  '/spark/',
];

fs.mkdirSync(path.join(OUT, 'screenshots'), { recursive: true });

const report = {
  target: TARGET,
  generated: new Date().toISOString(),
  pages: [],
  linkChecks: [],
  theme: {
    sharedNavFound: [],
    missingSharedNav: [],
    legacyBranding: [],
  },
  forms: [],
  verdict: 'PASS',
  failures: [],
};

function markFail(msg) {
  report.verdict = 'FAIL';
  report.failures.push(msg);
}

function safeName(urlPath) {
  return urlPath.replaceAll('/', '_').replace(/^_$/, 'home') || 'home';
}

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext({
  viewport: { width: 390, height: 844 },
  deviceScaleFactor: 2,
  isMobile: true,
});

for (const route of pagesToCheck) {
  const page = await context.newPage();
  const url = new URL(route, TARGET).toString();
  const record = {
    route,
    url,
    status: null,
    title: null,
    h1: null,
    links: [],
    buttons: [],
    inputs: [],
    hasSharedNav: false,
    hasMaskzeroThemeCss: false,
    hasLegacyDadudekcBranding: false,
    screenshot: null,
    errors: [],
  };

  page.on('pageerror', err => record.errors.push(`pageerror: ${err.message}`));
  page.on('console', msg => {
    if (['error', 'warning'].includes(msg.type())) {
      record.errors.push(`console:${msg.type()}: ${msg.text()}`);
    }
  });

  try {
    const response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 20000 });
    record.status = response?.status() ?? null;

    await page.waitForTimeout(1000);

    record.title = await page.title().catch(() => null);
    record.h1 = await page.locator('h1').first().innerText({ timeout: 2000 }).catch(() => null);

    record.links = await page.locator('a').evaluateAll(nodes =>
      nodes.map(a => ({
        text: (a.innerText || a.textContent || '').trim(),
        href: a.href || '',
      })).filter(x => x.text || x.href)
    ).catch(() => []);

    record.buttons = await page.locator('button, input[type="submit"], [role="button"]').evaluateAll(nodes =>
      nodes.map(b => ({
        text: (b.innerText || b.value || b.textContent || '').trim(),
        type: b.getAttribute('type') || b.tagName.toLowerCase(),
      }))
    ).catch(() => []);

    record.inputs = await page.locator('input, textarea, select').evaluateAll(nodes =>
      nodes.map(i => ({
        name: i.getAttribute('name') || '',
        type: i.getAttribute('type') || i.tagName.toLowerCase(),
        required: i.hasAttribute('required'),
      }))
    ).catch(() => []);

    const html = await page.content();
    record.hasSharedNav =
      html.includes('data-mz-nav') ||
      html.includes('maskzero-nav') ||
      html.includes('mz-nav');

    record.hasMaskzeroThemeCss =
      html.includes('maskzero-theme.css') ||
      html.includes('data-maskzero-theme') ||
      html.includes('mz-');

    record.hasLegacyDadudekcBranding =
      html.toLowerCase().includes('dadudekc.site');

    if (record.hasSharedNav) report.theme.sharedNavFound.push(route);
    else report.theme.missingSharedNav.push(route);

    if (record.hasLegacyDadudekcBranding) {
      report.theme.legacyBranding.push(route);
    }

    const shot = path.join(OUT, 'screenshots', `${safeName(route)}.png`);
    await page.screenshot({ path: shot, fullPage: true });
    record.screenshot = shot;

    if (!record.status || record.status >= 400) {
      markFail(`${route} returned bad status: ${record.status}`);
    }

    if (record.errors.length) {
      markFail(`${route} has browser console/page errors`);
    }
  } catch (err) {
    record.errors.push(String(err.message || err));
    markFail(`${route} failed to load: ${err.message || err}`);
  }

  report.pages.push(record);
  await page.close();
}

const checkPage = await context.newPage();
for (const pageRecord of report.pages) {
  for (const link of pageRecord.links) {
    if (!link.href.startsWith(TARGET)) continue;
    try {
      const res = await checkPage.request.get(link.href, { timeout: 15000 });
      const status = res.status();
      report.linkChecks.push({
        from: pageRecord.route,
        text: link.text,
        href: link.href,
        status,
      });
      if (status >= 400) {
        markFail(`Broken internal link from ${pageRecord.route}: ${link.href} status=${status}`);
      }
    } catch (err) {
      report.linkChecks.push({
        from: pageRecord.route,
        text: link.text,
        href: link.href,
        status: 'ERROR',
        error: String(err.message || err),
      });
      markFail(`Internal link error from ${pageRecord.route}: ${link.href}`);
    }
  }
}
await checkPage.close();

for (const pageRecord of report.pages) {
  if (pageRecord.inputs.length || pageRecord.buttons.length) {
    report.forms.push({
      route: pageRecord.route,
      inputs: pageRecord.inputs,
      buttons: pageRecord.buttons,
    });
  }
}

if (report.theme.missingSharedNav.length) {
  markFail(`Theme/nav missing on: ${report.theme.missingSharedNav.join(', ')}`);
}

if (report.theme.legacyBranding.length) {
  markFail(`Legacy dadudekc.site branding found on: ${report.theme.legacyBranding.join(', ')}`);
}

await browser.close();

fs.writeFileSync(path.join(OUT, 'maskzero_site_audit.json'), JSON.stringify(report, null, 2));

const md = [
  '# MaskZero Site Audit',
  '',
  `target: ${report.target}`,
  `generated: ${report.generated}`,
  `verdict: ${report.verdict}`,
  '',
  '## Failures',
  ...(report.failures.length ? report.failures.map(x => `- ${x}`) : ['- none']),
  '',
  '## Pages',
  '| Route | Status | H1 | Shared Nav | Theme CSS | Legacy Branding | Errors |',
  '|---|---:|---|---:|---:|---:|---:|',
  ...report.pages.map(p =>
    `| ${p.route} | ${p.status ?? 'ERR'} | ${(p.h1 || '').replaceAll('|', '/')} | ${p.hasSharedNav ? 'yes' : 'no'} | ${p.hasMaskzeroThemeCss ? 'yes' : 'no'} | ${p.hasLegacyDadudekcBranding ? 'yes' : 'no'} | ${p.errors.length} |`
  ),
  '',
  '## Internal Link Checks',
  '| From | Text | Status | Href |',
  '|---|---|---:|---|',
  ...report.linkChecks.map(l =>
    `| ${l.from} | ${(l.text || '').replaceAll('|', '/')} | ${l.status} | ${l.href} |`
  ),
  '',
  '## Forms / Buttons',
  '```json',
  JSON.stringify(report.forms, null, 2),
  '```',
].join('\n');

fs.writeFileSync(path.join(OUT, 'maskzero_site_audit.md'), md);
console.log(md);
console.log('');
console.log(`AUDIT_DIR=${OUT}`);
console.log(`AUDIT_JSON=${path.join(OUT, 'maskzero_site_audit.json')}`);
console.log(`AUDIT_MD=${path.join(OUT, 'maskzero_site_audit.md')}`);
