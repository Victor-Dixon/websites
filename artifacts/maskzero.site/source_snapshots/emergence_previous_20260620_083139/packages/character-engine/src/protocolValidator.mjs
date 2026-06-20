export function assertInteger(value, label) {
  if (!Number.isInteger(value)) {
    throw new Error(`${label} must be an integer`);
  }
}

export function validateFlavorScores(domain, scores) {
  if (!Array.isArray(scores)) {
    throw new Error(`Missing flavor scores for manifested domain: ${domain}`);
  }

  if (scores.length !== 6) {
    throw new Error(`Flavor scores for ${domain} must contain exactly 6 sub-affinity scores`);
  }

  let total = 0;
  scores.forEach((score, idx) => {
    assertInteger(score, `${domain} flavor score ${idx}`);
    if (score < 0 || score > 6) {
      throw new Error(`${domain} flavor score ${idx} must be between 0 and 6`);
    }
    total += score;
  });

  if (total !== 6) {
    throw new Error(`Flavor scores for ${domain} must total exactly 6; got ${total}`);
  }

  return true;
}

export function validateDomainScores(domainScores, expectedDomains) {
  for (const domain of expectedDomains) {
    if (!(domain in domainScores)) {
      throw new Error(`Missing domain score: ${domain}`);
    }

    const score = domainScores[domain];
    assertInteger(score, `${domain} domain score`);

    if (score < 0 || score > 39) {
      throw new Error(`${domain} domain score must be between 0 and 39`);
    }
  }

  return true;
}
