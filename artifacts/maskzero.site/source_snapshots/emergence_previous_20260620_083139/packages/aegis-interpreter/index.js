export function buildAegisPrompt({ quiz, responses, results }) {
  return [
    'You are AEGIS Classification Services for Spark Protocol.',
    'Read the user quiz results and produce a concise Discord-ready classification.',
    'Do not invent final powers yet. Scoring engine is pending.',
    'Use only the provided answer distribution and completion state.',
    '',
    `Quiz: ${quiz.title}`,
    `Form: ${quiz.form_id}`,
    `Version: ${quiz.version}`,
    `Answered: ${results.total_answered}/${results.total_questions}`,
    `Primary answer signal: ${results.primary}`,
    `Answer distribution: ${JSON.stringify(results.counts)}`,
    `Locked: ${results.locked}`,
    `Responses: ${JSON.stringify(responses)}`,
    '',
    'Return JSON only with:',
    '{',
    '  "classification_title": string,',
    '  "summary": string,',
    '  "primary_signal": string,',
    '  "risk_note": string,',
    '  "next_step": string',
    '}'
  ].join('\n');
}

export function mockAegisInterpretation({ results }) {
  const primary = results.primary || 'UNKNOWN';

  const signalMap = {
    A: 'Command / Structure Signal',
    B: 'Intensity / Emergence Signal',
    C: 'Velocity / Reflex Signal',
    D: 'Anchor / Endurance Signal',
    E: 'Adaptive / Empathic Signal',
    F: 'Specter / Concealment Signal'
  };

  return {
    classification_title: 'AEGIS Preliminary Classification',
    summary:
      'The subject completed the Spark Protocol classification sequence. Current output is a pre-scoring interpretation based on answer distribution only.',
    primary_signal: signalMap[primary] || 'Unresolved Signal',
    risk_note:
      results.locked
        ? 'Sheet lock is eligible. Final domain/flavor scoring engine required before power manifestation.'
        : 'Classification incomplete. More responses required before lock.',
    next_step:
      'Run scoring engine lane to convert answer map into domain scores, flavor vectors, tier, threat class, and immutable sheet.'
  };
}

export async function interpretQuizResult({ quiz, responses, results }) {
  if (!process.env.OPENAI_API_KEY && !process.env.ANTHROPIC_API_KEY) {
    return {
      provider: 'mock',
      prompt: buildAegisPrompt({ quiz, responses, results }),
      interpretation: mockAegisInterpretation({ results })
    };
  }

  return {
    provider: 'pending-live-api',
    prompt: buildAegisPrompt({ quiz, responses, results }),
    interpretation: mockAegisInterpretation({ results })
  };
}
