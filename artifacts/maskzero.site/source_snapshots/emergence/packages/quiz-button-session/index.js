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
  // Preserve finalized affinity queue once entered.
  if (session.phase === 'sub_affinity' && Array.isArray(session.activeQueue) && session.activeQueue.length > 0) {
    return session;
  }

  const ids = currentAdaptiveQuestionIds(flatResponses(session));

  if (ids.length <= 36) {
    session.phase = 'domain';
  } else {
    session.phase = 'sub_affinity';
  }

  session.activeQueue = ids;

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
  for (const key of Object.keys(session.responses).map(Number)) {
    const qid = Number(key);
    if (qid >= 37 && !valid.has(Number(qid))) delete session.responses[String(key)];
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

    if (next.responses[String(qid)]) {
      return { session: next, outcome: 'already_answered' };
    }

    next.responses[String(qid)] = {
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

    // Fixed queue + moving cursor. Do not shrink/rebuild activeQueue during a phase.
    if (next.cursor < next.activeQueue.length - 1) {
      next.cursor += 1;
      bump(next);
      return { session: next, outcome: 'answered' };
    }

    if (next.phase === 'domain') {
      const subQueue = currentAdaptiveQuestionIds(flatResponses(next));

      next.phase = 'sub_affinity';
      next.activeQueue = subQueue;
      next.cursor = 0;

      bump(next);
      return { session: next, outcome: 'answered' };
    }

    next.phase = 'complete';
    bump(next);
    return { session: next, outcome: 'complete' };
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
    const idx = next.activeQueue.findIndex(id => !next.responses[String(id)]);
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
  for (const key of Object.keys(session.responses).map(Number)) {
    const qid = Number(key);
    if (qid >= 37 && !session.activeQueue.includes(qid)) throw new Error(`irrelevant response ${qid}`);
  }
  return true;
}
