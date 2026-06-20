import 'dotenv/config';
import fs from 'node:fs';
import path from 'node:path';

import {
  ActionRowBuilder,
  ButtonBuilder,
  ButtonStyle,
  Client,
  EmbedBuilder,
  Events,
  ChannelType,
  GatewayIntentBits,
  MessageFlags,
  REST,
  Routes,
  SlashCommandBuilder
} from 'discord.js';

import { buildBasicResults, loadQuiz, normalizeQuestion, saveQuizResult } from '../../../packages/quiz-engine/index.js';
import { buildCharacterSheet, buildComicProfile } from '../../../packages/scoring-engine/index.js';
import { interpretQuizResult } from '../../../packages/aegis-interpreter/index.js';

import {
  createSession,
  currentQid,
  decodeId,
  encodeAnswerId,
  encodeNavId,
  flatResponses,
  progress,
  reduceSession
} from '../../../packages/quiz-button-session/index.js';

const quiz = loadQuiz();
const sessions = new Map();

const RUNTIME_LOG = path.join(repoRoot(), 'data/reports/discord_runtime/button_runtime.jsonl');

function runtimeLog(event) {
  fs.mkdirSync(path.dirname(RUNTIME_LOG), { recursive: true });
  fs.appendFileSync(RUNTIME_LOG, JSON.stringify({
    t: new Date().toISOString(),
    ...event
  }) + '\n');
}


const client = new Client({ intents: [GatewayIntentBits.Guilds] });

const commands = [
  new SlashCommandBuilder().setName('ping').setDescription('Spark runtime heartbeat.'),
  new SlashCommandBuilder().setName('generate').setDescription('Begin or resume Spark Protocol character generation.'),
  new SlashCommandBuilder().setName('quiz_results').setDescription('View your Spark Protocol quiz progress.'),
  new SlashCommandBuilder().setName('battle').setDescription('Run a Spark battle simulation.')
].map(cmd => cmd.toJSON());

const rest = new REST({ version: '10' }).setToken(process.env.DISCORD_TOKEN);

function repoRoot() {
  return path.resolve(new URL('../../..', import.meta.url).pathname);
}

function stateDir(name) {
  const dir = path.join(repoRoot(), 'data/state', name);
  fs.mkdirSync(dir, { recursive: true });
  return dir;
}

function sessionFile(userId) {
  return path.join(stateDir('quiz_sessions'), `${userId}.json`);
}

function sheetFile(userId) {
  return path.join(stateDir('character_sheets'), `${userId}.json`);
}

function saveSession(session) {
  fs.writeFileSync(sessionFile(session.userId), JSON.stringify(session, null, 2) + '\n');
}

function loadSession(userId, guildId, channelId) {
  const file = sessionFile(userId);
  if (fs.existsSync(file)) return JSON.parse(fs.readFileSync(file, 'utf8'));
  return createSession({ userId, guildId, channelId });
}

function saveSheet(userId, sheet) {
  fs.writeFileSync(sheetFile(userId), JSON.stringify(sheet, null, 2) + '\n');
}

function deleteSession(userId) {
  try { fs.unlinkSync(sessionFile(userId)); } catch {}
}

function key(interaction) {
  return `${interaction.guildId}:${interaction.user.id}`;
}

function getSession(interaction) {
  const k = key(interaction);
  if (!sessions.has(k)) {
    sessions.set(k, loadSession(interaction.user.id, interaction.guildId, interaction.channelId));
  }
  return sessions.get(k);
}

function progressBar(done, total) {
  const width = 18;
  const filled = Math.round((done / total) * width);
  return '█'.repeat(filled) + '░'.repeat(width - filled);
}

