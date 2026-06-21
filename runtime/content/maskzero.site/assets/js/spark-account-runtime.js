(function () {
  "use strict";

  var SESSION_ENDPOINTS = [
    "/api/spark-auth.php?action=session",
    "/wp-json/spark/v1/session",
    "/wp-json/emergence/v1/session"
  ];

  function hasWordPressCookie() {
    return document.cookie.indexOf("wordpress_logged_in") !== -1;
  }

  function normalizeSession(payload) {
    payload = payload || {};
    var loggedIn =
      payload.logged_in === true ||
      payload.loggedIn === true ||
      payload.authenticated === true ||
      !!payload.user;

    var user = payload.user || payload.account || null;
    var data = {
      logged_in: loggedIn,
      user: user,
      raw: payload
    };

    if (payload.game_role) data.game_role = payload.game_role;
    if (payload.is_owner === true) data.is_owner = true;
    if (payload.can_access_admin_panel === true) data.can_access_admin_panel = true;
    if (user && user.game_role) data.game_role = user.game_role;
    if (user && user.is_owner) data.is_owner = true;
    if (user && user.can_access_admin_panel) data.can_access_admin_panel = true;
    if (payload.email) data.email = payload.email;
    if (payload.display_name) data.display_name = payload.display_name;
    if (user && user.email) data.email = user.email;
    if (user && user.display_name) data.display_name = user.display_name;

    window.SPARK_ACCOUNT = Object.assign({}, window.SPARK_ACCOUNT || {}, data);

    return { ok: true, data: data };
  }

  function fetchJson(url) {
    return fetch(url, {
      credentials: "same-origin",
      headers: { "Accept": "application/json" }
    }).then(function (res) {
      if (!res.ok) {
        throw new Error("HTTP " + res.status);
      }
      return res.json();
    });
  }

  function session() {
    var chain = SESSION_ENDPOINTS.reduce(function (promise, endpoint) {
      return promise.catch(function () {
        return fetchJson(endpoint).then(normalizeSession);
      });
    }, Promise.reject(new Error("No endpoint tried")));

    return chain.catch(function () {
      return {
        ok: true,
        data: {
          logged_in: hasWordPressCookie(),
          user: null,
          raw: null
        }
      };
    });
  }

  function announce(loggedIn, user) {
    var event;
    try {
      event = new CustomEvent("spark:session", {
        detail: { loggedIn: !!loggedIn, user: user || null }
      });
    } catch (err) {
      event = document.createEvent("CustomEvent");
      event.initCustomEvent("spark:session", true, true, {
        loggedIn: !!loggedIn,
        user: user || null
      });
    }
    document.dispatchEvent(event);
  }

  window.SparkAccountRuntime = {
    session: session,
    hasWordPressCookie: hasWordPressCookie,
    announce: announce
  };
})();
