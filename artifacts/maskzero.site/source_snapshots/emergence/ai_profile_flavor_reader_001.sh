#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== AI PROFILE FLAVOR READER =="

mkdir -p packages/profile-flavor-reader tests/ai

cat > packages/profile-flavor-reader/index.js << 'JS'
import { execFileSync } from 'node:child_process';

export function buildProfileFlavorPrompt(sheet) {
  return [
    'You are AEGIS Classification Services.',
    'Write a short back-of-a-comic-book character profile.',
    'Do not reveal raw scores, answer counts, scoring math, or quiz internals.',
    'Do not change tier, domain, percentile, or threat band.',
    '',
    `Name: ${sheet.username}`,
    `Manifestation Domain: ${sheet.primary_domain}`,
    `Tier: ${sheet.tier}`,
    `Percentile: ${sheet.percentile}`,
    `Threat Band: ${sheet.threat_class}`,
    '',
    'Return only JSON:',
    '{',
    '  "headline": string,',
    '  "tagline": string,',
    '  "origin_blurb": string,',
    '  "field_note": string',
    '}'
  ].join('\n');
}

export function mockProfileFlavor(sheet) {
  return {
    headline: `${sheet.username}: Classified Emergence Profile`,
    tagline: `A ${sheet.primary_domain.toUpperCase()}-type emergence ranked in the ${sheet.percentile}th percentile.`,
    origin_blurb: 'AEGIS records indicate a stable manifestation pattern with personality-linked emergence traits.',
    field_note: 'Raw scoring matrix sealed. Profile authorized for public display.'
  };
}

export function parseJsonLoose(text) {
  const start = text.indexOf('{');
  const end = text.lastIndexOf('}');

  if (start < 0 || end < start) {
    throw new Error('AI response did not contain JSON');
  }

  return JSON.parse(text.slice(start, end + 1));
}

export function readProfileFlavor(sheet, options = {}) {
  const provider = options.provider || process.env.SPARK_AI_PROVIDER || 'mock';

  if (provider === 'mock') {
    return {
      provider: 'mock',
      prompt: buildProfileFlavorPrompt(sheet),
      profile: mockProfileFlavor(sheet)
    };
  }

  if (provider === 'gemini-cli') {
    const command = process.env.GEMINI_BIN || 'gemini';
    const prompt = buildProfileFlavorPrompt(sheet);

    const output = execFileSync(command, ['-p', prompt], {
      encoding: 'utf8',
      timeout: 45000,
      maxBuffer: 1024 * 1024
    });

    return {
      provider: 'gemini-cli',
      prompt,
      profile: parseJsonLoose(output)
    };
  }

  throw new Error(`Unsupported AI provider: ${provider}`);
}
JS

cat > tests/ai/profile_flavor_reader.test.js << 'JS'
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
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})

scripts["test:ai"] = "node --test tests/ai/*.test.js"
scripts["test:mobile"] = scripts["test:mobile"] + " && npm run test:ai" if "test:ai" not in scripts.get("test:mobile", "") else scripts["test:mobile"]

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("AI_TEST_SCRIPT=PASS")
PY

npm run test:ai

echo "AI_PROFILE_FLAVOR_READER=PASS"
