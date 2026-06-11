const COLS = "ABCDEFGHIJKLMNOP".split("");
const ROWS = Array.from({ length: 15 }, (_, i) => String(i + 1));

const grid = document.querySelector("[data-grid]");
const panel = document.querySelector("[data-panel]");
const panelTitle = document.querySelector("[data-panel-title]");
const panelBody = document.querySelector("[data-panel-body]");

let missions = [];
let news = [];

function coordFor(index) {
  const col = COLS[index % COLS.length];
  const row = ROWS[Math.floor(index / COLS.length)];
  return `${col}${row}`;
}

function normalizeCoord(value) {
  return String(value || "").trim().toUpperCase();
}

function missionsAt(coord) {
  return missions.filter(m => normalizeCoord(m.coord) === coord);
}

function newsAt(coord) {
  return news.filter(n => normalizeCoord(n.coord) === coord);
}

function clearSelectedCells() {
  document.querySelectorAll(".map-cell.is-selected").forEach(cell => {
    cell.classList.remove("is-selected");
  });
}

function selectCell(coord) {
  clearSelectedCells();

  const cell = document.querySelector(`[data-coord="${coord}"]`);
  if (!cell) return false;

  cell.classList.add("is-selected");
  cell.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
  return true;
}

function renderMissionCard(m) {
  return `
    <article class="mission-card mission-${m.status}">
      <div class="mission-meta">${m.type} · ${m.status} · threat: ${m.threat || "unknown"}</div>
      <h3>${m.title}</h3>
      <p><strong>${m.district}</strong></p>
      <p>${m.description}</p>
      <p class="reward">Reward: ${m.reward || "World state update"}</p>
      <button class="mission-btn">Open Mission</button>
    </article>
  `;
}

function renderNewsCard(n) {
  return `
    <article class="mission-card news-card news-${n.status}">
      <div class="mission-meta">Ledger · ${n.type} · ${n.status}</div>
      <h3>${n.headline}</h3>
      <p><strong>${n.district}</strong></p>
      <p>${n.summary}</p>
      <a class="mission-btn" href="./news/#${encodeURIComponent(n.id)}">Open Newspaper Story</a>
    </article>
  `;
}

function updateUrlSector(coord) {
  const url = new URL(window.location.href);
  url.searchParams.set("sector", coord);
  window.history.replaceState({}, "", url);
}

function renderPanel(coord, updateUrl = true) {
  const cleanCoord = normalizeCoord(coord);
  const foundMissions = missionsAt(cleanCoord);
  const foundNews = newsAt(cleanCoord);

  panel.classList.add("is-open");
  selectCell(cleanCoord);

  if (updateUrl) {
    updateUrlSector(cleanCoord);
  }

  const sections = [];

  if (foundMissions.length) {
    sections.push(`
      <h3 class="panel-section-title">Missions</h3>
      ${foundMissions.map(renderMissionCard).join("")}
    `);
  }

  if (foundNews.length) {
    sections.push(`
      <h3 class="panel-section-title">Related Newspaper Updates</h3>
      ${foundNews.map(renderNewsCard).join("")}
    `);
  }

  if (!sections.length) {
    panelTitle.textContent = `${cleanCoord} — Quiet Sector`;
    panelBody.innerHTML = `
      <p>This sector has no active missions or newspaper reports yet.</p>
      <div id="sector-atmosphere-panel"></div>
      <a class="mission-btn" href="./news/">Open The Meridian Ledger</a>
    `;
    renderAtmospherePanel(cleanCoord);
    return;
  }

  panelTitle.textContent = `${cleanCoord} — ${foundMissions.length} mission(s), ${foundNews.length} news update(s)`;
  panelBody.innerHTML = `
    ${sections.join("")}
    <div id="sector-atmosphere-panel"></div>
    <p class="panel-footnote">
      <a href="./news/">Read full Meridian Ledger</a>
    </p>
  `;
  renderAtmospherePanel(cleanCoord);
}

function renderAtmospherePanel(coord) {
  const mount = document.getElementById("sector-atmosphere-panel");
  if (!mount || !window.MeridianWorld) return;

  window.MeridianWorld.fetchWorld()
    .then(function (world) {
      const state = world && world.coords ? world.coords[coord] : null;
      if (!state || !state.responses) {
        mount.innerHTML = "<p><em>No Spark atmosphere recorded here yet. File a Dispatch response to influence this sector.</em></p>";
        return;
      }
      mount.innerHTML =
        '<article class="mission-card">' +
        '<div class="mission-meta">Collective Spark Atmosphere</div>' +
        "<h3>" + coord + " · " + String(state.atmosphere || "neutral") + "</h3>" +
        "<p><strong>" + state.responses + "</strong> player response(s) filed here.</p>" +
        (state.last_headline ? "<p>Latest headline: " + state.last_headline + "</p>" : "") +
        (state.last_spark ? "<p>Last Spark: " + state.last_spark + " · " + String(state.last_result || "") + "</p>" : "") +
        '<a class="mission-btn" href="/meridian-dispatch/">Answer The Dispatch</a>' +
        "</article>";
    })
    .catch(function () {
      mount.innerHTML = "";
    });
}

function buildGrid() {
  const total = COLS.length * ROWS.length;

  for (let i = 0; i < total; i++) {
    const coord = coordFor(i);
    const cell = document.createElement("button");
    cell.className = "map-cell";
    cell.dataset.coord = coord;
    cell.setAttribute("aria-label", `Map sector ${coord}`);
    cell.textContent = coord;

    cell.addEventListener("click", () => renderPanel(coord, true));

    grid.appendChild(cell);
  }
}

function markCells() {
  for (const mission of missions) {
    const coord = normalizeCoord(mission.coord);
    const cell = document.querySelector(`[data-coord="${coord}"]`);
    if (cell) {
      cell.classList.add("has-mission", `status-${mission.status}`);
      cell.title = mission.title;
    }
  }

  for (const item of news) {
    const coord = normalizeCoord(item.coord);
    const cell = document.querySelector(`[data-coord="${coord}"]`);
    if (cell) {
      cell.classList.add("has-news", `news-${item.status}`);
      cell.title = cell.title ? `${cell.title} · ${item.headline}` : item.headline;
    }
  }
}

async function fetchJson(path, fallback) {
  try {
    const response = await fetch(path, { cache: "no-store" });
    if (!response.ok) throw new Error(`${path} HTTP ${response.status}`);
    return await response.json();
  } catch (err) {
    console.warn(err);
    return fallback;
  }
}

async function boot() {
  [missions, news] = await Promise.all([
    fetchJson("/assets/data/missions.json", []),
    fetchJson("./news/newsfeed.json", [])
  ]);

  buildGrid();
  markCells();

  const url = new URL(window.location.href);
  const requestedSector = normalizeCoord(url.searchParams.get("sector"));

  if (requestedSector) {
    renderPanel(requestedSector, false);
  }
}

boot().catch(err => {
  panel.classList.add("is-open");
  panelTitle.textContent = "Map failed to load";
  panelBody.textContent = err.message;
});
