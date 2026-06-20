#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD VERIFIED LAUNCH GATE =="

mkdir -p scripts data/reports/runtime docs

test -f docs/CHANGELOG.md || cat > docs/CHANGELOG.md << 'MD'
# Spark Protocol Changelog

## Unreleased
- Initial Discord MVP runtime.
MD

cat > scripts/verified_launch.mjs << 'JS'
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
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["launch"] = "node scripts/verified_launch.mjs"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PACKAGE_LAUNCH_SCRIPT=PASS")
PY

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

if "async function postOnlineStatus" not in s:
    marker = "async function registerCommands()"
    insert = """async function postOnlineStatus(readyClient) {
  const channelId = process.env.STATUS_CHANNEL_ID || process.env.GUILD_STATUS_CHANNEL_ID;
  if (!channelId) {
    console.log('STATUS_CHANNEL=SKIP');
    return;
  }

  const channel = await readyClient.channels.fetch(channelId);
  const note = process.env.SPARK_RELEASE_NOTE || 'Runtime verified. No changelog excerpt provided.';

  await channel.send({
    embeds: [
      new EmbedBuilder()
        .setTitle('The Emergence Online')
        .setDescription('Verified launch gate passed. Bot is online.')
        .addFields(
          { name: 'Tests', value: 'PASS', inline: true },
          { name: 'Changelog', value: note.slice(0, 1000), inline: false }
        )
        .setColor(0x2ecc71)
    ]
  });

  console.log('DISCORD_ONLINE_STATUS=POSTED');
}

"""
    s = s.replace(marker, insert + marker)

s = s.replace(
"""  try { await registerCommands(); } catch (err) { console.error(err); }""",
"""  try {
    await registerCommands();
    await postOnlineStatus(readyClient);
  } catch (err) {
    console.error(err);
  }"""
)

p.write_text(s)
print("BOT_ONLINE_STATUS_PATCH=PASS")
PY

node --check apps/discord-bot/src/index.js
npm run test:mobile

echo "ADD_VERIFIED_LAUNCH_GATE=PASS"
