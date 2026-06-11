(function () {
  "use strict";

  var LINKS = [
    { href: "/", label: "Hub" },
    { href: "/focus/", label: "Focus" },
    { href: "/tasks/", label: "Tasks" },
    { href: "/projects/", label: "Projects" },
    { href: "/feed/", label: "Feed" },
    { href: "/skill-tree/", label: "Skill Tree" },
    { href: "/roadmap/", label: "Roadmap" },
  ];

  function normalizePath(pathname) {
    var path = pathname || "/";
    path = path.replace(/\/index\.html$/i, "/");
    if (path !== "/" && !path.endsWith("/")) path += "/";
    return path;
  }

  function mountSiteNav() {
    var nav = document.querySelector(".site-nav");
    if (!nav) return;

    var active = normalizePath(window.location.pathname);
    nav.innerHTML = "";

    var brand = document.createElement("a");
    brand.className = "brand";
    brand.href = "/";
    brand.textContent = "🧠 WeAreSwarm";
    nav.appendChild(brand);

    var links = document.createElement("div");
    links.className = "links";
    LINKS.forEach(function (item) {
      var a = document.createElement("a");
      a.href = item.href;
      a.textContent = item.label;
      if (normalizePath(item.href) === active) a.className = "active";
      links.appendChild(a);
    });
    nav.appendChild(links);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountSiteNav);
  } else {
    mountSiteNav();
  }

  window.SiteNav = { mountSiteNav: mountSiteNav, LINKS: LINKS };
})();
