const DECKS = {
  hook: [
    "Survive the storm while the whole lobby reads every plan you type.",
    "Raid bosses where coordination only works if everyone watches the same chat wall.",
    "Battle royale — last squad standing, but every betrayal is public in the feed.",
    "Arena rounds: abilities unlock when enough players react to one message.",
    "Town defense: NPCs repeat the loudest player quote from global chat.",
    "Racing heat — shortcuts appear when the crowd spam-votes in open chat.",
    "Mystery rooms: the killer is whoever sent the most suspicious line everyone saw.",
    "Co-op extraction: loot splits based on promises made in the visible feed.",
  ],
  mechanic: [
    "No private whispers — only emotes and pings to reply without new lines.",
    "Messages fade after 10s unless three players ‘strike’ (like) them to keep them.",
    "Thunder strike pins one message to the top for the whole match.",
    "Typing during a storm blackout sends garbled text everyone still sees.",
    "Moderator role rotates — one player can mute a line for 30s, visible to all.",
    "Chat heat meter: too many caps messages trigger lightning that scrambles UI.",
    "Quote-reply chains show who answered whom in a threaded public view.",
    "Dead players can still chat as ghosts — living players see every haunt.",
  ],
  setting: [
    "Floating islands during an endless thunder season.",
    "Neon coliseum under a glass dome — crowd chat merged with player chat.",
    "Subway tunnels flooded; global feed is the only ‘radio’.",
    "Orbital station — oxygen drops when chat spam exceeds a threshold.",
    "Desert convoy: sandstorm hides avatars, not messages.",
    "Retro arcade lobby where every cabinet shares one CRT chat crawl.",
    "Sky city blocks — district chat merges into city-wide storm feed hourly.",
    "Underwater dome — bubbles are chat lines rising to the surface for all.",
  ],
  twist: [
    "Bots post fake player lines to bait the room — humans must call them out.",
    "Winning squad’s final chat log becomes the next map’s graffiti.",
    "Players vote to ‘banish’ one message per round — removed from history for all.",
    "Boss reads the top chat line aloud as its next attack pattern.",
    "Alliance mode: two squads share one feed but different colors.",
    "Hardcore: you can’t move until you’ve read the last 5 global messages.",
    "Streamer overlay mode exports the feed for spectators in real time.",
    "Seasonal rule: polite messages deal bonus damage; toxic lines heal bosses.",
  ],
};

