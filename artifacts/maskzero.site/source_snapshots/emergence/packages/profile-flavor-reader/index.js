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