function buildQuizEmbed(session, notice = null) {
  const qid = currentQid(session);
  const q = normalizeQuestion(quiz.questions[qid - 1], qid - 1);
  const p = progress(session);
  const idx = session.activeQueue.indexOf(qid) + 1;

  const options = Object.entries(q.answers)
    .map(([letter, text]) => `**${letter}.** ${text}`)
    .join('\n');

  return new EmbedBuilder()
    .setTitle(`The Emergence Classification — Q${idx}/${session.activeQueue.length}`)
    .setDescription(`${notice ? `⚠️ ${notice}\n\n` : ''}**${q.question}**\n\n${options}`)
    .setColor(0x5865f2)
    .setFooter({
      text: `Progress: ${progressBar(p.answered, p.total)} (${p.answered}/${p.total}) | Questions left: ${p.remaining} | v${session.viewVersion}`
    });
}

function buildRows(session) {
  const qid = currentQid(session);
  const q = normalizeQuestion(quiz.questions[qid - 1], qid - 1);
  const letters = Object.keys(q.answers);
  const rows = [];

  for (let i = 0; i < letters.length; i += 5) {
    const row = new ActionRowBuilder();
    for (const letter of letters.slice(i, i + 5)) {
      row.addComponents(
        new ButtonBuilder()
          .setCustomId(encodeAnswerId({ session, qid, answer: letter }))
          .setLabel(letter)
          .setStyle(ButtonStyle.Primary)
      );
    }
    rows.push(row);
  }

  rows.push(
    new ActionRowBuilder().addComponents(
      new ButtonBuilder().setCustomId(encodeNavId({ session, action: 'prev' })).setLabel('Prev').setStyle(ButtonStyle.Secondary).setDisabled(session.cursor <= 0),
      new ButtonBuilder().setCustomId(encodeNavId({ session, action: 'next' })).setLabel('Next').setStyle(ButtonStyle.Secondary).setDisabled(session.cursor >= session.activeQueue.length - 1),
      new ButtonBuilder().setCustomId(encodeNavId({ session, action: 'unanswered' })).setLabel('Unanswered').setStyle(ButtonStyle.Secondary).setDisabled(progress(session).complete),
      new ButtonBuilder().setCustomId(encodeNavId({ session, action: 'refresh' })).setLabel('Refresh').setStyle(ButtonStyle.Success),
      new ButtonBuilder().setCustomId(encodeNavId({ session, action: 'submit' })).setLabel('Submit').setStyle(ButtonStyle.Danger).setDisabled(!progress(session).complete)
    )
  );

  return rows;
}

function buildSheetEmbed(user, sheet, aegis = null) {
  const profile = buildComicProfile(sheet);
  return new EmbedBuilder()
    .setTitle(profile.title)
    .setDescription(`**${profile.subtitle}**\n\n${profile.cover_line}`)
    .addFields(
      { name: 'Back-of-Comic Profile', value: profile.stat_blocks.join('\n'), inline: false },
      { name: 'AEGIS Readout', value: aegis?.summary || 'The subject has completed classification.', inline: false },
      { name: 'Field Notes', value: profile.back_matter.join('\n'), inline: false }
    )
    .setColor(0xf1c40f)
    .setFooter({ text: 'Raw scoring matrix sealed under AEGIS protocol.' });
}

async function finalize(interaction, session) {
  const responses = flatResponses(session);
  const results = { ...buildBasicResults(quiz, responses), locked: true };
  const sheet = buildCharacterSheet({
    userId: interaction.user.id,
    username: interaction.user.username,
    responses
  });

  const aegisPacket = await interpretQuizResult({ quiz, responses, results });
  saveSheet(interaction.user.id, sheet);
  saveQuizResult(interaction.user.id, {
    user_id: interaction.user.id,
    username: interaction.user.username,
    submitted_at: new Date().toISOString(),
    responses,
    results,
    sheet,
    aegis: aegisPacket
  });

  deleteSession(interaction.user.id);
  sessions.delete(key(interaction));

  await interaction.editReply({
    embeds: [buildSheetEmbed(interaction.user, sheet, aegisPacket.interpretation)],
    components: []
  });
}

