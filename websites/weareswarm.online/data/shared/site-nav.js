(function () {
  "use strict";

  var LINKS = [
    { href: "/", label: "Hub" },
    { href: "/focus/", label: "Focus" },
    { href: "/kids-planner/", label: "Kids Planner" },
    { href: "/tasks/", label: "Tasks" },
    { href: "/projects/", label: "Projects" },
    { href: "/investor-dashboard/", label: "Investor" },
    { href: "/feed/", label: "Feed" },
    { href: "/skill-tree/", label: "Skill Tree" },
    { href: "/level5/", label: "Level 5" },
    { href: "/roadmap/", label: "Ops Roadmap" },
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
    nav.classList.remove("is-open");

    var head = document.createElement("div");
    head.className = "site-nav__head";

    var brand = document.createElement("a");
    brand.className = "brand";
    brand.href = "/";
    brand.textContent = "🧠 WeAreSwarm";
    head.appendChild(brand);

    var toggle = document.createElement("button");
    toggle.className = "site-nav__toggle";
    toggle.type = "button";
    toggle.setAttribute("aria-label", "Toggle navigation");
    toggle.setAttribute("aria-expanded", "false");
    toggle.innerHTML = "&#9776;";
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    head.appendChild(toggle);

    nav.appendChild(head);

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
