(function () {
  "use strict";

  const FEED_URL = "/data/closeout-feed.json";

  function statusClass(status) {
    const normalized = String(status || "").toUpperCase();
    if (["PASS", "CLOSED", "DONE", "SHIPPED", "REPORT_ONLY_PASS"].includes(normalized)) {
      return "ok";
    }
    if (["FAIL", "BLOCKED"].includes(normalized)) {
      return "block";
    }
    if (["PARTIAL", "WARN", "WARNING"].includes(normalized)) {
      return "warn";
    }
    return "";
  }

  function formatTime(iso) {
    if (!iso) return "";
    try {
      const d = new Date(iso);
      if (Number.isNaN(d.getTime())) return iso;
      return d.toLocaleString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
      });
    } catch (_err) {
      return iso;
    }
  }

  function el(tag, attrs, children) {
    const node = document.createElement(tag);
    if (attrs) {
      Object.entries(attrs).forEach(([k, v]) => {
        if (k === "className") node.className = v;
        else if (k === "text") node.textContent = v;
        else node.setAttribute(k, v);
      });
    }
    (children || []).forEach((c) => {
      if (typeof c === "string") node.appendChild(document.createTextNode(c));
      else if (c) node.appendChild(c);
    });
    return node;
  }

  function renderCard(item) {
    const badge = el("span", {
      className: "badge " + statusClass(item.status),
      text: item.status || "UNKNOWN",
    });

    const metaParts = [
      item.lane ? "Lane: " + item.lane : "",
      item.repo ? "Repo: " + item.repo : "",
      item.device_hint ? "Device: " + item.device_hint : "",
      item.timestamp ? formatTime(item.timestamp) : "",
    ].filter(Boolean);

    const children = [
      badge,
      el("h3", { text: item.title || item.task_id || "Closeout" }),
      el("p", { text: item.summary || "" }),
    ];

    const tasks = item.tasks_completed || item.actions_taken || [];
    if (tasks.length) {
      const ul = el("ul");
      tasks.slice(0, 5).forEach((action) => ul.appendChild(el("li", { text: action })));
      children.push(ul);
    }

    if (item.verification) {
      children.push(el("p", { className: "feed-verify", text: "Verification: " + item.verification }));
    }

    if (item.commit_refs && item.commit_refs.length) {
      children.push(el("p", { className: "feed-meta", text: "Commits: " + item.commit_refs.join(", ") }));
    }

    if (metaParts.length) {
      children.push(el("p", { className: "feed-meta", text: metaParts.join(" · ") }));
    }

    if (item.feed_id) {
      children.push(el("p", { className: "feed-id", text: "feed_id: " + item.feed_id }));
    }

    return el("article", { className: "closeout-card" }, children);
  }

  async function loadFeed() {
    const grid = document.getElementById("closeout-feed-grid");
    const empty = document.getElementById("closeout-feed-empty");
    const syncMeta = document.getElementById("sync-meta");
    if (!grid) return;

    try {
      const res = await fetch(FEED_URL, { cache: "no-store" });
      if (!res.ok) throw new Error("HTTP " + res.status);
      const data = await res.json();
      const items = Array.isArray(data.items) ? data.items : [];
      grid.innerHTML = "";

      if (!items.length) {
        if (empty) empty.hidden = false;
        if (syncMeta) syncMeta.textContent = "Feed empty — run export_closeout_feed.py";
        return;
      }
      if (empty) empty.hidden = true;

      items.forEach((item) => grid.appendChild(renderCard(item)));

      if (syncMeta && data.generated_at) {
        syncMeta.textContent =
          "Synced " + formatTime(data.generated_at) + " · " + items.length + " closeout(s)";
      }
    } catch (err) {
      if (empty) {
        empty.hidden = false;
        empty.textContent =
          "Closeout feed unavailable — deploy data/closeout-feed.json or run export_closeout_feed.py.";
      }
      if (syncMeta) syncMeta.textContent = "Feed offline";
      console.warn("closeout feed load failed", err);
    }
  }

  document.addEventListener("DOMContentLoaded", loadFeed);
})();
