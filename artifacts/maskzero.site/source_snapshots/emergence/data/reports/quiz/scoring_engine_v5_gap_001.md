# Scoring Engine v5 Gap Report

## package scoring engine
export const DOMAIN_MAP = {
  A: 'titan',
  B: 'inferno',
  C: 'velocity',
  D: 'bulwark',
  E: 'primal',
  F: 'specter',
  G: 'omni'
};

export const FLAVOR_MAP = {
  A: 'force',
  B: 'surge',
  C: 'reflex',
  D: 'anchor',
  E: 'resonance',
  F: 'shadow',
  G: 'control'
};

export function calculateDomainScores(responses) {
  const scores = {};
  for (const domain of Object.values(DOMAIN_MAP)) scores[domain] = 0;

  for (const [qid, answer] of Object.entries(responses)) {
    const qnum = Number(qid);
    if (qnum >= 1 && qnum <= 36 && DOMAIN_MAP[answer]) {
      scores[DOMAIN_MAP[answer]] += 1;
    }
  }

  return scores;
}

export function calculateFlavorVectors(responses) {
  const vectors = {};
  for (const flavor of Object.values(FLAVOR_MAP)) vectors[flavor] = 0;

  for (const [qid, answer] of Object.entries(responses)) {
    const qnum = Number(qid);
    if (qnum >= 37 && qnum <= 72 && FLAVOR_MAP[answer]) {
      vectors[FLAVOR_MAP[answer]] += 1;
    }
  }

  return vectors;
}

export function determinePrimaryDomain(scores) {
  return Object.entries(scores).sort((a, b) => b[1] - a[1])[0]?.[0] || 'unknown';
}

export function manifestedDomains(domainScores) {
  const max = Math.max(...Object.values(domainScores));
  if (!Number.isFinite(max) || max <= 0) return [];

  return Object.entries(domainScores)
    .filter(([, score]) => score === max || score >= Math.ceil(max * 0.75))
    .map(([domain]) => domain);
}

export function domainFlavorRange(domain) {
  const ranges = {
    titan: [37, 42],
    bulwark: [37, 42],
    velocity: [43, 48],
    inferno: [49, 54],
    specter: [55, 60],
    omni: [61, 66],
    primal: [67, 72]
  };

  return ranges[domain] || [61, 66];
}

export function adaptiveQuestionIds(responses) {
  const domainScores = calculateDomainScores(responses);
  const domains = manifestedDomains(domainScores);
  const ids = new Set();

  for (let i = 1; i <= 36; i++) ids.add(i);

  for (const domain of domains) {
    const [start, end] = domainFlavorRange(domain);
    for (let i = start; i <= end; i++) ids.add(i);
  }

  return [...ids].sort((a, b) => a - b);
}

export function currentAdaptiveQuestionIds(responses) {
  const answeredDomainCount = Object.keys(responses)
    .map(Number)
    .filter(qid => qid >= 1 && qid <= 36)
    .length;

  if (answeredDomainCount < 36) {
    return Array.from({ length: 36 }, (_, i) => i + 1);
  }

  return adaptiveQuestionIds(responses);
}

export function adaptiveQuestionsRemaining(responses) {
  const ids = currentAdaptiveQuestionIds(responses);
  const answered = new Set(Object.keys(responses).map(Number));
  return ids.filter(id => !answered.has(id));
}

export function adaptiveProgress(responses) {
  const ids = currentAdaptiveQuestionIds(responses);
  const remaining = adaptiveQuestionsRemaining(responses);

  return {
    total: ids.length,
    answered: ids.length - remaining.length,
    remaining: remaining.length,
    remaining_ids: remaining,
    complete: remaining.length === 0
  };
}

export function determineTier(primaryScore) {
  if (primaryScore >= 28) return 'T5';
  if (primaryScore >= 22) return 'T4';
  if (primaryScore >= 16) return 'T3';
  if (primaryScore >= 10) return 'T2';
  return 'T1';
}

export function percentileFromTier(tier, score = 0) {
  const ranges = {
    T1: [80, 84],
    T2: [81, 86],
    T3: [84, 88],
    T4: [87, 91],
    T5: [89, 93],
    OMEGA: [93, 93]
  };

  const [min, max] = ranges[tier] || [77, 82];
  if (min === max) return min;

  const normalized = Math.min(Math.max(score, 0) / 36, 1);
  const value = min + (max - min) * normalized;

  return Number(Math.min(value, 93).toFixed(1));
}

export function calculatePercentile(primaryScore, totalDomainQuestions = 36) {
  return percentileFromTier(determineTier(primaryScore), primaryScore);
}

export function determineThreatClass(tier, flavorVectors) {
  const shadow = flavorVectors.shadow || 0;
  const surge = flavorVectors.surge || 0;
  const control = flavorVectors.control || 0;

  if (tier === 'T5' && (surge >= 5 || control >= 5)) return 'OMEGA';
  if (tier === 'T5' || tier === 'T4') return 'ALPHA';
  if (shadow >= 5) return 'SIGMA';

  return 'STANDARD';
}

export function buildCharacterSheet({ userId, username, responses }) {
  const domain_scores = calculateDomainScores(responses);
  const flavor_vectors = calculateFlavorVectors(responses);
  const primary_domain = determinePrimaryDomain(domain_scores);
  const primary_score = domain_scores[primary_domain] || 0;
  const tier = determineTier(primary_score);
  const percentile = percentileFromTier(tier, primary_score);
  const threat_class = determineThreatClass(tier, flavor_vectors);

  return {
    schema_version: 'spark_character_sheet_v1',
    locked: true,
    user_id: userId,
    username,
    generated_at: new Date().toISOString(),
    primary_domain,
    primary_score,
    tier,
    percentile,
    threat_class,
    domain_scores,
    flavor_vectors,
    manifestation: {
      codename: null,
      alignment: threat_class,
      descriptor: `${tier} ${primary_domain}`
    }
  };
}

