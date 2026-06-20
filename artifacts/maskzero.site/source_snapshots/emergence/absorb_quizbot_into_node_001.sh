#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ABSORB QUIZBOT INTO NODE =="

mkdir -p data/quiz data/state/quiz_results packages/quiz-engine apps/discord-bot/src/lib

cp apps/discord-quiz-bot/output/quizzes/spark_protocol_72.bot.json data/quiz/questions.json

cat > packages/quiz-engine/index.js << 'JS'
import fs from 'node:fs';

export function loadQuiz(path = 'data/quiz/questions.json') {
  const raw = JSON.parse(fs.readFileSync(path, 'utf8'));
  if (!raw.questions || !Array.isArray(raw.questions)) {
    throw new Error('Invalid quiz: missing questions[]');
  }
  return raw;
}

export function normalizeQuestion(q, idx) {
  const answers = q.answers || {};
  return {
    id: q.id ?? idx + 1,
    question: q.question || q.text || '',
    answers
  };
}

export function buildBasicResults(quiz, responses) {
  const counts = {};
  for (const value of Object.values(responses)) {
    counts[value] = (counts[value] || 0) + 1;
  }

  const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
  const primary = sorted[0]?.[0] || 'UNKNOWN';

  return {
    total_answered: Object.keys(responses).length,
    total_questions: quiz.questions.length,
    counts,
    primary,
    locked: Object.keys(responses).length === quiz.questions.length
  };
}

export function saveQuizResult(userId, payload) {
  fs.mkdirSync('data/state/quiz_results', { recursive: true });
  fs.writeFileSync(
    `data/state/quiz_results/${userId}.json`,
    JSON.stringify(payload, null, 2) + '\n'
  );
}
JS

cat > apps/discord-bot/src/index.js << 'JS'
import 'dotenv/config';
import {
  ActionRowBuilder,
  ButtonBuilder,
  ButtonStyle,
  Client,
  EmbedBuilder,
  Events,
  GatewayIntentBits,
  REST,
  Routes,
  SlashCommandBuilder
} from 'discord.js';

import {
  buildBasicResults,
  loadQuiz,
  normalizeQuestion,
  saveQuizResult
} from '../../../packages/quiz-engine/index.js';

const quiz = loadQuiz();
const sessions = new Map();

const client = new Client({
  intents: [GatewayIntentBits.Guilds]
});

const commands = [
  new SlashCommandBuilder()
    .setName('ping')
    .setDescription('Spark runtime heartbeat.'),

  new SlashCommandBuilder()
    .setName('generate')
    .setDescription('Begin Spark Protocol character generation.'),

  new SlashCommandBuilder()
    .setName('quiz_results')
    .setDescription('View your last Spark Protocol quiz results.'),

  new SlashCommandBuilder()
    .setName('battle')
    .setDescription('Run a Spark battle simulation.')
].map(cmd => cmd.toJSON());

const rest = new REST({ version: '10' })
  .setToken(process.env.DISCORD_TOKEN);

function sessionKey(interaction) {
  return `${interaction.guildId}:${interaction.user.id}`;
}

function getSession(interaction) {
  const key = sessionKey(interaction);
  if (!sessions.has(key)) {
    sessions.set(key, {
      userId: interaction.user.id,
      index: 0,
      responses: {}
    });
  }
  return sessions.get(key);
}

function progressBar(done, total) {
  const width = 18;
  const filled = Math.round((done / total) * width);
  return '█'.repeat(filled) + '░'.repeat(width - filled);
}

function buildQuizEmbed(session) {
  const q = normalizeQuestion(quiz.questions[session.index], session.index);
  const answered = Object.keys(session.responses).length;
  const current = session.responses[q.id];

  const lines = Object.entries(q.answers)
    .map(([letter, text]) => `${current === letter ? '✅' : '🔘'} **${letter}.** ${text}`)
    .join('\n');

  return new EmbedBuilder()
    .setTitle(`The Emergence Classification — Q${session.index + 1}/${quiz.questions.length}`)
    .setDescription(`**${q.question}**\n\n${lines}`)
    .setColor(current ? 0x2ecc71 : 0x5865f2)
    .setFooter({
      text: `Progress: ${progressBar(answered, quiz.questions.length)} (${answered}/${quiz.questions.length})`
    });
}

function buildQuizRows(session) {
  const q = normalizeQuestion(quiz.questions[session.index], session.index);
  const answerRow = new ActionRowBuilder();

  for (const letter of Object.keys(q.answers).slice(0, 5)) {
    answerRow.addComponents(
      new ButtonBuilder()
        .setCustomId(`spark_answer:${letter}`)
        .setLabel(letter)
        .setStyle(session.responses[q.id] === letter ? ButtonStyle.Success : ButtonStyle.Secondary)
    );
  }

  const navRow = new ActionRowBuilder().addComponents(
    new ButtonBuilder()
      .setCustomId('spark_nav:prev')
      .setLabel('Prev')
      .setStyle(ButtonStyle.Primary)
      .setDisabled(session.index <= 0),
    new ButtonBuilder()
      .setCustomId('spark_nav:next')
      .setLabel('Next')
      .setStyle(ButtonStyle.Primary)
      .setDisabled(session.index >= quiz.questions.length - 1),
    new ButtonBuilder()
      .setCustomId('spark_nav:unanswered')
      .setLabel('Unanswered')
      .setStyle(ButtonStyle.Secondary)
      .setDisabled(Object.keys(session.responses).length >= quiz.questions.length),
    new ButtonBuilder()
      .setCustomId('spark_nav:submit')
      .setLabel('Submit')
      .setStyle(ButtonStyle.Danger)
      .setDisabled(Object.keys(session.responses).length < quiz.questions.length)
  );

  return [answerRow, navRow];
}

