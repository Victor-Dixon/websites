/**
 * runtime/spark-battle-sim/server/battleResolver.js
 * Deterministic-ish sealed battle resolver scaffold.
 * Public UI should never receive this packet directly.
 */

const ARENAS = {
  locations: [
    "City docks", "Abandoned warehouse", "Skyscraper rooftop", "Active construction site",
    "Countryside farm", "Downtown intersection", "Subway platform/tunnel", "River bridge",
    "Multi-level parking garage", "Shopping mall atrium", "Power substation", "Stadium field",
    "Forest edge/treeline", "Industrial refinery", "Quarry/gravel pit", "Hospital district",
    "Train yard", "Frozen reservoir", "Old town square", "Wind farm on open hill"
  ],
  times: ["Pre-dawn", "Morning", "Midday", "Afternoon", "Dusk", "Night"],
  weather: ["Clear", "Overcast", "Light rain", "Heavy rain/storm", "Fog", "High wind", "Snow/sleet", "Heat haze"],
  temperatures: ["Freezing", "Cold", "Cool", "Mild", "Warm", "Hot"],
  positions: ["Ground open", "Ground cover", "Elevated", "Below/sunken"],
  distances: ["Close", "Mid", "Long", "Maximum"]
};

function pick(list, rng = Math.random) {
  return list[Math.floor(rng() * list.length)];
}

function clamp(n, min, max) {
  return Math.max(min, Math.min(max, n));
}

function parseCC(sheetText) {
  const match = String(sheetText || "").match(/CC:\s*(\d+)/i);
  return match ? Number(match[1]) : 50;
}

function hasAny(text, words) {
  const low = String(text || "").toLowerCase();
  return words.some(w => low.includes(w.toLowerCase()));
}

function arenaFit(sheetText, arena) {
  let mod = 0;

  if (hasAny(sheetText, ["Wall-Crawling"]) && /warehouse|rooftop|parking|subway|mall|construction/i.test(arena.location)) mod += 8;
  if (hasAny(sheetText, ["Weather Control"]) && /open|field|farm|bridge|hill|reservoir|docks/i.test(arena.location)) mod += 8;
  if (hasAny(sheetText, ["Super Speed"]) && /ice|frozen|snow|sleet|fog|heavy rain/i.test(`${arena.location} ${arena.weather}`)) mod -= 8;
  if (hasAny(sheetText, ["Invisibility"]) && /night|fog|pre-dawn|overcast/i.test(`${arena.time} ${arena.weather}`)) mod += 6;
  if (hasAny(sheetText, ["Flight"]) && /open|field|farm|bridge|hill|stadium/i.test(arena.location)) mod += 6;
  if (hasAny(sheetText, ["Toxic Emission"]) && /warehouse|subway|mall|hospital|parking/i.test(arena.location)) mod += 10;
  if (hasAny(sheetText, ["Mind Control", "Telepathy"]) && /close|mid/i.test(arena.distance)) mod += 6;

  return clamp(mod, -25, 25);
}

function generateArena(rng = Math.random) {
  return {
    location: pick(ARENAS.locations, rng),
    time: pick(ARENAS.times, rng),
    weather: pick(ARENAS.weather, rng),
    temperature: pick(ARENAS.temperatures, rng),
    fighter1Position: pick(ARENAS.positions, rng),
    fighter2Position: pick(ARENAS.positions, rng),
    distance: pick(ARENAS.distances, rng)
  };
}

function resolveBattle({ fighter1, fighter2, sheet1, sheet2, rng = Math.random }) {
  if (!fighter1 || !fighter2) {
    throw new Error("resolveBattle requires fighter1 and fighter2 names.");
  }

  const arena = generateArena(rng);

  const cc1 = parseCC(sheet1);
  const cc2 = parseCC(sheet2);
  const baseGap = cc1 - cc2;

  const fit1 = arenaFit(sheet1, arena);
  const fit2 = arenaFit(sheet2, arena);
  const adjustedGap = baseGap + fit1 - fit2;

  const favorite = adjustedGap >= 0 ? fighter1 : fighter2;
  const underdog = adjustedGap >= 0 ? fighter2 : fighter1;

  const favoriteBand = clamp(50 + Math.abs(adjustedGap), 52, 95);
  const roll = Math.floor(rng() * 100) + 1;
  const favoriteWins = roll <= favoriteBand;
  const winner = favoriteWins ? favorite : underdog;
  const loser = favoriteWins ? underdog : favorite;

  const closeness =
    favoriteBand >= 90 ? "stomp" :
    favoriteBand >= 75 ? "favored" :
    favoriteBand >= 62 ? "competitive" :
    "tossup";

  const upset = winner === underdog;

  return {
    private: true,
    arena,
    favorite,
    underdog,
    winner,
    loser,
    closeness,
    upset,
    notes: {
      favoriteBand,
      roll,
      cc1,
      cc2,
      fit1,
      fit2,
      adjustedGap
    }
  };
}

module.exports = {
  generateArena,
  resolveBattle,
  parseCC,
  arenaFit
};
