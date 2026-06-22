(function (root) {
  'use strict';

  var SESSION_URL = '/api/spark-auth.php?action=session';
  var NARRATION_URL = '/api/spark-narration.php';

  function fetchSession() {
    return fetch(SESSION_URL, { credentials: 'include', cache: 'no-store' })
      .then(function (r) { return r.json(); })
      .catch(function () { return { ok: false, logged_in: false }; });
  }

  function canUseServer(session) {
    if (!session || !session.logged_in) return false;
    if (session.can_render_video || session.skymotion_access) return true;
    if (session.user && (session.user.can_render_video || session.user.skymotion_access)) return true;
    if (session.is_staff || (session.user && session.user.is_staff)) return true;
    return false;
  }

  function chat(messages, opts) {
    opts = opts || {};
    return fetch(NARRATION_URL, {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        mode: opts.mode || 'dm',
        messages: messages,
        max_tokens: opts.max_tokens || 450,
        temperature: opts.temperature != null ? opts.temperature : 0.88
      })
    }).then(function (r) {
      return r.json().then(function (payload) {
        if (!r.ok || !payload.ok) {
          var err = new Error(payload.message || payload.error || 'Narration failed');
          err.status = r.status;
          err.payload = payload;
          throw err;
        }
        return payload;
      });
    });
  }

  root.SparkNarration = {
    fetchSession: fetchSession,
    canUseServer: canUseServer,
    chat: chat
  };
})(typeof window !== 'undefined' ? window : globalThis);