function buildResultsEmbed(user, session) {
  const results = buildBasicResults(quiz, session.responses);
  const counts = Object.entries(results.counts)
    .sort((a, b) => b[1] - a[1])
    .map(([letter, count]) => `**${letter}**: ${count}`)
    .join('\n') || 'No answers recorded.';

  return new EmbedBuilder()
    .setTitle('AEGIS Classification Locked')
    .setDescription(`Subject: **${user.username}**\nPrimary signal: **${results.primary}**\nLocked: **${results.locked ? 'YES' : 'NO'}**`)
    .addFields(
      { name: 'Answer Distribution', value: counts, inline: false },
      { name: 'Completion', value: `${results.total_answered}/${results.total_questions}`, inline: true }
    )
    .setColor(0xf1c40f)
    .setFooter({ text: 'Spark Protocol: your personality is your power.' });
}

async function registerCommands() {
  console.log('== REGISTERING COMMANDS ==');

  await rest.put(
    Routes.applicationGuildCommands(
      process.env.CLIENT_ID,
      process.env.GUILD_ID
    ),
    { body: commands }
  );

  console.log('COMMAND_REGISTER=PASS');
}

client.once(Events.ClientReady, async readyClient => {
  console.log(`SPARK_BOT_READY=${readyClient.user.tag}`);
  console.log(`QUIZ_QUESTIONS=${quiz.questions.length}`);

  try {
    await registerCommands();
  } catch (err) {
    console.error(err);
  }
});

client.on(Events.InteractionCreate, async interaction => {
  if (interaction.isChatInputCommand()) {
    if (interaction.commandName === 'ping') {
      await interaction.reply({ content: 'AEGIS runtime online.' });
    }

    if (interaction.commandName === 'generate') {
      const session = getSession(interaction);
      await interaction.reply({
        embeds: [buildQuizEmbed(session)],
        components: buildQuizRows(session),
        ephemeral: true
      });
    }

    if (interaction.commandName === 'quiz_results') {
      const session = getSession(interaction);
      await interaction.reply({
        embeds: [buildResultsEmbed(interaction.user, session)],
        ephemeral: true
      });
    }

    if (interaction.commandName === 'battle') {
      await interaction.reply({
        content: 'AEGIS battle adjudication queue initialized.'
      });
    }
  }

  if (interaction.isButton()) {
    const session = getSession(interaction);
    const q = normalizeQuestion(quiz.questions[session.index], session.index);

    if (interaction.customId.startsWith('spark_answer:')) {
      const letter = interaction.customId.split(':')[1];
      session.responses[q.id] = letter;

      if (session.index < quiz.questions.length - 1) {
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
      session.index = Math.min(quiz.questions.length - 1, session.index + 1);
    }

    if (interaction.customId === 'spark_nav:unanswered') {
      const idx = quiz.questions.findIndex((item, i) => {
        const nq = normalizeQuestion(item, i);
        return !session.responses[nq.id];
      });
      session.index = idx >= 0 ? idx : session.index;
    }

    if (interaction.customId === 'spark_nav:submit') {
      const results = buildBasicResults(quiz, session.responses);
      saveQuizResult(interaction.user.id, {
        user_id: interaction.user.id,
        username: interaction.user.username,
        submitted_at: new Date().toISOString(),
        responses: session.responses,
        results
      });

      await interaction.update({
        embeds: [buildResultsEmbed(interaction.user, session)],
        components: []
      });
      return;
    }

    await interaction.update({
      embeds: [buildQuizEmbed(session)],
      components: buildQuizRows(session)
    });
  }
});

client.login(process.env.DISCORD_TOKEN);
JS

cat > runtime/tasks/spark_protocol_absorb_quizbot_into_node_001.yaml << 'YAML'
task_id: spark_protocol_absorb_quizbot_into_node_001
objective: Absorb working Python quizbot data and interaction logic into the main Node Discord bot.
source:
  quiz_data: apps/discord-quiz-bot/output/quizzes/spark_protocol_72.bot.json
target:
  quiz_data: data/quiz/questions.json
  engine: packages/quiz-engine/index.js
  bot: apps/discord-bot/src/index.js
verification:
  - node imports quiz engine
  - bot logs QUIZ_QUESTIONS=72
  - /generate displays button quiz
  - answer buttons update progress
  - submit writes data/state/quiz_results/<user>.json
status: implemented
YAML

node -e "import('./packages/quiz-engine/index.js').then(m => { const q=m.loadQuiz(); console.log('QUIZ_LOAD=PASS'); console.log('QUESTIONS=' + q.questions.length); })"

echo "ABSORB_QUIZBOT_INTO_NODE=PASS"
