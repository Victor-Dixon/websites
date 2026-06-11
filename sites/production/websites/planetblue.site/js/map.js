(function () {
  "use strict";

  var DATA = window.PLANET_BLUE_DATA;
  var SAVE = window.PLANET_BLUE_SAVE;

  var save = SAVE.getOrCreateSave();
  if (!save.profileCreated) {
    window.location.href = "character.html";
    return;
  }

  var profileEl = document.getElementById("profile-summary");
  var nodesEl = document.getElementById("mission-nodes");

  function renderProfile() {
    var ch = save.character;
    var race = DATA.RACES[ch.race];
    var cls = DATA.CLASSES[ch.class];
    var stats = DATA.computeStats(ch.race, ch.class);
    profileEl.innerHTML =
      "<h3>" + ch.name + "</h3>" +
      "<p class=\"muted\">" + race.name + " · " + cls.name + " · Lv " + ch.level + "</p>" +
      "<p>XP: <strong>" + ch.xp + "</strong></p>" +
      "<p>Currency: <strong>" + ch.currency + "</strong></p>" +
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
      var card = document.createElement("article");
      card.className = "mission-card " + status;

      var rewards = "+" + m.rewards.xp + " XP, +" + m.rewards.currency + " cr";
      card.innerHTML =
        "<div class=\"mission-order\">" + (index + 1) + "</div>" +
        "<div class=\"mission-body\">" +
        "<h3>" + m.name + "</h3>" +
        "<p class=\"muted\">" + m.desc + "</p>" +
        "<p class=\"rewards\">Rewards: " + rewards + "</p>" +
        "</div>" +
        "<div class=\"mission-action\"></div>";

      var actionWrap = card.querySelector(".mission-action");

      if (status === "completed") {
        var badge = document.createElement("span");
        badge.className = "badge completed";
        badge.textContent = "Completed";
        actionWrap.appendChild(badge);
      } else if (status === "unlocked") {
        var btn = document.createElement("a");
        btn.className = "btn primary";
        btn.href = "battle.html?mission=" + id;
        btn.textContent = "Engage";
        actionWrap.appendChild(btn);
      } else {
        var lock = document.createElement("span");
        lock.className = "badge locked";
        lock.textContent = "Locked";
        actionWrap.appendChild(lock);
      }

      nodesEl.appendChild(card);
    });
  }

  renderProfile();
  renderMissions();
})();
