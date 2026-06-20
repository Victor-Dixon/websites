#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== STALE SAFE BUTTON QUIZ =="

mkdir -p packages/quiz-button-session tests/e2e

cat > packages/quiz-button-session/index.js << 'JS'
import crypto from 'node:crypto';
import {
  adaptiveProgress,
  currentAdaptiveQuestionIds
} from '../scoring-engine/index.js';

export const ANSWERS = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

export function shortId() {
  return crypto.randomUUID().replace(/-/g, '').slice(0, 8);
}

export function createSession({ userId, guildId = 'guild', channelId = 'channel' }) {
  const ids = Array.from({ length: 36 }, (_, i) => i + 1);
  return {
    sessionId: shortId(),
    userId,
    guildId,
    channelId,
    messageId: null,
    viewVersion: 0,
    phase: 'domain',
    cursor: 0,
    activeQueue: ids,
    responses: {},
    createdAt: Date.now(),
    updatedAt: Date.now(),
    expiresAt: Date.now() + 1000 * 60 * 60
  };
}

export function encodeAnswerId({ session, qid, answer }) {
  return `q|${session.viewVersion}|${session.sessionId}|${qid}|${answer}|${session.userId.slice(-6)}`;
}

export function encodeNavId({ session, action }) {
  return `n|${session.viewVersion}|${session.sessionId}|${action}|${session.userId.slice(-6)}`;
}

export function decodeId(id) {
  const p = String(id).split('|');
  if (p[0] === 'q' && p.length === 6) {
    return { kind: 'answer', v: Number(p[1]), s: p[2], q: Number(p[3]), a: p[4], u: p[5] };
  }
  if (p[0] === 'n' && p.length === 5) {
    return { kind: 'nav', v: Number(p[1]), s: p[2], action: p[3], u: p[4] };
  }
  return null;
}

export function syncAdaptive(session) {
  session.activeQueue = currentAdaptiveQuestionIds(flatResponses(session));
  if (session.cursor < 0) session.cursor = 0;
  if (session.cursor >= session.activeQueue.length) session.cursor = session.activeQueue.length - 1;
  if (session.cursor < 0) session.cursor = 0;
  session.phase = adaptiveProgress(flatResponses(session)).complete
    ? 'complete'
    : Object.keys(session.responses).filter(k => Number(k) <= 36).length >= 36
      ? 'sub_affinity'
      : 'domain';
  return session;
}

export function flatResponses(session) {
  const out = {};
  for (const [qid, record] of Object.entries(session.responses || {})) {
    out[qid] = typeof record === 'string' ? record : record.answer;
  }
  return out;
}

export function currentQid(session) {
  syncAdaptive(session);
  return session.activeQueue[session.cursor];
}

export function progress(session) {
  return adaptiveProgress(flatResponses(session));
}

export function bump(session) {
  session.viewVersion += 1;
  session.updatedAt = Date.now();
  return session;
}

export function prune(session) {
  syncAdaptive(session);
  const valid = new Set(session.activeQueue);
  for (const key of Object.keys(session.responses)) {
    const qid = Number(key);
    if (qid >= 37 && !valid.has(qid)) delete session.responses[key];
  }
  syncAdaptive(session);
  return session;
}

export function reduceSession(session, event) {
  const next = structuredClone(session);
  syncAdaptive(next);

  if (!event) return next;

  if (event.userTail && event.userTail !== next.userId.slice(-6)) {
    return { session: next, outcome: 'wrong_user' };
  }

  if (event.viewVersion !== undefined && event.viewVersion !== next.viewVersion) {
    return { session: next, outcome: 'stale_view' };
  }

  if (event.type === 'refresh') {
    bump(next);
    return { session: next, outcome: 'refresh' };
  }

  if (event.type === 'answer') {
    if (!ANSWERS.includes(event.answer)) return { session: next, outcome: 'bad_answer' };

    const qid = currentQid(next);
    if (event.qid !== qid) {
      if (next.responses[event.qid]) return { session: next, outcome: 'already_answered' };
      return { session: next, outcome: 'wrong_question' };
    }

    if (next.responses[qid]) {
      return { session: next, outcome: 'already_answered' };
    }

    next.responses[qid] = {
      answer: event.answer,
      answeredAt: Date.now(),
      viewVersionAtAnswer: event.viewVersion
    };

    prune(next);

    const p = progress(next);
    if (p.complete) {
      next.phase = 'complete';
      bump(next);
      return { session: next, outcome: 'complete' };
    }

    const ids = next.activeQueue;
    const firstOpen = ids.findIndex(id => !next.responses[id]);
    next.cursor = firstOpen >= 0 ? firstOpen : Math.max(0, ids.length - 1);

    bump(next);
    return { session: next, outcome: 'answered' };
  }

  if (event.type === 'next') {
    next.cursor = Math.min(next.cursor + 1, next.activeQueue.length - 1);
    bump(next);
    return { session: next, outcome: 'nav' };
  }

  if (event.type === 'prev') {
    next.cursor = Math.max(next.cursor - 1, 0);
    bump(next);
    return { session: next, outcome: 'nav' };
  }

  if (event.type === 'unanswered') {
    const idx = next.activeQueue.findIndex(id => !next.responses[id]);
    next.cursor = idx >= 0 ? idx : next.cursor;
    bump(next);
    return { session: next, outcome: 'nav' };
  }

  return { session: next, outcome: 'noop' };
}

