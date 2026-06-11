/* Planet Blue — friendly nav labels (never show .html to players) */
(function () {
  "use strict";

  var PAGE_LABELS = {
    "index.html": "Home",
    "character.html": "Character",
    "world.html": "Explore",
    "map.html": "World Map",
    "battle.html": "Battle"
  };

  function pageFromHref(href) {
    if (!href || href.indexOf("://") !== -1 || href.charAt(0) === "#") return null;
    var path = href.split("?")[0].split("#")[0];
    var parts = path.split("/");
    var file = parts[parts.length - 1];
    return file.indexOf(".html") !== -1 ? file : null;
  }

  function friendlyLabel(page) {
    if (PAGE_LABELS[page]) return PAGE_LABELS[page];
    return page.replace(/\.html$/i, "").replace(/[-_]/g, " ").replace(/\b\w/g, function (c) {
      return c.toUpperCase();
    });
  }

  function shouldReplaceLabel(text, page) {
    if (!text) return true;
    if (/\.html$/i.test(text)) return true;
    if (text === page) return true;
    return false;
  }

  function applyNavLabels() {
    var links = document.querySelectorAll("a[href]");
    for (var i = 0; i < links.length; i++) {
      var link = links[i];
      var page = pageFromHref(link.getAttribute("href"));
      if (!page) continue;
      var label = friendlyLabel(page);
      var text = (link.textContent || "").trim();
      if (shouldReplaceLabel(text, page)) {
        link.textContent = label;
      }
    }

    var page = pageFromHref(window.location.pathname.split("/").pop() || "index.html");
    if (page && PAGE_LABELS[page] && document.title.indexOf("Planet Blue") !== -1) {
      document.title = "Planet Blue — " + PAGE_LABELS[page];
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", applyNavLabels);
  } else {
    applyNavLabels();
  }
})();
