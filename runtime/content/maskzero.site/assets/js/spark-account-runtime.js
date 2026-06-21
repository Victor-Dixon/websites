(function () {
  "use strict";

  var SESSION_ENDPOINTS = [
    "/api/spark-auth.php?action=session",
    "/wp-json/spark/v1/session",
    "/wp-json/emergence/v1/session"
  ];

  function hasSparkAuthCookie() {
    return document.cookie.indexOf("maskzero_spark_session") !== -1;
  }

  function hasWordPressCookie() {
    return document.cookie.indexOf("wordpress_logged_in") !== -1;
  }

  function hasAuthCookie() {
    return hasSparkAuthCookie() || hasWordPressCookie();
  }

  function normalizeSession(payload) {
    payload = payload || {};
    var loggedIn =
      payload.logged_in === true ||
      payload.loggedIn === true ||
      payload.authenticated === true ||
      !!payload.user;

    return {
      ok: true,
      data: {
        logged_in: loggedIn,
        user: payload.user || payload.account || null,
        raw: payload
      }
    };
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
          logged_in: hasAuthCookie(),
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
    hasSparkAuthCookie: hasSparkAuthCookie,
    hasAuthCookie: hasAuthCookie,
    announce: announce
  };
})();
