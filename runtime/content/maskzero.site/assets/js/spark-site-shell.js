(function () {
  "use strict";

  document.documentElement.classList.add("spark-site-shell-ready");

  document.addEventListener("submit", function (event) {
    var form = event.target;
    if (!form || !form.matches || !form.matches("form")) {
      return;
    }
    var button = form.querySelector('button[type="submit"]');
    if (button && !button.disabled) {
      button.setAttribute("aria-busy", "true");
    }
  });
})();
