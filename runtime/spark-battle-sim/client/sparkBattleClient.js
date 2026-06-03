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
