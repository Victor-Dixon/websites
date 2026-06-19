(function () {
  var KEY = "maskzero_device_session_v1";

  function remember() {
    try {
      localStorage.setItem(KEY, JSON.stringify({
        loggedIn: true,
        savedAt: new Date().toISOString()
      }));
    } catch (_) {}
  }

  function isRemembered() {
    try {
      var raw = localStorage.getItem(KEY);
      if (!raw) return false;
      var data = JSON.parse(raw);
      return data && data.loggedIn === true;
    } catch (_) {
      return false;
    }
  }

  function boot() {
    document.documentElement.classList.add("mz-theme-ready");

    if (isRemembered()) {
      document.body.setAttribute("data-maskzero-remembered", "true");
      var msg = document.querySelector("[data-maskzero-login-status]");
      if (msg) msg.textContent = "Device login remembered.";
    }

    document.querySelectorAll("form").forEach(function (form) {
      form.addEventListener("submit", function () {
        var rememberBox = document.querySelector("#maskzero-remember-device");
        if (!rememberBox || rememberBox.checked) remember();
      });
    });

    document.querySelectorAll("[data-maskzero-login-button], button, input[type='submit']").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var rememberBox = document.querySelector("#maskzero-remember-device");
        if (!rememberBox || rememberBox.checked) remember();
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
