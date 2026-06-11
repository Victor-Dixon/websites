export const SAVE_KEY = "digital_dreamscape_save_v1";

const SAVE_FIELDS = ["id", "name", "x", "y", "direction", "level", "xp", "currency", "visitedMarkers"];

export function loadSave(defaultState) {
  try {
    const raw = window.localStorage.getItem(SAVE_KEY);
    if (!raw) {
      return { ...defaultState, path: [] };
    }

    const saved = JSON.parse(raw);
    return {
      ...defaultState,
      ...saved,
      x: Number.isFinite(saved.x) ? saved.x : defaultState.x,
      y: Number.isFinite(saved.y) ? saved.y : defaultState.y,
      walking: false,
      path: [],
    };
  } catch {
    return { ...defaultState, walking: false, path: [] };
  }
}

export function savePlayerState(player) {
  const payload = SAVE_FIELDS.reduce((snapshot, key) => {
    if (player[key] !== undefined) {
      snapshot[key] = player[key];
    }
    return snapshot;
  }, {});

  payload.savedAt = new Date().toISOString();
  window.localStorage.setItem(SAVE_KEY, JSON.stringify(payload));
  return payload;
}

export function formatSaveStatus(savedAt = new Date()) {
  const date = savedAt instanceof Date ? savedAt : new Date(savedAt);
  return `Saved ${date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit", second: "2-digit" })}`;
}
