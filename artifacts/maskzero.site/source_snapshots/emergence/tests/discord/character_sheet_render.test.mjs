import assert from 'node:assert/strict';
import test from 'node:test';

import { renderLockedCharacterSheetMarkdown } from '../../packages/discord-platform/src/renderLockedCharacterSheetMarkdown.mjs';

test('renderLockedCharacterSheetMarkdown is deterministic and includes critical fields', () => {
  const locked = {
    discordUsername: 'Architect',
    activeRole: 'player',
    character: {
      name: 'Architect',
      percentile: 91,
      threatClassification: 'Paragon',
      domains: [{ name: 'mind', score: 39, tier: 5 }],
      powers: [{ name: 'Mind Control', tier: 5, classification: 'primary', domain: 'mind' }],
      threatTags: ['Strategic Threat']
    }
  };

  const out1 = renderLockedCharacterSheetMarkdown(locked);
  const out2 = renderLockedCharacterSheetMarkdown(locked);

  assert.equal(out1, out2);
  assert.ok(out1.includes('Architect'));
  assert.ok(out1.includes('Percentile'));
  assert.ok(out1.includes('Paragon'));
  assert.ok(out1.includes('Mind Control'));
  assert.ok(out1.includes('Strategic Threat'));
});