export function assertInvariant(session) {
  syncAdaptive(session);
  const p = progress(session);
  if (session.cursor < 0 || session.cursor >= session.activeQueue.length) throw new Error('cursor out of bounds');
  if (p.answered + p.remaining !== p.total) throw new Error('progress mismatch');
  for (const key of Object.keys(session.responses)) {
    const qid = Number(key);
    if (qid >= 37 && !session.activeQueue.includes(qid)) throw new Error(`irrelevant response ${qid}`);
  }
  return true;
}
JS

cat > tests/e2e/stale_safe_button_quiz.e2e.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  assertInvariant,
  createSession,
  currentQid,
  progress,
  reduceSession
} from '../../packages/quiz-button-session/index.js';

test('stale view refreshes without mutation', () => {
  let s = createSession({ userId: '123456789' });
  let r = reduceSession(s, { type: 'answer', qid: currentQid(s), answer: 'A', viewVersion: 0, userTail: '456789' });
  s = r.session;

  const before = Object.keys(s.responses).length;
  r = reduceSession(s, { type: 'answer', qid: currentQid(s), answer: 'B', viewVersion: 0, userTail: '456789' });

  assert.equal(r.outcome, 'stale_view');
  assert.equal(Object.keys(r.session.responses).length, before);
});

test('wrong question is rejected', () => {
  const s = createSession({ userId: '123456789' });
  const r = reduceSession(s, { type: 'answer', qid: 18, answer: 'A', viewVersion: 0, userTail: '456789' });
  assert.equal(r.outcome, 'wrong_question');
});

test('all A-G paths complete', () => {
  for (const a of ['A','B','C','D','E','F','G']) {
    let s = createSession({ userId: '123456789' });
    let safety = 0;
    while (!progress(s).complete) {
      safety++;
      assert.ok(safety < 100);
      const qid = currentQid(s);
      const r = reduceSession(s, { type: 'answer', qid, answer: a, viewVersion: s.viewVersion, userTail: '456789' });
      s = r.session;
      assertInvariant(s);
    }
    assert.equal(progress(s).complete, true);
  }
});

test('random nav and stale clicks never corrupt state', () => {
  let s = createSession({ userId: '123456789' });
  let seed = 42;
  const choices = ['A','B','C','D','E','F','G'];

  function rnd() {
    seed = (seed * 1103515245 + 12345) % 2147483648;
    return seed;
  }

  for (let i = 0; i < 300 && !progress(s).complete; i++) {
    const roll = rnd() % 10;
    let event;

    if (roll <= 5) event = { type: 'answer', qid: currentQid(s), answer: choices[rnd() % 7], viewVersion: s.viewVersion, userTail: '456789' };
    else if (roll === 6) event = { type: 'answer', qid: currentQid(s), answer: choices[rnd() % 7], viewVersion: Math.max(0, s.viewVersion - 1), userTail: '456789' };
    else if (roll === 7) event = { type: 'next', viewVersion: s.viewVersion, userTail: '456789' };
    else if (roll === 8) event = { type: 'prev', viewVersion: s.viewVersion, userTail: '456789' };
    else event = { type: 'unanswered', viewVersion: s.viewVersion, userTail: '456789' };

    const r = reduceSession(s, event);
    s = r.session;
    assertInvariant(s);
  }

  assert.ok(Object.keys(s.responses).length > 0);
});
JS

cat > apps/discord-bot/src/index.js << 'JS'
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
  try { await registerCommands(); } catch (err) { console.error(err); }
});

client.on(Events.InteractionCreate, async interaction => {
  if (interaction.isChatInputCommand()) {
    if (interaction.commandName === 'ping') {
      await interaction.reply({ content: 'AEGIS runtime online.' });
      return;
    }

    if (interaction.commandName === 'generate') {
      const session = getSession(interaction);
      saveSession(session);
      await interaction.reply({
        embeds: [buildQuizEmbed(session)],
        components: buildRows(session),
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

  const result = reduceSession(session, event);
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
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test:stale-buttons"] = "node --test tests/e2e/stale_safe_button_quiz.e2e.test.js"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("STALE_BUTTON_TEST_SCRIPT=PASS")
PY

node --check apps/discord-bot/src/index.js
npm run test:stale-buttons
npm run test:mobile

echo "STALE_SAFE_BUTTON_QUIZ=PASS"
