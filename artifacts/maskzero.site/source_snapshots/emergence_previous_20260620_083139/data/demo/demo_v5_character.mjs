import fs from 'node:fs';

import {
  buildCharacterSheet,
  buildComicProfile
} from '../../packages/scoring-engine/index.js';

const responses = {};
for (let i = 1; i <= 36; i++) responses[i] = 'G';
for (let i = 73; i <= 78; i++) responses[i] = 'B';

const sheet = buildCharacterSheet(responses);
const profile = buildComicProfile(sheet);

const packet = {
  demo: true,
  generated_at: new Date().toISOString(),
  responses,
  sheet,
  profile
};

fs.writeFileSync(
  'data/demo/generated_characters/mind_t5_demo_character.json',
  JSON.stringify(packet, null, 2) + '\n'
);

console.log('SPARK_V5_DEMO=PASS');
console.log(`DOMAIN=${sheet.primary_domain}`);
console.log(`TIER=${sheet.tier}`);
console.log(`PERCENTILE=${sheet.percentile}`);
console.log(`THREAT=${sheet.threat_class}`);
console.log(`OUT=data/demo/generated_characters/mind_t5_demo_character.json`);
console.log('');
console.log(profile.cover_line);
for (const line of profile.sections) console.log(`- ${line}`);
