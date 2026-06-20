import assert from 'node:assert/strict';
import test from 'node:test';

import {
  buildProfileFlavorPrompt,
  mockProfileFlavor,
  parseJsonLoose,
  readProfileFlavor
} from '../../packages/profile-flavor-reader/index.js';

const sheet = {
  username: 'TestSubject',
  primary_domain: 'omni',
  tier: 'T2',
  percentile: 84.2,
  threat_class: 'STANDARD'
};

test('profile prompt seals raw scoring and preserves locked fields', () => {
  const prompt = buildProfileFlavorPrompt(sheet);

  assert.ok(prompt.includes('Do not reveal raw scores'));
  assert.ok(prompt.includes('Manifestation Domain: omni'));
  assert.ok(prompt.includes('Tier: T2'));
  assert.ok(prompt.includes('Percentile: 84.2'));
});

test('mock profile returns public-safe comic flavor', () => {
  const profile = mockProfileFlavor(sheet);

  assert.ok(profile.headline.includes('TestSubject'));
  assert.ok(profile.tagline.includes('OMNI'));
  assert.ok(profile.tagline.includes('84.2th percentile'));
  assert.ok(profile.field_note.includes('sealed'));
});

test('loose JSON parser extracts JSON from model output', () => {
  const parsed = parseJsonLoose('text before {"headline":"A","tagline":"B","origin_blurb":"C","field_note":"D"} text after');

  assert.equal(parsed.headline, 'A');
  assert.equal(parsed.field_note, 'D');
});

test('reader defaults to mock provider for offline tests', () => {
  const packet = readProfileFlavor(sheet);

  assert.equal(packet.provider, 'mock');
  assert.equal(typeof packet.prompt, 'string');
  assert.equal(typeof packet.profile.headline, 'string');
});