const STORAGE_KEY = "xthunder_saved_pitches_v1";
const CHAT_KEY = "xthunder_demo_chat_v1";
const MAX_TRAPS_PER_ROUND = 10;
const trapRoundState = {
  round: 1,
  traps: [],
};

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
  const labels = { hook: "Core loop", mechanic: "Chat rule", setting: "World", twist: "Twist" };
  return ["hook", "mechanic", "setting", "twist"]
    .map((slot) => {
      const value = document.getElementById(`pitch-${slot}`)?.textContent?.trim() || "—";
      return `${labels[slot]}: ${value}`;
    })
    .join("\n");
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
  if (text.includes("Hit “Spin all”") || text.includes('Hit "Spin all"')) {
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

  saved.forEach((entry) => {
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
      status.textContent = "Copied.";
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

function loadChat() {
  try {
    return JSON.parse(localStorage.getItem(CHAT_KEY) || "[]");
  } catch {
    return [];
  }
}

function saveChat(messages) {
  localStorage.setItem(CHAT_KEY, JSON.stringify(messages.slice(-80)));
}

function escapeHtml(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function renderChat() {
  const feed = document.getElementById("chat-feed");
  if (!feed) return;

  const messages = loadChat();
  feed.innerHTML = "";

  if (!messages.length) {
    const empty = document.createElement("p");
    empty.className = "chat-empty";
    empty.textContent = "No messages yet — be the first crack of thunder.";
    feed.appendChild(empty);
    return;
  }

  messages.forEach((msg) => {
    const row = document.createElement("article");
    row.className = "chat-line";
    row.innerHTML = `
      <span class="chat-player">${escapeHtml(msg.player)}</span>
      <span class="chat-text">${escapeHtml(msg.text)}</span>
      <time class="chat-time">${new Date(msg.at).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })}</time>
    `;
    feed.appendChild(row);
  });

  feed.scrollTop = feed.scrollHeight;
}

function addChatLine(player, text) {
  const messages = loadChat();
  messages.push({
    player: player.trim().slice(0, 24) || "Player",
    text: text.trim().slice(0, 280),
    at: new Date().toISOString(),
  });
  saveChat(messages);
  renderChat();
}

function seedDemoChat() {
  const samples = [
    { player: "Nova", text: "Everyone push left — I see the whole lobby typing go!" },
    { player: "Brick", text: "Wait, you can all see that? I'm not hiding anything lol" },
    { player: "Zed", text: "Storm in 10s — read the feed, don't ping spam" },
    { player: "Mira", text: "This is why xThunder hits different. One chat. No secrets." },
  ];
  saveChat(samples);
  renderChat();
}

function clearChat() {
  localStorage.removeItem(CHAT_KEY);
  renderChat();
}

function renderTrapLimit() {
  const round = document.getElementById("trap-round");
  const count = document.getElementById("trap-count");
  const meter = document.getElementById("trap-meter-fill");
  const status = document.getElementById("trap-status");
  const log = document.getElementById("trap-log");
  const placeButton = document.getElementById("place-trap");
  const trapCount = trapRoundState.traps.length;
  const trapsRemaining = MAX_TRAPS_PER_ROUND - trapCount;

  if (round) round.textContent = `Round ${trapRoundState.round}`;
  if (count) count.textContent = `${trapCount} / ${MAX_TRAPS_PER_ROUND}`;
  if (meter) meter.style.width = `${(trapCount / MAX_TRAPS_PER_ROUND) * 100}%`;
  if (placeButton) placeButton.disabled = trapCount >= MAX_TRAPS_PER_ROUND;

  if (status) {
    status.textContent = trapCount >= MAX_TRAPS_PER_ROUND
      ? `Trap limit reached: ${MAX_TRAPS_PER_ROUND} traps placed this round. Start next round to place more.`
      : `${trapsRemaining} trap${trapsRemaining === 1 ? "" : "s"} available this round.`;
  }

  if (!log) return;
  log.innerHTML = "";

  if (!trapCount) {
    const empty = document.createElement("li");
    empty.className = "empty";
    empty.textContent = "No traps placed this round.";
    log.appendChild(empty);
    return;
  }

  trapRoundState.traps.forEach((trap) => {
    const item = document.createElement("li");
    item.textContent = `Trap ${trap.id} armed at ${trap.at}`;
    log.appendChild(item);
  });
}

function placeTrap() {
  if (trapRoundState.traps.length >= MAX_TRAPS_PER_ROUND) {
    renderTrapLimit();
    return;
  }

  trapRoundState.traps.push({
    id: trapRoundState.traps.length + 1,
    at: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }),
  });
  renderTrapLimit();
}

function resetTrapRound() {
  trapRoundState.round += 1;
  trapRoundState.traps = [];
  renderTrapLimit();
}

const year = document.querySelector("#year");
if (year) {
  year.textContent = new Date().getFullYear();
}

document.getElementById("spin-all")?.addEventListener("click", spinAll);
document.getElementById("save-pitch")?.addEventListener("click", savePitch);
document.getElementById("copy-pitch")?.addEventListener("click", copyPitch);
document.getElementById("clear-saved")?.addEventListener("click", clearSaved);
document.getElementById("spin-all-hero")?.addEventListener("click", () => {
  spinAll();
  document.getElementById("lab")?.scrollIntoView({ behavior: "smooth" });
});

document.querySelectorAll("[data-spin]").forEach((btn) => {
  btn.addEventListener("click", () => {
    const slot = btn.getAttribute("data-spin");
    if (slot) spinSlot(slot);
  });
});

document.getElementById("chat-form")?.addEventListener("submit", (e) => {
  e.preventDefault();
  const player = document.getElementById("player-name")?.value || "Player";
  const text = document.getElementById("chat-message")?.value || "";
  if (!text.trim()) return;
  addChatLine(player, text);
  const input = document.getElementById("chat-message");
  if (input) input.value = "";
});

document.getElementById("seed-demo")?.addEventListener("click", seedDemoChat);
document.getElementById("clear-chat")?.addEventListener("click", clearChat);
document.getElementById("place-trap")?.addEventListener("click", placeTrap);
document.getElementById("reset-traps")?.addEventListener("click", resetTrapRound);

renderSaved();
renderChat();
renderTrapLimit();
