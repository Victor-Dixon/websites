export const QUESTS = {
  strange_signal: {
    id: "strange_signal",
    title: "A Strange Signal",
    stages: {
      start: {
        id: "start",
        text: "The Guide speaks with urgency: \"A strange signal pulses from the Silver Observatory — and two factions are already arguing about what it means. The Dreamweavers say it's an ancient message. The Signal Corps say it's a system fault. Everyone is waiting for someone to act. What do you do?\"",
        choices: [
          {
            text: "Gather information quietly before choosing a side.",
            consequences: {
              stats: { knowledge: 10 },
              xp: 20,
              reputation: { dreamweavers: 5, signal_corps: 5 },
            },
            nextStage: "resolved",
          },
          {
            text: "Unite both groups and investigate together.",
            consequences: {
              stats: { leadership: 10 },
              xp: 20,
              reputation: { dreamweavers: 10, signal_corps: 10 },
            },
            nextStage: "resolved",
          },
        ],
      },
      resolved: {
        id: "resolved",
        terminal: true,
        text: "Your choice set things in motion. The signal still pulses — but now it has a witness. Your name is beginning to mean something in this place.",
      },
    },
    startStage: "start",
    completedDialogue: "The Guide nods: \"The signal situation is still unfolding. Thank you for how you handled it.\"",
  },
  missing_apprentice: {
    id: "missing_apprentice",
    title: "The Missing Apprentice",
    stages: {
      start: {
        id: "start",
        text: "The Cartographer grips your arm: \"My apprentice vanished near the Dream Archive three nights ago. The Ironforge Guild wants a systematic grid search. The Wildpath Collective says to ask the locals first — someone always sees something. I need them found. What's your move?\"",
        choices: [
          {
            text: "Run a methodical search of the Archive district.",
            consequences: {
              stats: { discipline: 10 },
              xp: 20,
              reputation: { ironforge: 10 },
            },
            nextStage: "resolved",
          },
          {
            text: "Talk to the people who live here. Someone saw something.",
            consequences: {
              stats: { connection: 10 },
              xp: 20,
              reputation: { wildpath: 10 },
            },
            nextStage: "resolved",
          },
        ],
      },
      resolved: {
        id: "resolved",
        terminal: true,
        text: "The apprentice is found — following a light no one else could see. You've earned trust in this district. And a lesson: different people solve the same problem in completely different ways.",
      },
    },
    startStage: "start",
    completedDialogue: "The Cartographer smiles: \"My apprentice is back at work. You have my gratitude — and my maps.\"",
  },
  broken_machine: {
    id: "broken_machine",
    title: "The Broken Machine",
    stages: {
      start: {
        id: "start",
        text: "The Gate Keeper exhales slowly: \"The waypoint network is down. People can't move between districts safely. The Luminary Order insists we understand *why* it failed before touching it. I say fix it now and ask questions later. Can you help?\"",
        choices: [
          {
            text: "Improvise a creative workaround and get it running.",
            consequences: {
              stats: { creativity: 10 },
              xp: 20,
              reputation: { dreamweavers: 5, wildpath: 5 },
            },
            nextStage: "resolved",
          },
          {
            text: "Find the root cause so this never happens again.",
            consequences: {
              stats: { purpose: 10 },
              xp: 20,
              reputation: { luminary: 10, signal_corps: 5 },
            },
            nextStage: "resolved",
          },
        ],
      },
      resolved: {
        id: "resolved",
        terminal: true,
        text: "The waypoints flicker back to life. The Gate Keeper nods slowly. Whether you patched it or understood it — the world moves again because of what you did.",
      },
    },
    startStage: "start",
    completedDialogue: "The Gate Keeper stands watch: \"Still running. Good work, traveller.\"",
  },
};

export function isQuestCompleted(player, questId) {
  return player.quests?.[questId] === "completed";
}

export function isQuestStarted(player, questId) {
  return Boolean(player.quests?.[questId]);
}

export function startQuestIfNew(player, questId) {
  if (!player.quests) player.quests = {};
  if (!player.quests[questId]) {
    player.quests[questId] = QUESTS[questId]?.startStage ?? "start";
  }
}

export function getActiveStage(player, questId) {
  const quest = QUESTS[questId];
  if (!quest) return null;
  const stageId = player.quests?.[questId] ?? quest.startStage;
  if (stageId === "completed") return null;
  return quest.stages[stageId] ?? null;
}

export function advanceQuest(player, questId, choiceIndex) {
  const quest = QUESTS[questId];
  if (!quest) return null;
  if (!player.quests) player.quests = {};

  const stageId = player.quests[questId] ?? quest.startStage;
  const stage = quest.stages[stageId];
  if (!stage?.choices) return null;

  const choice = stage.choices[choiceIndex];
  if (!choice) return null;

  const c = choice.consequences ?? {};
  if (c.xp) player.xp = (player.xp || 0) + c.xp;
  if (c.stats) {
    if (!player.stats) player.stats = {};
    Object.entries(c.stats).forEach(([k, v]) => {
      player.stats[k] = (player.stats[k] || 0) + v;
    });
  }
  if (c.reputation) {
    if (!player.reputation) player.reputation = {};
    Object.entries(c.reputation).forEach(([k, v]) => {
      player.reputation[k] = (player.reputation[k] || 0) + v;
    });
  }

  const nextStage = quest.stages[choice.nextStage];
  if (nextStage?.terminal) {
    player.quests[questId] = "completed";
  } else if (choice.nextStage) {
    player.quests[questId] = choice.nextStage;
  }

  return nextStage ?? null;
}

export function questSummary(player) {
  return Object.entries(QUESTS).map(([id, quest]) => {
    const stage = player.quests?.[id];
    let status = "available";
    if (stage === "completed") status = "completed";
    else if (stage) status = "in_progress";
    return { id, title: quest.title, status };
  });
}