export function buildComicProfile(sheet) {
  return {
    title: `${sheet.username}: Classified Emergence Profile`,
    subtitle: `Manifestation Class ${sheet.tier}`,
    cover_line: `A ${sheet.primary_domain.toUpperCase()}-type emergence ranked in the ${sheet.percentile}th percentile with ${sheet.threat_class} threat-band behavior.`,
    stat_blocks: [
      `Primary Manifestation: ${sheet.primary_domain.toUpperCase()}`,
      `Power Tier: ${sheet.tier}`,
      `Threat Band: ${sheet.threat_class}`,
      `AEGIS Lock: IMMUTABLE`
    ],
    back_matter: [
      'Origin Signal: psychological resonance pattern confirmed.',
      'Protocol Note: raw scoring matrix sealed to prevent profile gaming.',
      'Battle Eligibility: approved after codename registration.',
      'Reader Advisory: this subject may evolve narratively, but the locked sheet does not drift.'
    ]
  };
}

## scoring tests
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  calculateDomainScores,
  calculateFlavorVectors,
  determinePrimaryDomain,
  determineTier,
  determineThreatClass,
  buildCharacterSheet
} from '../../packages/scoring-engine/index.js';

test('domain scoring counts first 36 responses only', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) {
    responses[i] = 'A';
  }

  const scores = calculateDomainScores(responses);

  assert.equal(scores.titan, 36);
  assert.equal(scores.velocity, 0);
});

test('flavor vectors count questions 37-72 only', () => {
  const responses = {};

  for (let i = 37; i <= 72; i++) {
    responses[i] = 'F';
  }

  const vectors = calculateFlavorVectors(responses);

  assert.equal(vectors.shadow, 36);
  assert.equal(vectors.force, 0);
});

test('tier thresholds classify correctly', () => {
  assert.equal(determineTier(5), 'T1');
  assert.equal(determineTier(10), 'T2');
  assert.equal(determineTier(16), 'T3');
  assert.equal(determineTier(22), 'T4');
  assert.equal(determineTier(28), 'T5');
});

test('threat class resolves correctly', () => {
  assert.equal(
    determineThreatClass('T5', { surge: 6 }),
    'OMEGA'
  );

  assert.equal(
    determineThreatClass('T4', { surge: 2 }),
    'ALPHA'
  );

  assert.equal(
    determineThreatClass('T2', { shadow: 14 }),
    'SIGMA'
  );

  assert.equal(
    determineThreatClass('T2', { shadow: 1 }),
    'STANDARD'
  );
});

test('full character sheet builds deterministically', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) {
    responses[i] = 'C';
  }

  for (let i = 37; i <= 72; i++) {
    responses[i] = 'F';
  }

  const sheet = buildCharacterSheet({
    userId: '123',
    username: 'Victor',
    responses
  });

  assert.equal(sheet.locked, true);
  assert.equal(sheet.primary_domain, 'velocity');
  assert.equal(sheet.primary_score, 36);
  assert.equal(sheet.tier, 'T5');
  assert.equal(sheet.threat_class, 'ALPHA');

  assert.equal(sheet.domain_scores.velocity, 36);
  assert.equal(sheet.flavor_vectors.shadow, 36);

  assert.equal(
    sheet.manifestation.descriptor,
    'T5 velocity'
  );
});

## adaptive tests
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  currentAdaptiveQuestionIds,
  domainFlavorRange,
  manifestedDomains,
  calculateDomainScores
} from '../../packages/scoring-engine/index.js';

test('adaptive engine starts at domain phase only', () => {
  const progress = adaptiveProgress({});
  assert.equal(progress.total, 36);
  assert.equal(progress.answered, 0);
  assert.equal(progress.remaining, 36);
  assert.equal(progress.complete, false);
});

test('each domain maps to a valid sub-affinity range', () => {
  for (const domain of ['titan', 'bulwark', 'velocity', 'inferno', 'specter', 'omni', 'primal']) {
    const [start, end] = domainFlavorRange(domain);
    assert.equal(end - start + 1, 6);
    assert.ok(start >= 37);
    assert.ok(end <= 72);
  }
});

test('G answers manifest omni and expand to omni block only', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const scores = calculateDomainScores(responses);
  const domains = manifestedDomains(scores);
  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.deepEqual(domains, ['omni']);
  assert.equal(ids.length, 42);
  assert.deepEqual(ids.slice(-6), [61, 62, 63, 64, 65, 66]);
  assert.equal(progress.remaining, 6);
});

test('adaptive questions left updates as manifested block is answered', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  assert.equal(adaptiveProgress(responses).remaining, 6);

  responses[61] = 'A';
  responses[62] = 'B';

  const progress = adaptiveProgress(responses);
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 38);
  assert.equal(progress.remaining, 4);
  assert.deepEqual(progress.remaining_ids, [63, 64, 65, 66]);
});

test('adaptive quiz completes without asking irrelevant flavor blocks', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  for (let i = 61; i <= 66; i++) responses[i] = 'C';

  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.includes(43), false);
  assert.equal(ids.includes(55), false);
  assert.equal(ids.includes(67), false);
  assert.equal(progress.complete, true);
  assert.equal(progress.remaining, 0);
});

## quiz question count
QUIZ= apps/discord-quiz-bot/output/quizzes/spark_protocol_72.bot.json
QUESTIONS= 72
FIRST_KEYS= ['answers', 'choices', 'id', 'question', 'required', 'type']
LAST_ID= 72
LAST_KEYS= ['answers', 'choices', 'id', 'question', 'required', 'type']
