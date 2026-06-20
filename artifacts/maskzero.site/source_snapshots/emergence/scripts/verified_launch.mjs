import 'dotenv/config';
import fs from 'node:fs';
import path from 'node:path';
import { spawnSync, spawn } from 'node:child_process';

const root = process.cwd();
const reportDir = path.join(root, 'data/reports/runtime');
fs.mkdirSync(reportDir, { recursive: true });

const changelog = path.join(root, 'docs/CHANGELOG.md');
const report = path.join(reportDir, 'verified_launch_latest.json');

function fail(reason, extra = {}) {
  fs.writeFileSync(report, JSON.stringify({
    ok: false,
    reason,
    ...extra,
    at: new Date().toISOString()
  }, null, 2) + '\n');

  console.error(`VERIFIED_LAUNCH=FAIL`);
  console.error(`REASON=${reason}`);
  process.exit(1);
}

if (!fs.existsSync(changelog)) {
  fail('missing_changelog');
}

const changelogText = fs.readFileSync(changelog, 'utf8');
if (!changelogText.includes('## Unreleased') || changelogText.trim().split('\n').length < 4) {
  fail('changelog_not_updated');
}

console.log('== TEST GATE ==');
const test = spawnSync('npm', ['run', 'test:mobile'], {
  cwd: root,
  stdio: 'inherit',
  shell: true
});

if (test.status !== 0) {
  fail('tests_failed', { exit_code: test.status });
}

const releaseNote = changelogText
  .split('## Unreleased')[1]
  ?.split('## ')[0]
  ?.trim()
  ?.slice(0, 1500) || 'No release notes found.';

fs.writeFileSync(report, JSON.stringify({
  ok: true,
  reason: 'tests_passed',
  changelog_excerpt: releaseNote,
  at: new Date().toISOString()
}, null, 2) + '\n');

console.log('VERIFIED_LAUNCH_TESTS=PASS');
console.log('CHANGELOG=PASS');
console.log('STARTING_BOT=PASS');

const child = spawn('node', ['apps/discord-bot/src/index.js'], {
  cwd: root,
  stdio: 'inherit',
  shell: true,
  env: {
    ...process.env,
    SPARK_RELEASE_NOTE: releaseNote
  }
});

child.on('exit', code => process.exit(code ?? 0));