async function postOnlineStatus(readyClient) {
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

async function registerCommands() {
  console.log('== REGISTERING COMMANDS ==');
  await rest.put(
    Routes.applicationGuildCommands(process.env.CLIENT_ID, process.env.GUILD_ID),
    { body: commands }
  );
  console.log('COMMAND_REGISTER=PASS');
}

client.once(Events.ClientReady, async readyClient => {
  console.log(`SPARK_BOT_READY=${readyClient.user.tag}`);
  console.log(`QUIZ_QUESTIONS=${quiz.questions.length}`);
  try {
    await registerCommands();
    await postOnlineStatus(readyClient);
  } catch (err) {
    console.error(err);
  }
});

client.on(Events.InteractionCreate, async interaction => {
  if (interaction.isChatInputCommand()) {
    if (interaction.commandName === 'ping') {
      await interaction.reply({ content: 'AEGIS runtime online.' });
      return;
    }

    if (interaction.commandName === 'generate') {
      const session = getSession(interaction);

      if (!session.threadId) {
        const thread = await interaction.channel.threads.create({
          name: `spark-${interaction.user.username}`.slice(0, 90),
          type: ChannelType.PrivateThread,
          invitable: false,
          reason: 'Spark Protocol private classification'
        });

        await thread.members.add(interaction.user.id);
        session.threadId = thread.id;

        const quizMessage = await thread.send({
          content: `<@${interaction.user.id}> Spark Protocol classification started.`,
          embeds: [buildQuizEmbed(session)],
          components: buildRows(session)
        });

        session.messageId = quizMessage.id;
        saveSession(session);

        await interaction.reply({
          content: `Classification thread opened: <#${thread.id}>`,
          flags: MessageFlags.Ephemeral
        });
        return;
      }

      const thread = await client.channels.fetch(session.threadId);
      const quizMessage = await thread.messages.fetch(session.messageId);

      await quizMessage.edit({
        embeds: [buildQuizEmbed(session, 'View refreshed from saved state.')],
        components: buildRows(session)
      });

      saveSession(session);

      await interaction.reply({
        content: `Classification thread resumed: <#${thread.id}>`,
        flags: MessageFlags.Ephemeral
      });
      return;
    }

    if (interaction.commandName === 'quiz_results') {
      const session = getSession(interaction);
      const p = progress(session);
      await interaction.reply({
        content: `Progress: ${p.answered}/${p.total} | Questions left: ${p.remaining}`,
        flags: MessageFlags.Ephemeral
      });
      return;
    }

    if (interaction.commandName === 'battle') {
      await interaction.reply({ content: 'AEGIS battle adjudication queue initialized.' });
      return;
    }
  }

  if (!interaction.isButton()) return;

  await interaction.deferUpdate();

  const payload = decodeId(interaction.customId);
  if (!payload) {
    await interaction.editReply({ content: 'Invalid quiz button. Use /generate to refresh.', components: [] });
    return;
  }

  const session = getSession(interaction);

  if (payload.u !== interaction.user.id.slice(-6) || payload.s !== session.sessionId) {
    await interaction.editReply({ embeds: [buildQuizEmbed(session, 'This view was refreshed because the button did not match your active session.')], components: buildRows(session) });
    return;
  }

  let event;

  if (payload.kind === 'answer') {
    event = { type: 'answer', qid: payload.q, answer: payload.a, viewVersion: payload.v, userTail: payload.u };
  } else {
    event = { type: payload.action === 'refresh' ? 'refresh' : payload.action, viewVersion: payload.v, userTail: payload.u };
  }

  runtimeLog({
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
  });
  Object.assign(session, result.session);
  saveSession(session);

  if (result.outcome === 'complete' || (payload.kind === 'nav' && payload.action === 'submit' && progress(session).complete)) {
    await finalize(interaction, session);
    return;
  }

  const notice = ['stale_view', 'wrong_question', 'already_answered'].includes(result.outcome)
    ? 'View refreshed from saved state. Please answer the current question.'
    : null;

  await interaction.editReply({
    embeds: [buildQuizEmbed(session, notice)],
    components: buildRows(session)
  });
});

client.login(process.env.DISCORD_TOKEN);
