(function () {
  const STATE_KEY = "meridian_player_state_v1";
  const SPARK_KEY = "emergence_saved_character_v1";

  const FACTIONS = ["AEGIS", "Sterling", "Gods Armor", "Undercity"];
  const REP_RUNGS = ["Stranger", "Known", "Trusted", "Inner Circle"];

  function defaultState() {
    return {
      version: 1,
      notoriety: 0,
      reputation: Object.fromEntries(FACTIONS.map((f) => [f, 0])),
      completed_missions: [],
      accepted_missions: [],
      unlocked_nodes: [],
      current_district: "spindle",
      spark_linked: false,
      spark_name: null,
    };
  }

  function loadState() {
    try {
      const raw = localStorage.getItem(STATE_KEY);
      if (!raw) return defaultState();
      const parsed = JSON.parse(raw);
      return { ...defaultState(), ...parsed };
    } catch (_err) {
      return defaultState();
    }
  }

  function saveState(state) {
    localStorage.setItem(STATE_KEY, JSON.stringify(state));
  }

  function loadSparkLink() {
    try {
      const raw = localStorage.getItem(SPARK_KEY) || localStorage.getItem("spark_character_record");
      if (!raw) return null;
      const parsed = JSON.parse(raw);
      return parsed.spark_name || parsed.character_name || parsed.name || null;
    } catch (_err) {
      return null;
    }
  }

  function repRung(points) {
    if (points >= 9) return "Inner Circle";
    if (points >= 6) return "Trusted";
    if (points >= 3) return "Known";
    return "Stranger";
  }

  function missionAvailability(mission, state) {
    if (state.completed_missions.includes(mission.id)) return "completed";
    if (mission.hidden_district && state.notoriety < (mission.requires_notoriety || 50)) return "hidden";
    if ((mission.requires_notoriety || 0) > state.notoriety) return "locked";
    if (mission.initial_state === "locked" && (mission.requires_notoriety || 0) > state.notoriety) return "locked";
    return mission.initial_state === "hidden" ? "hidden" : "available";
  }

  function applyRepChanges(state, changes) {
    Object.entries(changes || {}).forEach(([faction, delta]) => {
      if (!(faction in state.reputation)) return;
      state.reputation[faction] = Math.max(0, Math.min(12, state.reputation[faction] + delta));
      if (faction === "Sterling" && delta > 0) state.reputation["Gods Armor"] = Math.max(0, state.reputation["Gods Armor"] - 1);
      if (faction === "Gods Armor" && delta > 0) state.reputation.Sterling = Math.max(0, state.reputation.Sterling - 1);
    });
  }

  async function fetchJson(path) {
    const res = await fetch(path, { cache: "no-store" });
    if (!res.ok) throw new Error(`Failed to load ${path}`);
    return res.json();
  }

  function renderFactionBar(state, root) {
    root.innerHTML = FACTIONS.map((faction) => {
      const pts = state.reputation[faction] || 0;
      return `<span class="chip">${faction}: ${repRung(pts)} (${pts})</span>`;
    }).join("");
  }

  function renderGrid(mapGrid, missions, state, gridRoot, logRoot) {
    gridRoot.innerHTML = "";
    const missionById = Object.fromEntries(missions.map((m) => [m.id, m]));

    mapGrid.districts.forEach((district) => {
      const cell = document.createElement("div");
      cell.className = "grid-cell" + (state.current_district === district.id ? " active-district" : "");
      cell.style.gridRow = String((district.position?.row || 0) + 1);
      cell.style.gridColumn = String((district.position?.col || 0) + 1);

      const nodes = (district.mission_nodes || []).map((node) => {
        const mission = missionById[node.mission_id];
        if (!mission) return "";
        const availability = missionAvailability(mission, state);
        if (availability === "hidden") return "";
        return `<div class="node ${availability}" data-mission="${mission.id}">
          <strong>${node.id}</strong><br>${mission.title}
          <div class="meta">Risk: ${mission.risk} · +${mission.notoriety_reward} notoriety</div>
        </div>`;
      }).join("");

      cell.innerHTML = `<div class="district-name">${district.name}</div>
        <div class="district-tag">${district.cells.map((c) => c.label).join(" · ")}</div>
        <div class="node-list">${nodes}</div>`;
      gridRoot.appendChild(cell);
    });
  }

  function renderMissions(missions, state, listRoot, logRoot) {
    listRoot.innerHTML = "";
    missions.forEach((mission) => {
      if (mission.hidden_district && missionAvailability(mission, state) === "hidden") return;
      const availability = missionAvailability(mission, state);
      const card = document.createElement("article");
      card.className = "mission-card";
      card.innerHTML = `
        <h3>${mission.title}</h3>
        <div class="meta">${mission.district} · ${mission.risk} risk · +${mission.notoriety_reward} notoriety</div>
        <p>${mission.summary}</p>
        <div class="faction-bar">${(mission.faction_hooks || []).map((f) => `<span class="chip">${f}</span>`).join("")}</div>
        <div class="meta">Status: ${availability}</div>
        <button type="button" data-accept="${mission.id}" ${availability !== "available" || !state.spark_linked ? "disabled" : ""}>
          ${availability === "completed" ? "Completed" : "Accept Mission"}
        </button>
        <button type="button" data-complete="${mission.id}" ${state.accepted_missions.includes(mission.id) ? "" : "disabled"}>
          Report Completion
        </button>
      `;
      listRoot.appendChild(card);
    });

    listRoot.querySelectorAll("[data-accept]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-accept");
        if (!state.spark_linked) {
          logRoot.textContent = "LINK SPARK REQUIRED — generate or load a Spark before accepting missions.";
          return;
        }
        if (!state.accepted_missions.includes(id)) state.accepted_missions.push(id);
        saveState(state);
        logRoot.textContent = `ACCEPTED ${id}`;
        renderAll();
      });
    });

    listRoot.querySelectorAll("[data-complete]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-complete");
        const mission = missions.find((m) => m.id === id);
        if (!mission || !state.accepted_missions.includes(id)) return;
        if (state.completed_missions.includes(id)) return;
        state.completed_missions.push(id);
        state.notoriety += mission.notoriety_reward || 0;
        applyRepChanges(state, mission.rep_changes);
        (mission.unlock || []).forEach((nodeId) => {
          if (!state.unlocked_nodes.includes(nodeId)) state.unlocked_nodes.push(nodeId);
        });
        state.accepted_missions = state.accepted_missions.filter((m) => m !== id);
        saveState(state);
        logRoot.textContent = `COMPLETED ${id} · notoriety=${state.notoriety}`;
        renderAll();
      });
    });
  }

  function renderUndercity(mapGrid, missions, state, root) {
    const hidden = mapGrid.hidden_layers?.[0];
    const mission = missions.find((m) => m.id === hidden?.mission_nodes?.[0]?.mission_id);
    const unlocked = state.notoriety >= (hidden?.unlock_requires_notoriety || 50);
    root.innerHTML = `
      <h2>Undercity — Hidden Layer</h2>
      <p class="meta">Not a normal map district. Access via tunnels, sewers, and refuge routes.</p>
      <p>${unlocked ? "ACCESS GRANTED — Gear Exchange reachable." : `LOCKED — requires ${hidden?.unlock_requires_notoriety || 50} notoriety (have ${state.notoriety}).`}</p>
      ${mission && unlocked ? `<div class="mission-card"><h3>${mission.title}</h3><p>${mission.summary}</p></div>` : ""}
    `;
    root.className = "panel undercity";
  }

  let state = loadState();
  let world = null;
  let missions = [];
  let mapGrid = null;

  const els = {
    notoriety: document.getElementById("notorietyValue"),
    spark: document.getElementById("sparkValue"),
    factionBar: document.getElementById("factionBar"),
    cityGrid: document.getElementById("cityGrid"),
    missionList: document.getElementById("missionList"),
    undercity: document.getElementById("undercityPanel"),
    log: document.getElementById("statusLog"),
  };

  function renderAll() {
    const sparkName = loadSparkLink();
    state.spark_linked = Boolean(sparkName);
    state.spark_name = sparkName;
    els.notoriety.textContent = String(state.notoriety);
    els.spark.textContent = sparkName || "Not linked";
    renderFactionBar(state, els.factionBar);
    renderGrid(mapGrid, missions, state, els.cityGrid, els.log);
    renderMissions(missions, state, els.missionList, els.log);
    renderUndercity(mapGrid, missions, state, els.undercity);
  }

  async function init() {
    try {
      [world, mapGrid, missions] = await Promise.all([
        fetchJson("data/world.json"),
        fetchJson("data/map-grid.json"),
        fetchJson("data/missions.json").then((d) => d.missions),
      ]);
      renderAll();
    } catch (err) {
      els.log.textContent = `BOOT ERROR: ${err.message}`;
    }
  }

  document.getElementById("resetState")?.addEventListener("click", () => {
    if (!confirm("Reset Meridian player state?")) return;
    state = defaultState();
    saveState(state);
    renderAll();
  });

  init();
})();
