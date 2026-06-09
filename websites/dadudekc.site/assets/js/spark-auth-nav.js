(function () {
  "use strict";

  var NAV_ASSET_VERSION = "7";
  var navEl = null;
  var navIsComic = false;

  var GUEST_LINKS = [
    { href: "/", label: "Home", comicLabel: "Cover", key: "home" },
    {
      href: "/spark-account/",
      label: "Build Account",
      comicLabel: "Origin Rules",
      key: "account",
      accent: true
    }
  ];

  var LOGGED_IN_HOME = {
    href: "/spark-dashboard/",
    label: "Dashboard",
    comicLabel: "Command Post",
    key: "dashboard",
    accent: true
  };

  var AUTH_LINKS = [
    { href: "/news/", label: "Meridian News", key: "news" },
    { href: "/meridian-dispatch/", label: "Dispatch", key: "dispatch" },
    { href: "/meridian-map/", label: "Map", key: "map" },
    { href: "/spark-gauntlet/", label: "The Gauntlet", comicLabel: "Trials", key: "gauntlet" },
    { href: "/spark-battle/", label: "Battle Sim", key: "battle" }
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
    if (!loggedIn) {
      return GUEST_LINKS.slice();
    }
    return [
      LOGGED_IN_HOME,
      {
        href: "/spark-account/",
        label: "Account",
        comicLabel: "Origin Rules",
        key: "account"
      }
    ].concat(AUTH_LINKS);
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
        '<a href="/spark-generator/" style="color:#f5f7fb;text-decoration:none;font-weight:800">Generator</a>' +
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
    var html = "";

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
      html +=
        '<a href="/spark-generator/">Generator</a>' +
        '<a href="/spark-logout/">Log Out</a>';
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

  function refresh(loggedIn) {
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
