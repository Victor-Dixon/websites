import {
  adaptiveProgress,
  currentAdaptiveQuestionIds
} from '../scoring-engine/index.js';

export function createSession(userId) {
  return {
    userId,
    cursor: 0,
    responses: {},
    updated_at: new Date().toISOString(),
    view_version: 0
  };
}

export function normalizeSession(session, userId = 'unknown') {
  const clean = session && typeof session === 'object' ? session : createSession(userId);

  clean.userId = clean.userId || userId;
  clean.cursor = Number.isInteger(clean.cursor) ? clean.cursor : 0;
  clean.responses = clean.responses && typeof clean.responses === 'object' ? clean.responses : {};

  const ids = currentAdaptiveQuestionIds(clean.responses);
  if (clean.cursor < 0) clean.cursor = 0;
  if (clean.cursor >= ids.length) clean.cursor = ids.length - 1;

  clean.view_version = Number.isInteger(clean.view_version) ? clean.view_version : 0;
  clean.updated_at = new Date().toISOString();
  return clean;
}

export function activeIds(session) {
  return currentAdaptiveQuestionIds(session.responses);
}

export function activeQuestionId(session) {
  const ids = activeIds(session);
  const cursor = Math.min(Math.max(session.cursor, 0), ids.length - 1);
  return ids[cursor];
}

export function firstUnansweredCursor(session) {
  const ids = activeIds(session);
  const idx = ids.findIndex(id => session.responses[id] === undefined);
  return idx >= 0 ? idx : Math.max(0, ids.length - 1);
}


export function pruneIrrelevantResponses(session) {
  const ids = new Set(currentAdaptiveQuestionIds(session.responses));

  for (const key of Object.keys(session.responses)) {
    const qid = Number(key);

    if (qid >= 37 && !ids.has(qid)) {
      delete session.responses[key];
    }
  }

  return session;
}

export function reduceQuizSession(session, action) {
  const next = normalizeSession(JSON.parse(JSON.stringify(session || createSession(action?.userId || 'unknown'))));

  if (!action || !action.type) {
    return next;
  }

  if (action.type === 'answer') {
    const qid = activeQuestionId(next);
    const answer = action.answer;

    if (!['A', 'B', 'C', 'D', 'E', 'F', 'G'].includes(answer)) {
      return next;
    }

    next.responses[qid] = answer;

    pruneIrrelevantResponses(next);

    const idsAfter = activeIds(next);
    const unansweredAfter = idsAfter.findIndex(id => next.responses[id] === undefined);

    if (unansweredAfter >= 0) {
      next.cursor = unansweredAfter;
    } else {
      next.cursor = Math.max(0, idsAfter.length - 1);
    }

    next.updated_at = new Date().toISOString();
    return normalizeSession(next);
  }

  if (action.type === 'next') {
    const ids = activeIds(next);
    next.cursor = Math.min(next.cursor + 1, ids.length - 1);
    return normalizeSession(next);
  }

  if (action.type === 'prev') {
    next.cursor = Math.max(next.cursor - 1, 0);
    return normalizeSession(next);
  }

  if (action.type === 'unanswered') {
    next.cursor = firstUnansweredCursor(next);
    return normalizeSession(next);
  }

  if (action.type === 'reset') {
    return createSession(next.userId);
  }

  pruneIrrelevantResponses(next);
  return next;
}

export function sessionProgress(session) {
  return adaptiveProgress(session.responses);
}

export function assertSessionInvariant(session) {
  const ids = activeIds(session);
  const progress = sessionProgress(session);

  if (session.cursor < 0 || session.cursor >= ids.length) {
    throw new Error(`cursor out of bounds: ${session.cursor}/${ids.length}`);
  }

  if (progress.answered + progress.remaining !== progress.total) {
    throw new Error('progress total mismatch');
  }

  for (const qid of Object.keys(session.responses).map(Number)) {
    if (!ids.includes(qid) && qid >= 37) {
      throw new Error(`irrelevant affinity response retained: ${qid}`);
    }
  }

  return true;
}


export function bumpViewVersion(session) {
  session.view_version = Number.isInteger(session.view_version) ? session.view_version + 1 : 1;
  session.updated_at = new Date().toISOString();
  return session;
}

export function isStaleView(session, incomingVersion) {
  return Number(incomingVersion) !== Number(session.view_version || 0);
}
