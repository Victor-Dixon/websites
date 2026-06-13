(function () {
  "use strict";

  function byId(id) { return document.getElementById(id); }

  var briefForm = byId("brief-gate-form");
  if (briefForm) {
    briefForm.addEventListener("submit", function (event) {
      event.preventDefault();
      var email = briefForm.querySelector("input[type=email]");
      var audience = briefForm.querySelector("select[name=audience]");
      var result = byId("brief-gate-result");
      if (!email || !email.value || email.value.indexOf("@") === -1) {
        result.textContent = "Enter a valid email to unlock the technical overview download.";
        result.hidden = false;
        return;
      }
      try {
        localStorage.setItem("maskzero.briefLead.v1", JSON.stringify({
          email: email.value,
          audience: audience ? audience.value : "",
          capturedAt: new Date().toISOString()
        }));
      } catch (err) {}
      result.innerHTML = 'Thank you. Download the <a href="/assets/docs/maskzero-technical-overview.pdf">MaskZero Technical Overview PDF</a>, then <a href="/contact/">contact MaskZero</a> for diligence materials or NDA review.';
      result.hidden = false;
    });
  }
})();
