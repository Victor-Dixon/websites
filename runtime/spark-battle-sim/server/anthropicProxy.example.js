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
