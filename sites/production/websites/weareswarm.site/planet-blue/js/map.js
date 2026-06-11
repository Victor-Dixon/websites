(function () {
  "use strict";

  var DATA = window.PLANET_BLUE_DATA;
  var SAVE = window.PLANET_BLUE_SAVE;
  var WORLD = window.PLANET_BLUE_WORLD;

  var save = SAVE.getOrCreateSave();
  WORLD.ensureWorldSystems(save);
  SAVE.syncMissionUnlocks(save);
  WORLD.initQuestLog(save);
  SAVE.saveGame(save);

  if (!save.profileCreated) {
    window.location.href = "character.html";
    return;
  }

  var profileEl = document.getElementById("profile-summary");
  var nodesEl = document.getElementById("mission-nodes");
  var zoneListEl = document.getElementById("zone-list");
  var moralityEl = document.getElementById("morality-display");
  var nemesisEl = document.getElementById("nemesis-list");
  var questEl = document.getElementById("quest-entries");
  var dialogueEl = document.getElementById("world-dialogue");
  var greetingEl = document.getElementById("map-greeting");

  function renderMoralityBar(container) {
    var score = save.morality.score;
    var pct = ((score + 100) / 200) * 100;
    container.innerHTML =
      "<div class=\"morality-bar-wrap\">" +
      "<div class=\"morality-bar\">" +
      "<div class=\"evil-side\"></div><div class=\"neutral-side\"></div><div class=\"good-side\"></div>" +
      "</div>" +
      "<div class=\"morality-marker\"><span style=\"left:" + pct + "%\"></span></div>" +
      "<p class=\"morality-label\">" + WORLD.alignmentLabel(save.morality.alignment) +
      " (" + (score > 0 ? "+" : "") + score + ")</p></div>";
  }

  function renderZones() {
    zoneListEl.innerHTML = "";
    Object.keys(save.world.zones).forEach(function (id) {
      var z = save.world.zones[id];
      var status = WORLD.zoneStatus(z.safety);
      var row = document.createElement("div");
      row.className = "zone-row";
      row.innerHTML =
        "<div class=\"zone-header\">" +
        "<span class=\"zone-name\">" + z.name + "</span>" +
        "<span class=\"badge " + status + "\">" + WORLD.zoneStatusLabel(status) + "</span>" +
        "</div>" +
        "<div class=\"influence-bar\">" +
        "<div class=\"safety-fill\" style=\"width:" + z.safety + "%\"></div>" +
        "<div class=\"threat-fill\" style=\"width:" + z.threat + "%\"></div>" +
        "</div>" +
        "<div class=\"influence-labels\">" +
        "<span>Safe " + z.safety + "%</span><span>Threat " + z.threat + "%</span>" +
        "</div>";
      zoneListEl.appendChild(row);
    });
  }

  function renderNemeses() {
    var active = save.nemesis.registry.filter(function (n) { return n.active; });
    if (!active.length) {
      nemesisEl.innerHTML = "<p class=\"muted\">No nemeses yet. Survive a narrow fight or fall in battle.</p>";
      return;
    }
    nemesisEl.innerHTML = active.map(function (n) {
      return "<div class=\"nemesis-card\">" +
        "<h4>" + n.displayName + "</h4>" +
        "<p>Zone: " + (save.world.zones[n.zoneId] ? save.world.zones[n.zoneId].name : n.zoneId) + "</p>" +
        "<p>Power Lv " + n.powerLevel + " · Encounters: " + n.encounters + "</p>" +
        "<p>Resists: " + (n.lastAbilityUsed || "none") + "</p></div>";
    }).join("");
  }

  function renderQuests() {
    questEl.innerHTML = "";
    save.quests.log.forEach(function (q) {
      var entry = document.createElement("div");
      entry.className = "quest-entry " + q.status;
      entry.innerHTML = "<h4>" + q.title + "</h4><p>" + q.body + "</p>";
      questEl.appendChild(entry);
    });
  }

  function renderProfile() {
    var ch = save.character;
    var race = DATA.RACES[ch.race];
    var cls = DATA.CLASSES[ch.class];
    var stats = DATA.computeStats(ch.race, ch.class);
    profileEl.innerHTML =
      "<h3>" + ch.name + "</h3>" +
      "<p class=\"muted\">" + race.name + " · " + cls.name + " · Lv " + ch.level + "</p>" +
      "<p>XP: <strong>" + ch.xp + "</strong></p>" +
      "<p>Gold: <strong>" + ch.currency + "</strong></p>" +
      "<p class=\"stat-mini\">HP " + stats.hp + " · ATK " + stats.atk + " · DEF " + stats.def + "</p>";
  }

  function renderMissions() {
    var ids = Object.keys(DATA.MISSIONS).sort(function (a, b) {
      return DATA.MISSIONS[a].order - DATA.MISSIONS[b].order;
    });

    nodesEl.innerHTML = "";

    ids.forEach(function (id, index) {
      var m = DATA.MISSIONS[id];
      var status = save.missions[id] || "locked";
      var moralOk = WORLD.missionAllowedByMorality(save, id);
      var card = document.createElement("article");
      card.className = "mission-card " + status;

      var zoneId = DATA.MISSION_ZONE[id];
      var zone = zoneId ? save.world.zones[zoneId] : null;
      var zoneHint = zone ? " · Zone: " + WORLD.zoneStatusLabel(WORLD.zoneStatus(zone.safety)) : "";

      var rewards = "+" + m.rewards.xp + " XP, +" + m.rewards.currency + " gp";
      card.innerHTML =
        "<div class=\"mission-order\">" + (index + 1) + "</div>" +
        "<div class=\"mission-body\">" +
        "<h3>" + m.name + "</h3>" +
        "<p class=\"muted\">" + m.desc + zoneHint + "</p>" +
        "<p class=\"rewards\">Rewards: " + rewards + "</p>" +
        (!moralOk ? "<p class=\"muted\" style=\"color:var(--evil)\">Requires " + DATA.MORALITY_GATES[id] + " alignment</p>" : "") +
        "</div>" +
        "<div class=\"mission-action\"></div>";

      var actionWrap = card.querySelector(".mission-action");

      if (status === "completed") {
        var badge = document.createElement("span");
        badge.className = "badge completed";
        badge.textContent = "Complete";
        actionWrap.appendChild(badge);
      } else if (status === "unlocked" && moralOk) {
        var btn = document.createElement("a");
        btn.className = "btn primary";
        btn.href = "battle.html?mission=" + id;
        btn.textContent = "Engage";
        actionWrap.appendChild(btn);
      } else if (!moralOk) {
        var lockM = document.createElement("span");
        lockM.className = "badge locked";
        lockM.textContent = "Alignment";
        actionWrap.appendChild(lockM);
      } else {
        var lock = document.createElement("span");
        lock.className = "badge locked";
        lock.textContent = "Locked";
        actionWrap.appendChild(lock);
      }

      nodesEl.appendChild(card);
    });
  }

  if (dialogueEl) {
    dialogueEl.textContent = WORLD.getMoralityDialogue(save, "map_greeting");
  }
  if (greetingEl) {
    greetingEl.textContent = "World Map — " + save.character.name;
  }

  renderProfile();
  renderMoralityBar(moralityEl);
  renderZones();
  renderNemeses();
  renderQuests();
  renderMissions();
})();
