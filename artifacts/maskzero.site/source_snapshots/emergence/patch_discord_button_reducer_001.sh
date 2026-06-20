#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== PATCH DISCORD BUTTON REDUCER =="

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

# Ensure reducer import exists.
if "from '../../../packages/quiz-session/index.js';" not in s:
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

# Replace activeQuestionId function to use reducer.
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

# Replace button handler lines 300-371 by structural search.
start = s.index("  if (interaction.isButton()) {")
end = s.index("\n  }\n});", start)

new_block = """  if (interaction.isButton()) {
    const session = getSession(interaction);

    if (interaction.customId.startsWith('spark_answer:')) {
      const letter = interaction.customId.split(':')[1];
      Object.assign(session, reduceQuizSession(session, { type: 'answer', answer: letter }));
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
    }

    if (interaction.customId === 'spark_nav:submit') {
      const progress = adaptiveProgress(session.responses);

      if (!progress.complete) {
        Object.assign(session, reduceQuizSession(session, { type: 'unanswered' }));
        saveSession(interaction.user.id, session);

        await interaction.update({
          embeds: [buildQuizEmbed(session)],
          components: buildQuizRows(session)
        });
        return;
      }

      const results = { ...buildBasicResults(quiz, session.responses), locked: true };
      const sheet = buildCharacterSheet({
        userId: interaction.user.id,
        username: interaction.user.username,
        responses: session.responses
      });

      const aegisPacket = await interpretQuizResult({
        quiz,
        responses: session.responses,
        results
      });

      saveCharacterSheet(interaction.user.id, sheet);

      try {
        fs.unlinkSync(sessionFile(interaction.user.id));
      } catch {}

      saveQuizResult(interaction.user.id, {
        user_id: interaction.user.id,
        username: interaction.user.username,
        submitted_at: new Date().toISOString(),
        responses: session.responses,
        results,
        sheet,
        aegis: aegisPacket
      });

      await interaction.update({
        embeds: [buildSheetEmbed(interaction.user, sheet, aegisPacket.interpretation)],
        components: []
      });
      return;
    }

    saveSession(interaction.user.id, session);

    await interaction.update({
      embeds: [buildQuizEmbed(session)],
      components: buildQuizRows(session)
    });
  }"""

s = s[:start] + new_block + s[end:]

# Normalize new session shape.
s = s.replace("index: 0,", "cursor: 0,")

p.write_text(s)
print("DISCORD_BUTTON_REDUCER_PATCH=PASS")
PY

node --check apps/discord-bot/src/index.js
npm run test:quiz-session
npm run test:mobile

echo "PATCH_DISCORD_BUTTON_REDUCER=PASS"
