import {
  DOMAINS,
  DOMAIN_POWER_MAP,
  STRATEGIC_THREATS
} from "./powerRegistry.mjs";
import {
  validateDomainScores,
  validateFlavorScores
} from "./protocolValidator.mjs";

export function domainTier(score) {
  if (score >= 35) return "T5";
  if (score >= 24) return "T4";
  if (score >= 16) return "T3";
  if (score >= 7) return "T2";
  return "T1";
}

export function tierNumber(tier) {
  return Number(String(tier).replace("T", ""));
}

export function tierLabel(tierNum) {
  return `T${Math.max(1, tierNum)}`;
}

export function manifestationThreshold(highestScore) {
  return highestScore * 0.25;
}

export function remainingMaxPointsAfterTopScore(topScore, totalQuestions = 36) {
  const minAnswersUsedByTop = Math.max(0, topScore - 3);
  const remainingAnswers = Math.max(0, totalQuestions - minAnswersUsedByTop);
  return remainingAnswers * 2;
}

export function t5ForcesSoloManifestation(topScore) {
  return topScore >= 35 &&
    remainingMaxPointsAfterTopScore(topScore) < manifestationThreshold(topScore);
}

export function manifests(score, highestScore) {
  if (t5ForcesSoloManifestation(highestScore)) {
    return score === highestScore;
  }

  return score >= manifestationThreshold(highestScore);
}

export function percentile({
  highestTier,
  secondTier,
  manifestedPowerCount
}) {
  return Math.round(
    70 +
    (highestTier * 2.5) +
    (secondTier * 1) +
    manifestedPowerCount
  );
}

export function threatClassification(percentileScore) {
  if (percentileScore >= 89) return "City-Wide Potential";
  if (percentileScore >= 82) return "City Block-Level Potential";
  return "Building-Level Potential";
}

export function strategicTags(powerNames) {
  return powerNames.filter((name) => STRATEGIC_THREATS.has(name));
}

export function resolveFlavorPowers({
  domain,
  domainTierLabel,
  flavorScores
}) {
  validateFlavorScores(domain, flavorScores);

  const powers = DOMAIN_POWER_MAP[domain];
  if (!powers) {
    throw new Error(`Unknown domain power map: ${domain}`);
  }

  const domainTierNum = tierNumber(domainTierLabel);

  const indexedScores = flavorScores
    .map((score, idx) => ({
      name: powers[idx],
      flavorScore: score,
      index: idx
    }))
    .filter((entry) => entry.flavorScore > 0);

  if (indexedScores.length === 0) {
    return [{
      name: powers[0],
      tier: domainTierLabel,
      flavorScore: 0,
      domain,
      classification: "primary"
    }];
  }

  const topScore = Math.max(...indexedScores.map((entry) => entry.flavorScore));

  if (domainTierNum === 1) {
    const firstTop = indexedScores.find((entry) => entry.flavorScore === topScore);
    return [{
      name: firstTop.name,
      tier: "T1",
      flavorScore: firstTop.flavorScore,
      domain,
      classification: "primary"
    }];
  }

  if (topScore < 2) {
    const firstTop = indexedScores.find((entry) => entry.flavorScore === topScore);
    return [{
      name: firstTop.name,
      tier: domainTierLabel,
      flavorScore: firstTop.flavorScore,
      domain,
      classification: "primary"
    }];
  }

  const manifested = [];

  const topEntries = indexedScores.filter((entry) => entry.flavorScore === topScore);
  for (const entry of topEntries) {
    manifested.push({
      name: entry.name,
      tier: domainTierLabel,
      flavorScore: entry.flavorScore,
      domain,
      classification: topEntries.length > 1 ? "co-primary" : "primary"
    });
  }

  const remaining = indexedScores
    .filter((entry) => entry.flavorScore !== topScore && entry.flavorScore >= 2)
    .sort((a, b) => b.flavorScore - a.flavorScore || a.index - b.index);

  let depthSlot = 0;
  let cursor = 0;

  while (cursor < remaining.length && depthSlot < 2) {
    const slotScore = remaining[cursor].flavorScore;
    const tied = remaining.filter((entry) => entry.flavorScore === slotScore);
    const classification = depthSlot === 0
      ? (tied.length > 1 ? "co-secondary" : "secondary")
      : (tied.length > 1 ? "co-tertiary" : "tertiary");

    for (const entry of tied) {
      manifested.push({
        name: entry.name,
        tier: tierLabel(domainTierNum - (depthSlot + 1)),
        flavorScore: entry.flavorScore,
        domain,
        classification
      });
    }

    cursor += tied.length;
    depthSlot += 1;
  }

  return manifested;
}

export function buildCharacterFromQuiz({
  codename,
  domainScores,
  flavorScores
}) {
  validateDomainScores(domainScores, DOMAINS);

  const highestDomainScore = Math.max(...Object.values(domainScores));

  const manifestedDomains = DOMAINS
    .filter((domain) => manifests(domainScores[domain], highestDomainScore))
    .map((domain) => ({
      name: domain,
      score: domainScores[domain],
      tier: domainTier(domainScores[domain])
    }));

  const manifestedPowers = [];

  for (const domain of manifestedDomains) {
    const scores = flavorScores[domain.name];
    const powers = resolveFlavorPowers({
      domain: domain.name,
      domainTierLabel: domain.tier,
      flavorScores: scores
    });

    manifestedPowers.push(...powers);
  }

  const sortedTierNums = manifestedDomains
    .map((domain) => tierNumber(domain.tier))
    .sort((a, b) => b - a);

  const resultPercentile = percentile({
    highestTier: sortedTierNums[0] || 1,
    secondTier: sortedTierNums[1] || 0,
    manifestedPowerCount: manifestedPowers.length
  });

  return {
    name: codename,
    percentile: resultPercentile,
    threatClassification: threatClassification(resultPercentile),
    domains: manifestedDomains,
    powers: manifestedPowers,
    threatTags: strategicTags(manifestedPowers.map((power) => power.name))
  };
}
