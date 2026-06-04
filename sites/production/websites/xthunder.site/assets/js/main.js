const DECKS = {
  hook: [
    "You inherit a broken arcade cabinet that predicts real-world storms.",
    "Every death rewinds the town, but NPCs remember your last run.",
    "Your weapon only charges when players tell the truth in voice chat.",
    "The dungeon is your childhood neighborhood, procedurally misremembered.",
    "Bosses are versions of you from timelines where you quit.",
    "You run a food truck that feeds adventurers between raids.",
    "The map is a live weather radar you must learn to read to survive.",
    "Companions are postcards from people who never got your reply.",
  ],
  mechanic: [
    "Combo-building by chaining opposite emotions (fear → courage → doubt).",
    "Deckbuilding where cards are rumors you spread across the town.",
    "Rhythm parries: block on the beat, punish on the off-beat.",
    "Territory painting — your color spreads when you solve micro-puzzles.",
    "Stealth where light sources are sound waves you sculpt.",
    "Crafting from trash the player photographs in real life (optional AR).",
    "Turn-based tactics on a shrinking grid (the storm closes in).",
    "Co-op async: one player plants traps, another triggers them days later.",
  ],
  setting: [
    "A neon coastal city during perpetual thunder season.",
    "Orbital greenhouse failing one biome at a time.",
    "Underground transit network turned dungeon after the blackout.",
    "Floating libraries where books are spells and overdue fees are curses.",
    "Desert festival that only exists for three in-game days per real week.",
    "Suburban cul-de-sac where every garage is a different genre.",
    "Sky islands tethered by lightning you can ride if timed right.",
    "Retro mall frozen in 2003, staffed by nostalgic ghosts.",
  ],
  twist: [
    "Winning makes the game harder for your future self (legacy scars).",
    "The shopkeeper is the final boss but only if you were rude.",
    "Friendly fire heals allies but damages your reputation stat.",
    "No jump button — movement is all teleport sketches you draw.",
    "Enemies learn from clips you export to social (mock integration).",
    "The true resource is attention; distractions drain your max HP.",
    "Every upgrade removes a UI element for minimalist mastery.",
    "Multiplayer votes on which law of physics applies each hour.",
  ],
};

const STORAGE_KEY = "xthunder_saved_pitches_v1";

function pick(deck) {
  return deck[Math.floor(Math.random() * deck.length)];
}

function spinSlot(slot) {
  const el = document.getElementById(`pitch-${slot}`);
  if (el && DECKS[slot]) {
    el.textContent = pick(DECKS[slot]);
  }
}

function spinAll() {
  Object.keys(DECKS).forEach(spinSlot);
}

function currentPitchText() {
  const parts = ["hook", "mechanic", "setting", "twist"].map((slot) => {
    const label = slot.charAt(0).toUpperCase() + slot.slice(1);
    const value = document.getElementById(`pitch-${slot}`)?.textContent?.trim() || "—";
    return `${label}: ${value}`;
  });
  return parts.join("\n");
}

function loadSaved() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
  } catch {
    return [];
  }
}

function savePitch() {
  const text = currentPitchText();
  if (text.includes("Tap “Spin full idea”")) {
    return;
  }
  const saved = loadSaved();
  saved.unshift({ text, at: new Date().toISOString() });
  localStorage.setItem(STORAGE_KEY, JSON.stringify(saved.slice(0, 20)));
  renderSaved();
}

function clearSaved() {
  localStorage.removeItem(STORAGE_KEY);
  renderSaved();
}

function renderSaved() {
  const list = document.getElementById("saved-list");
  if (!list) return;

  const saved = loadSaved();
  list.innerHTML = "";

  if (!saved.length) {
    const li = document.createElement("li");
    li.className = "empty";
    li.textContent = "No saved ideas yet.";
    list.appendChild(li);
    return;
  }

  saved.forEach((entry, index) => {
    const li = document.createElement("li");
    const pre = document.createElement("pre");
    pre.textContent = entry.text;
    const meta = document.createElement("span");
    meta.className = "saved-meta";
    meta.textContent = new Date(entry.at).toLocaleString();
    li.append(pre, meta);
    list.appendChild(li);
  });
}

async function copyPitch() {
  const status = document.getElementById("copy-status");
  try {
    await navigator.clipboard.writeText(currentPitchText());
    if (status) {
      status.hidden = false;
      setTimeout(() => {
        status.hidden = true;
      }, 2000);
    }
  } catch {
    if (status) {
      status.textContent = "Copy failed — select text manually.";
      status.hidden = false;
    }
  }
}

const year = document.querySelector("#year");
if (year) {
  year.textContent = new Date().getFullYear();
}

document.getElementById("spin-all")?.addEventListener("click", spinAll);
document.getElementById("spin-all-hero")?.addEventListener("click", () => {
  spinAll();
  document.getElementById("lab")?.scrollIntoView({ behavior: "smooth" });
});
document.getElementById("save-pitch")?.addEventListener("click", savePitch);
document.getElementById("copy-pitch")?.addEventListener("click", copyPitch);
document.getElementById("clear-saved")?.addEventListener("click", clearSaved);

document.querySelectorAll("[data-spin]").forEach((btn) => {
  btn.addEventListener("click", () => {
    const slot = btn.getAttribute("data-spin");
    if (slot) spinSlot(slot);
  });
});

renderSaved();
