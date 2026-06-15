(function () {
  "use strict";

  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  ready(function () {
    var toggles = Array.prototype.slice.call(document.querySelectorAll(
      '[data-nav-toggle], .nav-toggle, .menu-toggle, .hamburger, button[aria-controls], button[aria-expanded]'
    ));

    var navs = Array.prototype.slice.call(document.querySelectorAll(
      '[data-nav-menu], .nav-menu, .site-nav, nav, header nav'
    ));

    if (!toggles.length || !navs.length) {
      return;
    }

    function getTarget(toggle) {
      var id = toggle.getAttribute("aria-controls") || toggle.getAttribute("data-target");
      if (id) {
        var byId = document.getElementById(id.replace(/^#/, ""));
        if (byId) return byId;
        var bySelector = document.querySelector(id);
        if (bySelector) return bySelector;
      }

      var header = toggle.closest("header");
      if (header) {
        var localNav = header.querySelector("[data-nav-menu], .nav-menu, .site-nav, nav");
        if (localNav) return localNav;
      }

      return navs[0];
    }

    toggles.forEach(function (toggle) {
      if (toggle.dataset.dreamosNavBound === "1") return;
      toggle.dataset.dreamosNavBound = "1";

      toggle.addEventListener("click", function (event) {
        event.preventDefault();

        var menu = getTarget(toggle);
        if (!menu) return;

        var expanded = toggle.getAttribute("aria-expanded") === "true";
        var next = !expanded;

        toggle.setAttribute("aria-expanded", String(next));
        toggle.classList.toggle("is-open", next);
        menu.classList.toggle("is-open", next);
        menu.classList.toggle("open", next);
        menu.hidden = false;

        if (next) {
          document.documentElement.classList.add("nav-open");
          document.body.classList.add("nav-open");
        } else {
          document.documentElement.classList.remove("nav-open");
          document.body.classList.remove("nav-open");
        }
      });
    });

    document.addEventListener("keydown", function (event) {
      if (event.key !== "Escape") return;

      toggles.forEach(function (toggle) {
        toggle.setAttribute("aria-expanded", "false");
        toggle.classList.remove("is-open");
      });

      navs.forEach(function (nav) {
        nav.classList.remove("is-open", "open");
      });

      document.documentElement.classList.remove("nav-open");
      document.body.classList.remove("nav-open");
    });
  });
})();
