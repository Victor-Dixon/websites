#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== DIAGNOSE DISCORD BUTTON RUNTIME =="

mkdir -p data/reports/discord_runtime

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

if "function runtimeLog(event)" not in s:
    marker = "const sessions = new Map();"
    insert = r"""
const RUNTIME_LOG = path.join(repoRoot(), 'data/reports/discord_runtime/button_runtime.jsonl');

function runtimeLog(event) {
  fs.mkdirSync(path.dirname(RUNTIME_LOG), { recursive: true });
  fs.appendFileSync(RUNTIME_LOG, JSON.stringify({
    t: new Date().toISOString(),
    ...event
  }) + '\n');
}
"""
    s = s.replace(marker, marker + "\n" + insert)

s = s.replace(
"const result = reduceSession(session, event);",
"""runtimeLog({
    stage: 'before_reduce',
    customId: interaction.customId,
    payload,
    event,
    session: {
      sessionId: session.sessionId,
      userId: session.userId,
      viewVersion: session.viewVersion,
      cursor: session.cursor,
      activeQueue: session.activeQueue,
      responses: session.responses
    }
  });

  const result = reduceSession(session, event);

  runtimeLog({
    stage: 'after_reduce',
    outcome: result.outcome,
    session: {
      sessionId: result.session.sessionId,
      userId: result.session.userId,
      viewVersion: result.session.viewVersion,
      cursor: result.session.cursor,
      activeQueue: result.session.activeQueue,
      responses: result.session.responses
    }
  });"""
)

p.write_text(s)
print("RUNTIME_LOG_PATCH=PASS")
PY

node --check apps/discord-bot/src/index.js
npm run test:stale-buttons

echo "DIAGNOSE_DISCORD_BUTTON_RUNTIME=PASS"
