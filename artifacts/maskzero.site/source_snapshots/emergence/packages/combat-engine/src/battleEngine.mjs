import { createSeededRng } from "./rng.mjs";
import { tierNumber, tierInteraction } from "./tierMatrix.mjs";

const STRATEGIC_TAGS = new Set([
  "Duplication",
  "Pheromone Control",
  "Adaptive Biology",
  "Healing Factor",
  "Teleportation",
  "Shapeshifting",
  "Mind Control"
]);

function clamp(value, min, max) {
  return Math.max(min, Math.min(max, value));
}

function bestTier(fighter) {
  return Math.max(...fighter.powers.map((p) => tierNumber(p.tier)));
}

function strategicCount(fighter) {
  return (fighter.threatTags || []).filter((tag) => STRATEGIC_TAGS.has(tag)).length;
}

export function calculateBattleOdds(fighterA, fighterB, arena = {}) {
  const aTier = bestTier(fighterA);
  const bTier = bestTier(fighterB);

  let aOdds = 0.5;
  const tierGap = aTier - bTier;
  aOdds += tierGap * 0.13;

  aOdds += ((fighterA.powers?.length || 0) - (fighterB.powers?.length || 0)) * 0.03;
  aOdds += strategicCount(fighterA) * 0.07;
  aOdds -= strategicCount(fighterB) * 0.07;

  if (arena.favors === fighterA.name) aOdds += 0.08;
  if (arena.favors === fighterB.name) aOdds -= 0.08;

  aOdds = clamp(aOdds, 0.08, 0.92);

  return {
    fighterA: fighterA.name,
    fighterB: fighterB.name,
    odds: {
      [fighterA.name]: Number(aOdds.toFixed(2)),
      [fighterB.name]: Number((1 - aOdds).toFixed(2))
    },
    factors: {
      tierGap,
      fighterAPowerCount: fighterA.powers.length,
      fighterBPowerCount: fighterB.powers.length,
      fighterAStrategicTags: strategicCount(fighterA),
      fighterBStrategicTags: strategicCount(fighterB),
      arenaFavors: arena.favors || null
    }
  };
}

export function adjudicateBattle({ fighterA, fighterB, arena = {}, seed = "demo" }) {
  const rng = createSeededRng(seed);
  const odds = calculateBattleOdds(fighterA, fighterB, arena);
  const roll = Number(rng().toFixed(4));
  const aChance = odds.odds[fighterA.name];
  const winner = roll <= aChance ? fighterA : fighterB;
  const loser = winner.name === fighterA.name ? fighterB : fighterA;

  const attackerPower = winner.powers[0];
  const defenderPower = loser.powers[0];
  const interaction = tierInteraction(attackerPower.tier, defenderPower.tier);

  return {
    seed,
    roll,
    winner: winner.name,
    loser: loser.name,
    odds,
    mechanicalLog: [
      `${fighterA.name} best tier: T${Math.max(...fighterA.powers.map((p) => tierNumber(p.tier)))}`,
      `${fighterB.name} best tier: T${Math.max(...fighterB.powers.map((p) => tierNumber(p.tier)))}`,
      `${winner.name} selected by seeded roll ${roll} against ${aChance} threshold for ${fighterA.name}.`,
      `${attackerPower.name} ${attackerPower.tier} vs ${defenderPower.name} ${defenderPower.tier}: ${interaction.effect}.`
    ],
    interaction
  };
}
