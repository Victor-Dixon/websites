/* Digital Dreamscape — interaction popups */
(function (global) {
  "use strict";

  var overlayEl = null;
  var titleEl = null;
  var bodyEl = null;
  var actionsEl = null;
  var closeBtn = null;
  var onClose = null;

  function init(elements, closeCallback) {
    overlayEl = elements.overlay;
    titleEl = elements.title;
    bodyEl = elements.body;
    actionsEl = elements.actions;
    closeBtn = elements.closeBtn;
    onClose = closeCallback;
    if (closeBtn) {
      closeBtn.addEventListener("click", hide);
    }
    if (overlayEl) {
      overlayEl.addEventListener("click", function (e) {
        if (e.target === overlayEl) hide();
      });
    }
  }

  function show(title, body, actions) {
    if (!overlayEl) return;
    titleEl.textContent = title;
    bodyEl.textContent = body;
    actionsEl.innerHTML = "";
    (actions || []).forEach(function (act) {
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn" + (act.primary ? " primary" : "");
      btn.textContent = act.label;
      btn.addEventListener("click", function () {
        if (act.action) act.action();
        else hide();
      });
      actionsEl.appendChild(btn);
    });
    overlayEl.classList.remove("hidden");
  }

  function hide() {
    if (overlayEl) overlayEl.classList.add("hidden");
    if (onClose) onClose();
  }

  function triggerInteraction(obj, save, SAVE) {
    if (!obj || !obj.interaction) return;
    var inter = obj.interaction;
    var title = obj.name || "Interaction";
    var text = inter.text || "";
    SAVE.markVisited(save, obj.id);
    show(title, text, [{ label: "Close", primary: true, action: hide }]);
  }

  function findInteractable(world, x, y) {
    var i;
    for (i = 0; i < world.objects.length; i++) {
      var o = world.objects[i];
      if (o.x === x && o.y === y && o.interaction) return o;
    }
    return null;
  }

  function findAdjacentInteractable(world, px, py, tx, ty) {
    var obj = findInteractable(world, tx, ty);
    if (!obj) return null;
    var dist = Math.abs(px - tx) + Math.abs(py - ty);
    if (dist <= 1) return obj;
    return null;
  }

  function checkTileInteraction(world, player, save, SAVE) {
    var obj = findInteractable(world, player.x, player.y);
    if (obj) triggerInteraction(obj, save, SAVE);
  }

  global.DD_INTERACTIONS = {
    init: init,
    show: show,
    hide: hide,
    triggerInteraction: triggerInteraction,
    findInteractable: findInteractable,
    findAdjacentInteractable: findAdjacentInteractable,
    checkTileInteraction: checkTileInteraction
  };
})(typeof window !== "undefined" ? window : global);
