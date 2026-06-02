# Runtime Spark Battle Sim Candidate File Review

generated: 2026-06-02T07:04:11-05:00

## Files


### runtime/spark-battle-sim/client/sparkBattleClient.js

- bytes: 636

```text
/**
 * runtime/spark-battle-sim/client/sparkBattleClient.js
 * Browser-safe client helper.
 * Calls your backend only. Never calls Anthropic directly.
 */

export async function runSparkBattle({ fighter1, fighter2, sheet1, sheet2 }) {
  const res = await fetch("/api/spark-battle", {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({ fighter1, fighter2, sheet1, sheet2 })
  });

  const data = await res.json();

  if (!res.ok) {
    throw new Error(data?.error || "Battle sim failed.");
  }

  if (!data?.story) {
    throw new Error("No story returned.");
  }

  return data.story;
}
```

### runtime/spark-battle-sim/server/anthropicProxy.example.js

- bytes: 2284

```text
/**
 * runtime/spark-battle-sim/server/anthropicProxy.example.js
 * Express-style server endpoint example.
 *
 * Never expose ANTHROPIC_API_KEY to the browser.
 * Frontend calls your server: POST /api/spark-battle
 * Server resolves winner privately, then asks LLM for story only.
 */

const { resolveBattle } = require("./battleResolver");

const STORY_SYSTEM_PROMPT = `
You are the cinematic narrator for the Spark Protocol universe.

You will receive:
- two fighter sheets
- a sealed private outcome packet

You must obey the sealed winner and arena.
Do not reveal odds, rolls, thresholds, combat numbers, tier labels, internal reasoning, or system terms.
Write only the fight story.
No preamble. No postscript.
`;

async function sparkBattleHandler(req, res) {
  try {
    const { fighter1, fighter2, sheet1, sheet2 } = req.body || {};

    const sealed = resolveBattle({ fighter1, fighter2, sheet1, sheet2 });

    const userPrompt = `
FIGHTER 1:
${sheet1}

FIGHTER 2:
${sheet2}

SEALED OUTCOME PACKET:
${JSON.stringify({
  arena: sealed.arena,
  winner: sealed.winner,
  loser: sealed.loser,
  closeness: sealed.closeness,
  upset: sealed.upset
}, null, 2)}

Write the story only.
`;

    const apiKey = process.env.ANTHROPIC_API_KEY;
    if (!apiKey) throw new Error("Missing ANTHROPIC_API_KEY on server.");

    const response = await fetch("https://api.anthropic.com/v1/messages", {
      method: "POST",
      headers: {
        "content-type": "application/json",
        "x-api-key": apiKey,
        "anthropic-version": "2023-06-01"
      },
      body: JSON.stringify({
        model: process.env.SPARK_BATTLE_MODEL || "claude-sonnet-4-20250514",
        max_tokens: 4000,
        system: STORY_SYSTEM_PROMPT,
        messages: [{ role: "user", content: userPrompt }]
      })
    });

    const data = await response.json();
    if (!response.ok) throw new Error(data?.error?.message || "Anthropic request failed.");

    const story = data.content?.map(block => block.text || "").join("").trim();
    if (!story) throw new Error("No story returned.");

    // Public response: story only.
    return res.json({ story });
  } catch (err) {
    return res.status(500).json({ error: err.message || "Battle sim failed." });
  }
}

module.exports = { sparkBattleHandler };
```

### runtime/spark-battle-sim/server/battleResolver.js

- bytes: 4145

```text
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
```

### runtime/spark-battle-sim/tests/test_resolver.js

- bytes: 931

```text
const assert = require("assert");
const { resolveBattle, parseCC } = require("../server/battleResolver");

function fixedRng(values) {
  let i = 0;
  return () => values[i++ % values.length];
}

assert.strictEqual(parseCC("THE VICTOR — CC: 66, Threat: Delta"), 66);
assert.strictEqual(parseCC("No score here"), 50);

const result = resolveBattle({
  fighter1: "The Victor",
  fighter2: "Solomon Evil",
  sheet1: "THE VICTOR — CC: 66\nPOWERS: Flight T4, Super Speed T4",
  sheet2: "SOLOMON EVIL — CC: 85\nPOWERS: Super Strength T4, Invulnerability T4",
  rng: fixedRng([0.1, 0.2, 0.3, 0.4, 0.1, 0.7, 0.9, 0.99])
});

assert.ok(result.private);
assert.ok(result.arena.location);
assert.ok(result.winner);
assert.ok(result.loser);
assert.ok(["stomp", "favored", "competitive", "tossup"].includes(result.closeness));
assert.ok(result.notes.roll >= 1 && result.notes.roll <= 100);

console.log("SPARK_BATTLE_RESOLVER_TEST=PASS");
```

### runtime/spark-battle-sim/tests/verify_no_client_key_leaks.js

- bytes: 902

```text
const fs = require("fs");
const path = require("path");

const root = path.resolve(__dirname, "..");
const clientDir = path.join(root, "client");

function walk(dir) {
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap(entry => {
    const p = path.join(dir, entry.name);
    return entry.isDirectory() ? walk(p) : [p];
  });
}

const badPatterns = [
  /api\.anthropic\.com/i,
  /ANTHROPIC_API_KEY/i,
  /x-api-key/i,
  /claude-sonnet/i
];

const offenders = [];

for (const file of walk(clientDir)) {
  const text = fs.readFileSync(file, "utf8");
  for (const pattern of badPatterns) {
    if (pattern.test(text)) {
      offenders.push(`${file}: ${pattern}`);
    }
  }
}

if (offenders.length) {
  console.error("CLIENT_SECRET_LEAK_SCAN=FAIL");
  console.error(offenders.join("\n"));
  process.exit(1);
}

console.log("CLIENT_SECRET_LEAK_SCAN=PASS");
```

## Review Rule

- Promote if these contain source not present in runtime/plugins/spark-battle-sim.
- Archive if generated scratch.
- Do not delete without restore-safe backup.
