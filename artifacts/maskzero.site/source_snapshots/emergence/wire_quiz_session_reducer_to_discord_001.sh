#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== WIRE QUIZ SESSION REDUCER TO DISCORD =="

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

s = s.replace(
"""import {
  adaptiveProgress,
  buildCharacterSheet,
  buildComicProfile,
  currentAdaptiveQuestionIds
} from '../../../packages/scoring-engine/index.js';""",
"""import {
  adaptiveProgress,
  buildCharacterSheet,
  buildComicProfile,
  currentAdaptiveQuestionIds
} from '../../../packages/scoring-engine/index.js';

import {
  activeQuestionId as reducerActiveQuestionId,
  reduceQuizSession
} from '../../../packages/quiz-session/index.js';"""
)

start = s.index("function activeQuestionId(session)")
end = s.index("\nfunction buildQuizEmbed", start)

replacement = """function activeQuestionId(session) {
  return reducerActiveQuestionId(session);
}

function activeQuestion(session) {
  const qid = activeQuestionId(session);
  return normalizeQuestion(quiz.questions[qid - 1], qid - 1);
}

function syncSessionIndex(session) {
  return session;
}
"""

s = s[:start] + replacement + s[end:]

old = """    if (interaction.customId.startsWith('spark_answer:')) {
      const letter = interaction.customId.split(':')[1];
      session.responses[q.id] = letter;
      saveSession(interaction.user.id, session);

      const ids = currentAdaptiveQuestionIds(session.responses);
      if (session.index < ids.length - 1) {
        session.index += 1;
      }

      await interaction.update({
        embeds: [buildQuizEmbed(session)],
        components: buildQuizRows(session)
      });
      return;
    }

    if (interaction.customId === 'spark_nav:prev') {
      session.index = Math.max(0, session.index - 1);
    }

    if (interaction.customId === 'spark_nav:next') {
      const ids = currentAdaptiveQuestionIds(session.responses);
      session.index = Math.min(ids.length - 1, session.index + 1);
    }

    if (interaction.customId === 'spark_nav:unanswered') {
      const ids = currentAdaptiveQuestionIds(session.responses);
      const idx = ids.findIndex((qid) => !session.responses[qid]);
      session.index = idx >= 0 ? idx : session.index;
    }"""

new = """    if (interaction.customId.startsWith('spark_answer:')) {
      const letter = interaction.customId.split(':')[1];
      const reduced = reduceQuizSession(session, { type: 'answer', answer: letter });
      Object.assign(session, reduced);
      saveSession(interaction.user.id, session);

      await interaction.update({
        embeds: [buildQuizEmbed(session)],
        components: buildQuizRows(session)
      });
      return;
    }

    if (interaction.customId === 'spark_nav:prev') {
      Object.assign(session, reduceQuizSession(session, { type: 'prev' }));
    }

    if (interaction.customId === 'spark_nav:next') {
      Object.assign(session, reduceQuizSession(session, { type: 'next' }));
    }

    if (interaction.customId === 'spark_nav:unanswered') {
      Object.assign(session, reduceQuizSession(session, { type: 'unanswered' }));
    }"""

if old not in s:
    raise SystemExit("HANDLER_BLOCK_NOT_FOUND")

s = s.replace(old, new)

s = s.replace("index: 0,", "cursor: 0,")

p.write_text(s)
print("DISCORD_REDUCER_WIRED=PASS")
PY

npm run test:quiz-session
npm run test:mobile

echo "WIRE_QUIZ_SESSION_REDUCER_TO_DISCORD=PASS"
