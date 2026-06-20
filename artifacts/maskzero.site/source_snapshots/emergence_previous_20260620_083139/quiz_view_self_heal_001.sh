#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== QUIZ VIEW SELF HEAL =="

python - << 'PY'
from pathlib import Path

p = Path("packages/quiz-session/index.js")
s = p.read_text()

s = s.replace(
"""updated_at: new Date().toISOString()""",
"""updated_at: new Date().toISOString(),
    view_version: 0"""
)

s = s.replace(
"""clean.updated_at = new Date().toISOString();
  return clean;""",
"""clean.view_version = Number.isInteger(clean.view_version) ? clean.view_version : 0;
  clean.updated_at = new Date().toISOString();
  return clean;"""
)

if "export function bumpViewVersion" not in s:
    s += """

export function bumpViewVersion(session) {
  session.view_version = Number.isInteger(session.view_version) ? session.view_version + 1 : 1;
  session.updated_at = new Date().toISOString();
  return session;
}

export function isStaleView(session, incomingVersion) {
  return Number(incomingVersion) !== Number(session.view_version || 0);
}
"""

p.write_text(s)
print("QUIZ_SESSION_VIEW_VERSION_PATCH=PASS")
PY

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

s = s.replace(
"""import {
  activeQuestionId,
  createSession,
  reduceQuizSession
} from '../../../packages/quiz-session/index.js';""",
"""import {
  activeQuestionId,
  bumpViewVersion,
  createSession,
  isStaleView,
  reduceQuizSession
} from '../../../packages/quiz-session/index.js';"""
)

s = s.replace(
"setCustomId(`spark_answer:${letter}`)",
"setCustomId(`spark_answer:${letter}:${session.view_version || 0}`)"
)

s = s.replace(
".setCustomId('spark_nav:prev')",
".setCustomId(`spark_nav:prev:${session.view_version || 0}`)"
)
s = s.replace(
".setCustomId('spark_nav:next')",
".setCustomId(`spark_nav:next:${session.view_version || 0}`)"
)
s = s.replace(
".setCustomId('spark_nav:unanswered')",
".setCustomId(`spark_nav:unanswered:${session.view_version || 0}`)"
)
s = s.replace(
".setCustomId('spark_nav:submit')",
".setCustomId(`spark_nav:submit:${session.view_version || 0}`)"
)

s = s.replace(
"""if (interaction.customId.startsWith('spark_answer:')) {
      const letter = interaction.customId.split(':')[1];""",
"""if (interaction.customId.startsWith('spark_answer:')) {
      const [, letter, version] = interaction.customId.split(':');

      if (isStaleView(session, version)) {
        bumpViewVersion(session);
        saveSession(interaction.user.id, session);

        await interaction.update({
          embeds: [buildQuizEmbed(session)],
          components: buildQuizRows(session)
        });
        return;
      }"""
)

for nav in ["prev", "next", "unanswered", "submit"]:
    s = s.replace(
        f"interaction.customId === 'spark_nav:{nav}'",
        f"interaction.customId.startsWith('spark_nav:{nav}:')"
    )

s = s.replace(
"""if (interaction.customId.startsWith('spark_nav:prev:')) {
      Object.assign(session, reduceQuizSession(session, { type: 'prev' }));
    }""",
"""if (interaction.customId.startsWith('spark_nav:prev:')) {
      const version = interaction.customId.split(':')[2];
      if (isStaleView(session, version)) {
        bumpViewVersion(session);
        saveSession(interaction.user.id, session);
        await interaction.update({ embeds: [buildQuizEmbed(session)], components: buildQuizRows(session) });
        return;
      }
      Object.assign(session, reduceQuizSession(session, { type: 'prev' }));
    }"""
)

s = s.replace(
"""if (interaction.customId.startsWith('spark_nav:next:')) {
      Object.assign(session, reduceQuizSession(session, { type: 'next' }));
    }""",
"""if (interaction.customId.startsWith('spark_nav:next:')) {
      const version = interaction.customId.split(':')[2];
      if (isStaleView(session, version)) {
        bumpViewVersion(session);
        saveSession(interaction.user.id, session);
        await interaction.update({ embeds: [buildQuizEmbed(session)], components: buildQuizRows(session) });
        return;
      }
      Object.assign(session, reduceQuizSession(session, { type: 'next' }));
    }"""
)

s = s.replace(
"""if (interaction.customId.startsWith('spark_nav:unanswered:')) {
      Object.assign(session, reduceQuizSession(session, { type: 'unanswered' }));
    }""",
"""if (interaction.customId.startsWith('spark_nav:unanswered:')) {
      const version = interaction.customId.split(':')[2];
      if (isStaleView(session, version)) {
        bumpViewVersion(session);
        saveSession(interaction.user.id, session);
        await interaction.update({ embeds: [buildQuizEmbed(session)], components: buildQuizRows(session) });
        return;
      }
      Object.assign(session, reduceQuizSession(session, { type: 'unanswered' }));
    }"""
)

s = s.replace(
"""if (interaction.customId.startsWith('spark_nav:submit:')) {
      const progress = adaptiveProgress(session.responses);""",
"""if (interaction.customId.startsWith('spark_nav:submit:')) {
      const version = interaction.customId.split(':')[2];
      if (isStaleView(session, version)) {
        bumpViewVersion(session);
        saveSession(interaction.user.id, session);
        await interaction.update({ embeds: [buildQuizEmbed(session)], components: buildQuizRows(session) });
        return;
      }

      const progress = adaptiveProgress(session.responses);"""
)

s = s.replace(
"""Object.assign(session, reduceQuizSession(session, { type: 'answer', answer: letter }));
      saveSession(interaction.user.id, session);""",
"""Object.assign(session, reduceQuizSession(session, { type: 'answer', answer: letter }));
      bumpViewVersion(session);
      saveSession(interaction.user.id, session);"""
)

for marker in [
    "Object.assign(session, reduceQuizSession(session, { type: 'prev' }));",
    "Object.assign(session, reduceQuizSession(session, { type: 'next' }));",
    "Object.assign(session, reduceQuizSession(session, { type: 'unanswered' }));"
]:
    s = s.replace(marker, marker + "\n      bumpViewVersion(session);")

p.write_text(s)
print("DISCORD_VIEW_VERSION_PATCH=PASS")
PY

cat > tests/e2e/quiz_view_self_heal.e2e.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  bumpViewVersion,
  createSession,
  isStaleView,
  reduceQuizSession
} from '../../packages/quiz-session/index.js';

test('view version increments after mutation', () => {
  let session = createSession('v');
  assert.equal(session.view_version, 0);

  session = reduceQuizSession(session, { type: 'answer', answer: 'A' });
  bumpViewVersion(session);

  assert.equal(session.view_version, 1);
});

test('stale view is detected and can be refreshed without mutation', () => {
  const session = createSession('v');
  bumpViewVersion(session);
  bumpViewVersion(session);

  assert.equal(isStaleView(session, 0), true);
  assert.equal(isStaleView(session, 2), false);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test:view-self-heal"] = "node --test tests/e2e/quiz_view_self_heal.e2e.test.js"

mobile = scripts["test:mobile"]
if "test:view-self-heal" not in mobile:
    scripts["test:mobile"] = mobile + " && npm run test:view-self-heal"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("VIEW_SELF_HEAL_TEST_SCRIPT=PASS")
PY

node --check apps/discord-bot/src/index.js
npm run test:view-self-heal
npm run test:mobile

echo "QUIZ_VIEW_SELF_HEAL=PASS"
