export const FACTIONS = {
  dreamweavers: {
    id: "dreamweavers",
    name: "Dreamweavers",
    description: "Keepers of ancient knowledge and creative vision.",
    color: "#8d74ff",
  },
  ironforge: {
    id: "ironforge",
    name: "Ironforge Guild",
    description: "Masters of discipline and forged structure.",
    color: "#ffd166",
  },
  signal_corps: {
    id: "signal_corps",
    name: "Signal Corps",
    description: "Seekers of truth transmitted through purposeful signals.",
    color: "#62d9ff",
  },
  wildpath: {
    id: "wildpath",
    name: "Wildpath Collective",
    description: "Wanderers bound by deep connection to the living world.",
    color: "#76f0aa",
  },
  luminary: {
    id: "luminary",
    name: "Luminary Order",
    description: "Guides who lead with purpose and unwavering light.",
    color: "#ff7a8a",
  },
};

export const FACTION_IDS = Object.keys(FACTIONS);

export function createFactionReputation() {
  return Object.fromEntries(FACTION_IDS.map((id) => [id, 0]));
}

export function getFactionStanding(rep) {
  if (rep >= 100) return "Honored";
  if (rep >= 50) return "Friendly";
  if (rep >= 10) return "Neutral+";
  if (rep > -10) return "Neutral";
  if (rep >= -50) return "Unfriendly";
  return "Hostile";
}
