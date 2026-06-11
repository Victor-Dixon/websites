(function (global) {
  "use strict";

  var USERS_URL = "/data/kids-planner/users.json";
  var SESSION_KEY = "kidsPlanner.session";
  var REMEMBER_KEY = "kidsPlanner.rememberToken";
  var SESSION_HOURS = 8;
  var REMEMBER_DAYS = 30;

  var cachedUsers = null;

  function sha256Hex(text) {
    if (!global.crypto || !global.crypto.subtle) {
      return Promise.reject(new Error("Secure context required for login"));
    }
    var enc = new TextEncoder().encode(text);
    return global.crypto.subtle.digest("SHA-256", enc).then(function (buf) {
      return Array.from(new Uint8Array(buf))
        .map(function (b) {
          return b.toString(16).padStart(2, "0");
        })
        .join("");
    });
  }

  function loadUsers() {
    if (cachedUsers) return Promise.resolve(cachedUsers);
    return fetch(USERS_URL)
      .then(function (r) {
        if (!r.ok) throw new Error("Could not load user list");
        return r.json();
      })
      .then(function (data) {
        cachedUsers = (data && data.users) || [];
        return cachedUsers;
      });
  }

  function saveSession(session, remember) {
    sessionStorage.setItem(SESSION_KEY, JSON.stringify(session));
    if (remember) {
      localStorage.setItem(REMEMBER_KEY, JSON.stringify({
        userId: session.userId,
        role: session.role,
        displayName: session.displayName,
        exp: session.exp,
        sig: session.sig,
      }));
    } else {
      localStorage.removeItem(REMEMBER_KEY);
    }
  }

  function parseSession(raw) {
    try {
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function isExpired(exp) {
    return !exp || Date.now() > exp;
  }

  function buildSession(user, pinHash, remember) {
    var hours = remember ? REMEMBER_DAYS * 24 : SESSION_HOURS;
    var exp = Date.now() + hours * 3600000;
    return sha256Hex(user.id + ":" + exp + ":" + pinHash).then(function (sig) {
      return {
        userId: user.id,
        displayName: user.display_name,
        role: user.role,
        exp: exp,
        sig: sig,
      };
    });
  }

  function verifyRememberToken(token, users) {
    if (!token || isExpired(token.exp)) return null;
    var user = users.find(function (u) {
      return u.id === token.userId;
    });
    if (!user) return null;
    return sha256Hex(user.id + ":" + token.exp + ":" + user.pin_hash).then(function (sig) {
      if (sig !== token.sig) return null;
      return {
        userId: user.id,
        displayName: user.display_name,
        role: user.role,
        exp: token.exp,
        sig: token.sig,
      };
    });
  }

  function getSession() {
    var raw = sessionStorage.getItem(SESSION_KEY);
    var session = parseSession(raw);
    if (session && !isExpired(session.exp)) return Promise.resolve(session);

    var rememberRaw = localStorage.getItem(REMEMBER_KEY);
    var remember = parseSession(rememberRaw);
    if (!remember) return Promise.resolve(null);

    return loadUsers().then(function (users) {
      return verifyRememberToken(remember, users);
    }).then(function (restored) {
      if (restored) {
        sessionStorage.setItem(SESSION_KEY, JSON.stringify(restored));
        return restored;
      }
      localStorage.removeItem(REMEMBER_KEY);
      return null;
    });
  }

  function requireAuth(loginPath) {
    loginPath = loginPath || "/kids-planner/login.html";
    return getSession().then(function (session) {
      if (session) return session;
      var next = global.location.pathname + global.location.search;
      global.location.href = loginPath + "?next=" + encodeURIComponent(next);
      return null;
    });
  }

  function login(userId, pin, remember) {
    return loadUsers().then(function (users) {
      var user = users.find(function (u) {
        return u.id === userId;
      });
      if (!user || !user.salt || !user.pin_hash) {
        throw new Error("Unknown user");
      }
      return sha256Hex(user.salt + pin).then(function (hash) {
        if (hash !== user.pin_hash) throw new Error("Wrong PIN");
        return buildSession(user, user.pin_hash, remember);
      }).then(function (session) {
        saveSession(session, remember);
        return session;
      });
    });
  }

  function logout() {
    sessionStorage.removeItem(SESSION_KEY);
    localStorage.removeItem(REMEMBER_KEY);
    global.location.href = "/kids-planner/login.html";
  }

  function isParent(session) {
    return !!(session && session.role === "parent");
  }

  function isKid(session) {
    return !!(session && session.role === "kid");
  }

  global.KidsAuth = {
    USERS_URL: USERS_URL,
    loadUsers: loadUsers,
    getSession: getSession,
    requireAuth: requireAuth,
    login: login,
    logout: logout,
    isParent: isParent,
    isKid: isKid,
  };
})(window);
