(function(){
  "use strict";

  const LOCAL_HERO_KEY = "spark.localHeroDossier.v1";
  const PENDING_SAVE_KEY = "spark.pendingSave.v1";
  const DEFAULT_POST_LOGIN = "/spark-dashboard/";
  // Same keys as meridian-dispatch plus generator save paths (account hub summary).
  const SPARK_IDENTITY_KEYS = [
    LOCAL_HERO_KEY,
    "spark.localHero.v1",
    "spark.finalDossier.v1",
    "spark.lastSavedHero.v1",
    "dreamos.savedSparkCharacters.v1",
    "spark.savedCharacter.v1",
    "spark.character.v1"
  ];

  function jsonFetch(url, options){
    options = options || {};
    options.credentials = "same-origin";
    options.cache = options.cache || "no-store";
    options.headers = Object.assign({"Accept":"application/json"}, options.headers || {});

    return fetch(url, options).then(async function(res){
      let data = {};
      try { data = await res.json(); } catch(e) {}
      return {ok:res.ok, status:res.status, data:data};
    }).catch(function(err){
      return {ok:false, status:"FETCH_ERROR", data:{message:String(err && err.message || err)}};
    });
  }

  function nonceHeaders(){
    const h = {"Accept":"application/json", "Content-Type":"application/json"};
    if (window.SPARK_ACCOUNT && window.SPARK_ACCOUNT.restNonce) {
      h["X-WP-Nonce"] = window.SPARK_ACCOUNT.restNonce;
    }
    return h;
  }

  let sessionPromise = null;

  function dispatchSessionEvent(result){
    const loggedIn = !!(result.ok && result.data && result.data.logged_in);
    document.dispatchEvent(new CustomEvent("spark:session", {
      detail: { result, loggedIn }
    }));
    if (window.SparkAuthNav && typeof window.SparkAuthNav.refresh === "function") {
      window.SparkAuthNav.refresh(loggedIn);
    }
    return result;
  }

  function session(){
    if (sessionPromise) {
      return sessionPromise;
    }
    sessionPromise = jsonFetch("/wp-json/spark/v1/session").then(function(result){
      window.SPARK_ACCOUNT = Object.assign({}, window.SPARK_ACCOUNT || {}, result.data || {});
      return dispatchSessionEvent(result);
    }).catch(function(err){
      sessionPromise = null;
      const failure = {ok:false, status:"FETCH_ERROR", data:{message:String(err && err.message || err)}};
      dispatchSessionEvent(failure);
      return failure;
    });
    return sessionPromise;
  }

  async function hero(){
    await session();
    return jsonFetch("/wp-json/spark/v1/hero", {headers: nonceHeaders()});
  }

  async function save(payload){
    await session();
    return jsonFetch("/wp-json/spark/v1/save", {
      method:"POST",
      headers: nonceHeaders(),
      body: JSON.stringify(payload || {})
    });
  }

  function isLoggedIn(){
    return !!(window.SPARK_ACCOUNT && window.SPARK_ACCOUNT.logged_in);
  }

  function readLocalHero(){
    try {
      const raw = localStorage.getItem(LOCAL_HERO_KEY);
      if (!raw) return null;
      const parsed = JSON.parse(raw);
      if (!parsed || typeof parsed !== "object") return null;
      parsed.local_only = true;
      return parsed;
    } catch(e) {
      return null;
    }
  }

  function writeLocalHero(hero){
    try {
      localStorage.setItem(LOCAL_HERO_KEY, JSON.stringify(hero || {}));
      return true;
    } catch(e) {
      return false;
    }
  }

  function writePendingSave(payload){
    try {
      localStorage.setItem(PENDING_SAVE_KEY, JSON.stringify(payload || {}));
      return true;
    } catch(e) {
      return false;
    }
  }

  function readPendingSave(){
    try {
      const raw = localStorage.getItem(PENDING_SAVE_KEY);
      if (!raw) return null;
      return JSON.parse(raw);
    } catch(e) {
      return null;
    }
  }

  function clearPendingSave(){
    try { localStorage.removeItem(PENDING_SAVE_KEY); } catch(e) {}
  }

  function loginUrl(redirectPath){
    const target = redirectPath || window.location.pathname + window.location.search || DEFAULT_POST_LOGIN;
    return "/spark-login/?redirect_to=" + encodeURIComponent(target);
  }

  function readJSON(key){
    try {
      const raw = localStorage.getItem(key);
      if (!raw) return null;
      return JSON.parse(raw);
    } catch(e) {
      return null;
    }
  }

  function loadSparkIdentity(){
    for (const key of SPARK_IDENTITY_KEYS) {
      const value = readJSON(key);
      if (Array.isArray(value) && value.length) return value[value.length - 1];
      if (value && typeof value === "object" && Object.keys(value).length) return value;
    }
    return null;
  }

  function sparkDisplayName(spark){
    return (spark && (spark.name || spark.codename || spark.spark_name || spark.hero_name)) || "Unnamed Spark";
  }

  function sparkDisplayDomains(spark){
    const raw = (spark && (spark.domains || spark.manifested_domains || spark.domain)) || [];
    if (Array.isArray(raw)) return raw.length ? raw.join(", ") : "Pending";
    return raw || "Pending";
  }

  function escapeHTML(str){
    return String(str).replace(/[&<>"']/g, function(ch){
      return {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[ch];
    });
  }

  function cookieIndicatesLoggedIn(){
    return document.cookie.indexOf("wordpress_logged_in") !== -1;
  }

  function applyAuthVisibility(loggedIn){
    document.documentElement.setAttribute("data-spark-nav", loggedIn ? "in" : "out");
    document.querySelectorAll("[data-spark-auth-only]").forEach(function(node){
      node.hidden = !loggedIn;
    });
    document.querySelectorAll("[data-spark-guest-only]").forEach(function(node){
      node.hidden = !!loggedIn;
    });
    if (window.SparkAuthNav && typeof window.SparkAuthNav.refresh === "function") {
      window.SparkAuthNav.refresh(loggedIn);
    }
  }

  async function bootAccountHub(){
    const lede = document.getElementById("hub-lede");
    const summary = document.getElementById("spark-summary");
    const summaryBody = document.getElementById("spark-summary-body");
    const unlockPanel = document.getElementById("unlock-panel");
    const quizLink = document.getElementById("quiz-link");
    if (!lede) return;

    applyAuthVisibility(cookieIndicatesLoggedIn());

    const sessionResult = await session();
    const loggedIn = !!(sessionResult.ok && sessionResult.data && sessionResult.data.logged_in);
    applyAuthVisibility(loggedIn);
    const localSpark = loadSparkIdentity();
    let serverHero = null;

    if (loggedIn) {
      const heroResult = await hero();
      if (heroResult.ok && heroResult.data && typeof heroResult.data === "object") {
        serverHero = heroResult.data.hero || heroResult.data;
      }
    }

    const spark = serverHero || localSpark;

    document.querySelectorAll("[data-spark-auth-href]").forEach(function(link){
      const target = link.getAttribute("data-spark-auth-href");
      if (!target) return;
      link.href = loggedIn ? target : loginUrl(target);
    });

    if (loggedIn) {
      const label = (sessionResult.data && (sessionResult.data.display_name || sessionResult.data.user_login)) || "Operator";
      lede.textContent = "Signed in as " + label + ". Your command post is at /spark-dashboard/ — Dispatch, Generator, and News are wired.";
      if (quizLink) quizLink.href = "/spark-generator/";
      if (unlockPanel) unlockPanel.hidden = true;
    } else {
      lede.textContent = "No session yet. Create a hero (1 free Spark), take the origin quiz, then answer the Dispatch.";
      if (quizLink) quizLink.href = loginUrl("/spark-generator/");
      if (unlockPanel) unlockPanel.hidden = false;
    }

    if (loggedIn && spark && summary && summaryBody) {
      const source = serverHero ? "Account-locked hero" : (readLocalHero() ? "Local draft (sign in to lock)" : "Browser draft");
      summaryBody.innerHTML =
        "<p><strong>" + escapeHTML(sparkDisplayName(spark)) + "</strong></p>" +
        "<p>Domains: " + escapeHTML(sparkDisplayDomains(spark)) + "</p>" +
        "<p class=\"spark-source\">" + escapeHTML(source) + "</p>";
    } else if (summary && summaryBody) {
      summaryBody.innerHTML = "";
    }
  }

  async function debug(){
    const s = await session();
    const h = await hero();
    const out = {
      session_status: s.status,
      logged_in: !!(s.data && s.data.logged_in),
      user_id: (s.data && s.data.user_id) || 0,
      has_restNonce: !!(s.data && s.data.restNonce),
      cookie_has_wordpress_logged_in: document.cookie.indexOf("wordpress_logged_in") !== -1,
      cookie_has_wordpress_sec: document.cookie.indexOf("wordpress_sec") !== -1,
      hero_status: h.status,
      hero_ok: h.ok,
      hero_code: h.data && h.data.code
    };
    return out;
  }

  window.SparkAccountRuntime = {
    session,
    hero,
    save,
    isLoggedIn,
    applyAuthVisibility,
    readLocalHero,
    writeLocalHero,
    writePendingSave,
    readPendingSave,
    clearPendingSave,
    loginUrl,
    loadSparkIdentity,
    sparkDisplayName,
    sparkDisplayDomains,
    bootAccountHub,
    debug,
    defaultPostLogin: DEFAULT_POST_LOGIN,
    keys: {
      localHero: LOCAL_HERO_KEY,
      pendingSave: PENDING_SAVE_KEY,
      identity: SPARK_IDENTITY_KEYS
    }
  };
})();
