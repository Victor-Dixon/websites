(function () {
  "use strict";

  var DATA = window.PLANET_BLUE_DATA;
  var SAVE = window.PLANET_BLUE_SAVE;
  var WORLD = window.PLANET_BLUE_WORLD;
  var ABIL = window.PLANET_BLUE_ABILITIES;

  var save = SAVE.getOrCreateSave();
  WORLD.ensureWorldSystems(save);

  var form = document.getElementById("character-form");
  var previewEl = document.getElementById("stat-preview");
  var abilityEl = document.getElementById("ability-preview");
  var progressEl = document.getElementById("progress-display");
  var moralityEl = document.getElementById("morality-display");
  var nemesisEl = document.getElementById("nemesis-list");

  function renderMoralityBar() {
    if (!moralityEl) return;
    var score = save.morality.score;
    var pct = ((score + 100) / 200) * 100;
    moralityEl.innerHTML =
      "<div class=\"morality-bar-wrap\">" +
      "<div class=\"morality-bar\">" +
      "<div class=\"evil-side\"></div><div class=\"neutral-side\"></div><div class=\"good-side\"></div>" +
      "</div>" +
      "<div class=\"morality-marker\"><span style=\"left:" + pct + "%\"></span></div>" +
      "<p class=\"morality-label\">" + WORLD.alignmentLabel(save.morality.alignment) +
      " (" + (score > 0 ? "+" : "") + score + ")</p></div>";
  }

  function renderNemeses() {
    if (!nemesisEl) return;
    var active = save.nemesis.registry.filter(function (n) { return n.active; });
    if (!active.length) {
      nemesisEl.innerHTML = "<p class=\"muted\">No nemeses registered.</p>";
      return;
    }
    nemesisEl.innerHTML = active.map(function (n) {
      return "<div class=\"nemesis-card\">" +
        "<h4>" + n.displayName + "</h4>" +
        "<p>Kills vs you: " + n.killsVsPlayer + " · Power Lv " + n.powerLevel + "</p></div>";
    }).join("");
  }

  function populateSelects() {
    var raceSel = document.getElementById("race");
    var classSel = document.getElementById("class");

    Object.keys(DATA.RACES).forEach(function (id) {
      var opt = document.createElement("option");
      opt.value = id;
      opt.textContent = DATA.RACES[id].name;
      if (id === save.character.race) opt.selected = true;
      raceSel.appendChild(opt);
    });

    Object.keys(DATA.CLASSES).forEach(function (id) {
      var opt = document.createElement("option");
      opt.value = id;
      opt.textContent = DATA.CLASSES[id].name;
      if (id === save.character.class) opt.selected = true;
      classSel.appendChild(opt);
    });

    document.getElementById("name").value = save.character.name;
  }

  function updatePreview() {
    var raceId = document.getElementById("race").value;
    var classId = document.getElementById("class").value;
    var stats = DATA.computeStats(raceId, classId);
    var race = DATA.RACES[raceId];
    var cls = DATA.CLASSES[classId];

    previewEl.innerHTML =
      "<p><strong>HP</strong> " + stats.hp + "</p>" +
      "<p><strong>ATK</strong> " + stats.atk + "</p>" +
      "<p><strong>DEF</strong> " + stats.def + "</p>" +
      "<p><strong>Move</strong> " + stats.move + "</p>" +
      "<p><strong>Range</strong> " + stats.range + "</p>" +
      "<p class=\"muted\">" + race.desc + " · " + cls.desc + "</p>";

    var ability = ABIL.getAbilityForClass(classId);
    if (ability) {
      abilityEl.innerHTML =
        "<h3>" + ability.name + " <span class=\"badge\">Lv " + ability.level + "</span></h3>" +
        "<p>" + ability.desc + "</p>" +
        "<p class=\"muted\">Power " + ability.power + " · Range " + ability.range + " · " + ability.type + "</p>";
    } else {
      abilityEl.textContent = "No ability assigned.";
    }
  }

  function renderProgress() {
    if (!save.profileCreated) {
      progressEl.innerHTML = "<p class=\"muted\">Create your profile to begin.</p>";
      return;
    }
    progressEl.innerHTML =
      "<p>Level <strong>" + save.character.level + "</strong></p>" +
      "<p>XP <strong>" + save.character.xp + "</strong></p>" +
      "<p>Gold <strong>" + save.character.currency + "</strong></p>";
  }

  populateSelects();
  updatePreview();
  renderProgress();
  renderMoralityBar();
  renderNemeses();

  document.getElementById("race").addEventListener("change", updatePreview);
  document.getElementById("class").addEventListener("change", updatePreview);

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    save.character.name = document.getElementById("name").value.trim() || "Explorer";
    save.character.race = document.getElementById("race").value;
    save.character.class = document.getElementById("class").value;
    save.profileCreated = true;
    SAVE.saveGame(save);
    renderProgress();
    window.location.href = "world.html";
  });
})();
