<<<<<<< origin/master:runtime/content/dadudekc.site/assets/js/spark-auth-nav.js
(function () {
  "use strict";

  var NAV_ASSET_VERSION = "9";
  var navEl = null;
  var navIsComic = false;
  var menuToggleEl = null;

  var GUEST_LINKS = [
    { href: "/", label: "Home", comicLabel: "Cover", key: "home" },
    {
      href: "/spark-generator/",
      label: "Origin Lab",
      comicLabel: "Origin Lab",
      key: "generator"
    }
  ];

  var LOGGED_IN_HOME = {
    href: "/spark-dashboard/",
    label: "Dashboard",
    comicLabel: "Command Post",
    key: "dashboard",
    accent: true
  };

  var LOGGED_IN_LINKS = [
    LOGGED_IN_HOME,
    {
      href: "/spark-generator/",
      label: "Origin Lab",
      comicLabel: "Origin Lab",
      key: "generator"
    },
    {
      href: "/spark-account/",
      label: "Account",
      comicLabel: "Origin Rules",
      key: "account"
    }
  ];

  function injectNavGuardCSS() {
    if (document.getElementById("spark-nav-guard-css")) {
      return;
    }
    var style = document.createElement("style");
    style.id = "spark-nav-guard-css";
    style.textContent =
      'html:not([data-spark-nav="in"]) [data-spark-auth-only]{display:none!important}' +
      'html[data-spark-nav="in"] [data-spark-guest-only]{display:none!important}' +
      '.spark-menu-toggle,.spark-menu-close{display:none}' +
      '@media(max-width:760px){' +
        '.spark-menu-toggle{position:sticky;top:0;z-index:2147482999;display:flex;align-items:center;justify-content:center;width:calc(100% - 24px);min-height:56px;margin:12px auto;padding:10px 14px;border:5px solid #09080b;background:#ffd12f;color:#09080b;box-shadow:6px 6px 0 #09080b;font:950 18px/1 Impact,"Arial Black",system-ui,sans-serif;text-transform:uppercase;letter-spacing:.08em;cursor:pointer}' +
        '#spark-auth-nav.comic-nav{display:none!important}' +
        'html[data-spark-mobile-menu="open"] #spark-auth-nav.comic-nav{position:fixed!important;top:0!important;left:0!important;right:auto!important;bottom:auto!important;width:100vw!important;height:100vh!important;z-index:2147483000!important;display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:18px!important;flex-wrap:nowrap!important;margin:0!important;padding:calc(28px + env(safe-area-inset-top,0px)) 18px calc(28px + env(safe-area-inset-bottom,0px))!important;border:0!important;background:#09080b!important;box-shadow:none!important;overflow-y:auto!important;-webkit-overflow-scrolling:touch!important}' +
        'html[data-spark-mobile-menu="open"] #spark-auth-nav.comic-nav a,html[data-spark-mobile-menu="open"] #spark-auth-nav.comic-nav button{width:min(340px,calc(100vw - 48px))!important;min-height:56px!important;display:flex!important;align-items:center!important;justify-content:center!important;text-align:center!important;border:4px solid #fff!important;background:#fff7d6!important;color:#09080b!important;box-shadow:6px 6px 0 #ffd12f!important;font:950 19px/1.05 Impact,"Arial Black",system-ui,sans-serif!important;text-transform:uppercase!important;letter-spacing:.04em!important;text-decoration:none!important}' +
        'html[data-spark-mobile-menu="open"] #spark-auth-nav.comic-nav .pop{background:#ff3155!important;color:#fff!important}' +
        'html[data-spark-mobile-menu="open"] #spark-auth-nav.comic-nav .spark-menu-close{display:flex!important;background:#ffd12f!important;color:#09080b!important;cursor:pointer!important}' +
        'html[data-spark-mobile-menu="open"],body.spark-menu-open{overflow:hidden!important}' +
        'body.spark-menu-open{position:fixed!important;width:100%!important;touch-action:none!important}' +
      '}' +
      '@media(min-width:761px){.spark-menu-toggle,.spark-menu-close{display:none!important}}';
    (document.head || document.documentElement).appendChild(style);
  }

  function setNavState(loggedIn) {
    document.documentElement.setAttribute(
      "data-spark-nav",
      loggedIn ? "in" : "out"
    );
  }

  function currentKey() {
    var path = window.location.pathname.replace(/\/+$/, "") || "/";
    if (path === "/" || path === "") return "home";
    if (path.indexOf("/news") === 0) return "news";
    if (path.indexOf("/meridian-dispatch") === 0) return "dispatch";
    if (path.indexOf("/meridian-map") === 0) return "map";
    if (path.indexOf("/spark-dashboard") === 0) return "dashboard";
    if (path.indexOf("/spark-account") === 0) return "account";
    if (path.indexOf("/spark-login") === 0) return "login";
    if (path.indexOf("/spark-signup") === 0) return "signup";
    if (path.indexOf("/spark-logout") === 0) return "logout";
    if (path.indexOf("/spark-generator") === 0) return "generator";
    if (path.indexOf("/spark-gauntlet") === 0) return "gauntlet";
    if (path.indexOf("/spark-battle") === 0) return "battle";
    return "";
  }

  function linksForSession(loggedIn) {
    return loggedIn ? LOGGED_IN_LINKS.slice() : GUEST_LINKS.slice();
  }

  function linkLabel(link, isComic) {
    return isComic && link.comicLabel ? link.comicLabel : link.label;
  }

  function linkStyle(link, active) {
    if (active) {
      return "color:#ffd12f;text-decoration:none;font-weight:900";
    }
    if (link.accent) {
      return "color:#78f0ff;text-decoration:none;font-weight:900";
    }
    return "color:#f5f7fb;text-decoration:none;font-weight:800";
  }

  function renderSparkNav(nav, loggedIn) {
    var active = currentKey();
    var html = '<button type="button" class="spark-menu-close" data-spark-menu-close>Close Menu</button>';

    linksForSession(loggedIn).forEach(function (link) {
      var style = linkStyle(link, active === link.key);
      html +=
        '<a href="' +
        link.href +
        '" style="' +
        style +
        '">' +
        linkLabel(link, false) +
        "</a>";
    });

    if (loggedIn) {
      html +=
        '<a href="/spark-logout/" style="padding:8px 10px;border-radius:10px;background:#ff3155;color:#fff;text-decoration:none;font-weight:900">Log Out</a>';
    } else {
      html +=
        '<a href="/spark-login/" style="color:#f5f7fb;text-decoration:none;font-weight:800">Log In</a>' +
        '<a href="/spark-signup/" style="padding:8px 10px;border-radius:10px;background:#78f0ff;color:#061019;text-decoration:none;font-weight:900">Sign Up</a>';
    }

    nav.innerHTML = html;
  }

  function renderComicNav(nav, loggedIn) {
    var active = currentKey();
    var html = '<button type="button" class="spark-menu-close" data-spark-menu-close>Close Menu</button>';

    linksForSession(loggedIn).forEach(function (link) {
      var cls = active === link.key ? ' class="pop"' : "";
      html +=
        '<a href="' +
        link.href +
        '"' +
        cls +
        ">" +
        linkLabel(link, true) +
        "</a>";
    });

    if (loggedIn) {
      html += '<a href="/spark-logout/">Log Out</a>';
    } else {
      html +=
        '<a href="/spark-login/">Log In</a>' +
        '<a class="pop" href="/spark-signup/">Join The Universe</a>';
    }

    nav.innerHTML = html;
  }

  function isRootHref(href) {
    return href === "/" || href === "/index.html" || href === "";
  }

  function patchRootLinks(loggedIn) {
    var selector =
      '#spark-auth-nav a[href="/"], #spark-auth-nav a[href="/index.html"],' +
      '.comic-nav a[href="/"], .comic-nav a[href="/index.html"],' +
      '.spark-nav a[href="/"], .spark-nav a[href="/index.html"],' +
      'a[data-spark-home]';

    document.querySelectorAll(selector).forEach(function (link) {
      if (link.hasAttribute("data-spark-cover")) {
        return;
      }

      if (loggedIn) {
        if (!link.dataset.sparkGuestHref) {
          link.dataset.sparkGuestHref = link.getAttribute("href") || "/";
        }
        link.setAttribute("href", LOGGED_IN_HOME.href);
        var label = (link.textContent || "").trim().toLowerCase();
        if (label === "home" || label === "cover") {
          link.textContent = linkLabel(LOGGED_IN_HOME, navIsComic);
        }
      } else if (link.dataset.sparkGuestHref) {
        link.setAttribute("href", link.dataset.sparkGuestHref);
      }
    });
  }

  function setMobileMenu(open) {
    document.documentElement.setAttribute("data-spark-mobile-menu", open ? "open" : "closed");
    document.body.classList.toggle("spark-menu-open", !!open);
    if (menuToggleEl) {
      menuToggleEl.setAttribute("aria-expanded", open ? "true" : "false");
    }
    if (navEl) {
      navEl.setAttribute("aria-hidden", open ? "false" : "true");
    }
  }

  function ensureMobileMenuControls() {
    if (!navEl || menuToggleEl) {
      return;
    }

    menuToggleEl = document.createElement("button");
    menuToggleEl.type = "button";
    menuToggleEl.className = "spark-menu-toggle";
    menuToggleEl.setAttribute("aria-controls", navEl.id || "spark-auth-nav");
    menuToggleEl.setAttribute("aria-expanded", "false");
    menuToggleEl.textContent = "Menu";
    navEl.parentNode.insertBefore(menuToggleEl, navEl);
    navEl.setAttribute("data-spark-mobile-panel", "1");
    setMobileMenu(false);
  }

  function apply(loggedIn) {
    setNavState(!!loggedIn);
    patchRootLinks(!!loggedIn);
    if (!navEl) {
      return;
    }
    if (navIsComic) {
      renderComicNav(navEl, loggedIn);
    } else {
      renderSparkNav(navEl, loggedIn);
    }
    ensureMobileMenuControls();
  }

  function cookieIndicatesLoggedIn() {
    return document.cookie.indexOf("wordpress_logged_in") !== -1;
  }

  function resolveLoggedIn(explicit) {
    if (typeof explicit === "boolean") {
      return explicit;
    }
    if (window.SPARK_ACCOUNT && typeof window.SPARK_ACCOUNT.logged_in === "boolean") {
      return window.SPARK_ACCOUNT.logged_in;
    }
    return cookieIndicatesLoggedIn();
  }

  function refresh(loggedIn) {
    apply(resolveLoggedIn(loggedIn));
  }

  function bindMobileMenu() {
    document.addEventListener("click", function (event) {
      var toggle = event.target.closest && event.target.closest(".spark-menu-toggle");
      if (toggle) {
        event.preventDefault();
        setMobileMenu(document.documentElement.getAttribute("data-spark-mobile-menu") !== "open");
        return;
      }

      var close = event.target.closest && event.target.closest("[data-spark-menu-close]");
      if (close) {
        event.preventDefault();
        setMobileMenu(false);
        return;
      }

      var navLink = event.target.closest && event.target.closest("#spark-auth-nav a");
      if (navLink) {
        setMobileMenu(false);
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        setMobileMenu(false);
      }
    });
  }

  function boot() {
    injectNavGuardCSS();

    navEl =
      document.getElementById("spark-auth-nav") ||
      document.querySelector(".spark-nav") ||
      document.querySelector(".comic-nav");

    navIsComic = !!(navEl && navEl.classList.contains("comic-nav"));
    bindMobileMenu();

    var cachedLoggedIn = resolveLoggedIn();
    apply(cachedLoggedIn);

    var runtime = window.SparkAccountRuntime;
    if (!runtime || typeof runtime.session !== "function") {
      return;
    }

    runtime
      .session()
      .then(function (result) {
        var loggedIn = !!(result.ok && result.data && result.data.logged_in);
        if (!loggedIn && cookieIndicatesLoggedIn()) {
          loggedIn = true;
        }
        apply(loggedIn);
      })
      .catch(function () {
        apply(cookieIndicatesLoggedIn());
      });
  }

  window.SparkAuthNav = {
    refresh: refresh,
    version: NAV_ASSET_VERSION
  };
  window.SPARK_NAV_ASSET_VERSION = NAV_ASSET_VERSION;

  document.addEventListener("spark:session", function (event) {
    var detail = (event && event.detail) || {};
    if (typeof detail.loggedIn === "boolean") {
      refresh(detail.loggedIn);
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
=======
(function () {
  "use strict";

  var NAV_ASSET_VERSION = "14";
  var navEl = null;
  var navIsComic = false;
  var originLabBlocked = false;

  var GUEST_LINKS = [
    { href: "/", label: "Home", comicLabel: "Home", key: "home" },
    {
      href: "/spark-generator/",
      label: "Create Hero",
      comicLabel: "Create Hero",
      key: "generator"
    },
    {
      href: "/#how-it-works",
      label: "How It Works",
      comicLabel: "How It Works",
      key: "how"
    }
  ];

  var LOGGED_IN_HOME = {
    href: "/spark-dashboard/",
    label: "Dashboard",
    comicLabel: "Command Post",
    key: "dashboard",
    accent: true
  };

  var LOGGED_IN_LINKS = [
    LOGGED_IN_HOME,
    {
      href: "/spark-inbox/",
      label: "Inbox",
      comicLabel: "MaskZero Inbox",
      key: "inbox"
    },
    {
      href: "/spark-generator/",
      label: "Create Hero",
      comicLabel: "Create Hero",
      key: "generator"
    }
  ];

  function injectNavGuardCSS() {
    if (document.getElementById("spark-nav-guard-css")) {
      return;
    }
    var style = document.createElement("style");
    style.id = "spark-nav-guard-css";
    style.textContent =
      'html:not([data-spark-nav="in"]) [data-spark-auth-only]{display:none!important}' +
      'html[data-spark-nav="in"] [data-spark-guest-only]{display:none!important}';
    (document.head || document.documentElement).appendChild(style);
  }

  function setNavState(loggedIn) {
    document.documentElement.setAttribute(
      "data-spark-nav",
      loggedIn ? "in" : "out"
    );
  }

  function currentKey() {
    var path = window.location.pathname.replace(/\/+$/, "") || "/";
    if (path === "/" || path === "") return "home";
    if (path.indexOf("/news") === 0) return "news";
    if (path.indexOf("/meridian-dispatch") === 0) return "dispatch";
    if (path.indexOf("/meridian-map") === 0) return "map";
    if (path.indexOf("/spark-dashboard") === 0) return "dashboard";
    if (path.indexOf("/spark-inbox") === 0) return "inbox";
    if (path.indexOf("/spark-account") === 0) return "account";
    if (path.indexOf("/spark-login") === 0) return "login";
    if (path.indexOf("/spark-signup") === 0) return "signup";
    if (path.indexOf("/spark-logout") === 0) return "logout";
    if (path.indexOf("/spark-generator") === 0) return "generator";
    if (path === "/" && window.location.hash === "#how-it-works") return "how";
    if (path.indexOf("/how-it-works") === 0) return "how";
    if (path.indexOf("/spark-gauntlet") === 0) return "gauntlet";
    if (path.indexOf("/spark-battle") === 0) return "battle";
    if (path.indexOf("/spark-owner") === 0) return "owner";
    return "";
  }

  var OWNER_PANEL_LINK = {
    href: "/spark-owner/",
    label: "Owner Panel",
    comicLabel: "Owner Panel",
    key: "owner",
    accent: true,
    ownerOnly: true,
  };

  var UPGRADE_ROSTER_LINK = {
    href: "/spark-dashboard/#origin-rules",
    label: "Upgrade Roster",
    comicLabel: "Upgrade Roster",
    key: "generator",
    accent: true
  };

  function sessionIsOwner() {
    var account = window.SPARK_ACCOUNT || {};
    return !!account.is_owner;
  }

  function linksForSession(loggedIn) {
    var links = loggedIn ? LOGGED_IN_LINKS.slice() : GUEST_LINKS.slice();
    if (loggedIn && sessionIsOwner()) {
      links.splice(1, 0, OWNER_PANEL_LINK);
    }
    if (loggedIn && originLabBlocked) {
      return links.map(function (link) {
        if (link.key === "generator") {
          return UPGRADE_ROSTER_LINK;
        }
        return link;
      });
    }
    return links;
  }

  function linkLabel(link, isComic) {
    return isComic && link.comicLabel ? link.comicLabel : link.label;
  }

  function linkStyle(link, active) {
    if (active) {
      return "color:#ffd12f;text-decoration:none;font-weight:900";
    }
    if (link.accent) {
      return "color:#78f0ff;text-decoration:none;font-weight:900";
    }
    return "color:#f5f7fb;text-decoration:none;font-weight:800";
  }

  function renderSparkNav(nav, loggedIn) {
    var active = currentKey();
    var html = "";

    linksForSession(loggedIn).forEach(function (link) {
      var isActive = active === link.key;
      var style = linkStyle(link, isActive);
      var aria = isActive ? ' aria-current="page"' : "";
      if (html) {
        html += " ";
      }
      html +=
        '<a href="' +
        link.href +
        '"' +
        aria +
        ' style="' +
        style +
        '">' +
        linkLabel(link, false) +
        "</a>";
    });

    if (loggedIn) {
      html +=
        ' <a href="/spark-logout/" style="padding:8px 10px;border-radius:10px;background:#ff3155;color:#fff;text-decoration:none;font-weight:900">Log Out</a>';
    } else {
      html +=
        ' <a href="/spark-login/" style="color:#f5f7fb;text-decoration:none;font-weight:800">Log In</a>' +
        ' <a href="/spark-signup/" style="padding:8px 10px;border-radius:10px;background:#78f0ff;color:#061019;text-decoration:none;font-weight:900">Create Account</a>';
    }

    nav.innerHTML = html;
  }

  function renderComicNav(nav, loggedIn) {
    var active = currentKey();
    var html = "";

    linksForSession(loggedIn).forEach(function (link) {
      var isActive = active === link.key;
      var cls = isActive ? ' class="pop"' : "";
      var aria = isActive ? ' aria-current="page"' : "";
      if (html) {
        html += " ";
      }
      html +=
        '<a href="' +
        link.href +
        '"' +
        cls +
        aria +
        ">" +
        linkLabel(link, true) +
        "</a>";
    });

    if (loggedIn) {
      html += ' <a href="/spark-logout/">Log Out</a>';
    } else {
      html +=
        ' <a href="/spark-login/">Log In</a>' +
        ' <a class="pop" href="/spark-signup/">Create Account</a>';
    }

    nav.innerHTML = html;
  }

  function isRootHref(href) {
    return href === "/" || href === "/index.html" || href === "";
  }

  function patchRootLinks(loggedIn) {
    var selector =
      '#spark-auth-nav a[href="/"], #spark-auth-nav a[href="/index.html"],' +
      '.comic-nav a[href="/"], .comic-nav a[href="/index.html"],' +
      '.spark-nav a[href="/"], .spark-nav a[href="/index.html"],' +
      'a[data-spark-home]';

    document.querySelectorAll(selector).forEach(function (link) {
      if (link.hasAttribute("data-spark-cover")) {
        return;
      }

      if (loggedIn) {
        if (!link.dataset.sparkGuestHref) {
          link.dataset.sparkGuestHref = link.getAttribute("href") || "/";
        }
        link.setAttribute("href", LOGGED_IN_HOME.href);
        var label = (link.textContent || "").trim().toLowerCase();
        if (label === "home" || label === "cover") {
          link.textContent = linkLabel(LOGGED_IN_HOME, navIsComic);
        }
      } else if (link.dataset.sparkGuestHref) {
        link.setAttribute("href", link.dataset.sparkGuestHref);
      }
    });
  }

  function apply(loggedIn) {
    setNavState(!!loggedIn);
    patchRootLinks(!!loggedIn);
    if (!navEl) {
      return;
    }
    if (navIsComic) {
      renderComicNav(navEl, loggedIn);
    } else {
      renderSparkNav(navEl, loggedIn);
    }
  }

  function cookieIndicatesLoggedIn() {
    return document.cookie.indexOf("wordpress_logged_in") !== -1;
  }

  function resolveLoggedIn(explicit) {
    if (typeof explicit === "boolean") {
      return explicit;
    }
    if (window.SPARK_ACCOUNT && typeof window.SPARK_ACCOUNT.logged_in === "boolean") {
      return window.SPARK_ACCOUNT.logged_in;
    }
    return cookieIndicatesLoggedIn();
  }

  function resolveOriginLabBlocked() {
    var runtime = window.SparkAccountRuntime;
    if (runtime && typeof runtime.canAccessOriginLab === "function") {
      return !runtime.canAccessOriginLab();
    }
    var account = window.SPARK_ACCOUNT || {};
    if (account.logged_in && typeof account.can_access_origin_lab === "boolean") {
      return !account.can_access_origin_lab;
    }
    return false;
  }

  function refreshRoster() {
    originLabBlocked = resolveOriginLabBlocked();
    apply(resolveLoggedIn());
  }

  function refresh(loggedIn) {
    originLabBlocked = resolveOriginLabBlocked();
    apply(resolveLoggedIn(loggedIn));
  }

  function boot() {
    injectNavGuardCSS();

    navEl =
      document.getElementById("spark-auth-nav") ||
      document.querySelector(".spark-nav") ||
      document.querySelector(".comic-nav");

    navIsComic = !!(navEl && navEl.classList.contains("comic-nav"));

    var cachedLoggedIn = resolveLoggedIn();
    apply(cachedLoggedIn);

    var runtime = window.SparkAccountRuntime;
    if (!runtime || typeof runtime.session !== "function") {
      return;
    }

    runtime
      .session()
      .then(function (result) {
        var loggedIn = !!(result.ok && result.data && result.data.logged_in);
        if (!loggedIn && cookieIndicatesLoggedIn()) {
          loggedIn = true;
        }
        originLabBlocked = resolveOriginLabBlocked();
        apply(loggedIn);
        if (loggedIn && typeof runtime.me === "function") {
          return runtime.me().then(function () {
            originLabBlocked = resolveOriginLabBlocked();
            apply(loggedIn);
          });
        }
      })
      .catch(function () {
        apply(cookieIndicatesLoggedIn());
      });
  }

  window.SparkAuthNav = {
    refresh: refresh,
    refreshRoster: refreshRoster,
    version: NAV_ASSET_VERSION
  };
  window.SPARK_NAV_ASSET_VERSION = NAV_ASSET_VERSION;

  document.addEventListener("spark:roster", function () {
    refreshRoster();
  });

  document.addEventListener("spark:session", function (event) {
    var detail = (event && event.detail) || {};
    if (typeof detail.loggedIn === "boolean") {
      refresh(detail.loggedIn);
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
>>>>>>> feat/dadudekc-spark-dashboard:websites/dadudekc.site/assets/js/spark-auth-nav.js
