(function () {
  var header = document.querySelector(".site-header");
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("primary-navigation");

  if (header && toggle && nav) {
    toggle.addEventListener("click", function () {
      var isOpen = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!isOpen));
      header.classList.toggle("nav-open", !isOpen);
    });

    nav.addEventListener("click", function (event) {
      if (event.target && event.target.tagName === "A") {
        toggle.setAttribute("aria-expanded", "false");
        header.classList.remove("nav-open");
      }
    });
  }

  var form = document.querySelector("[data-contact-form]");

  if (!form) {
    return;
  }

  var status = form.querySelector(".form-status");
  var submitButton = form.querySelector("button[type='submit']");
  var defaultLabel = submitButton ? submitButton.getAttribute("data-submit-label") || submitButton.textContent : "";

  function setStatus(message, type) {
    if (!status) {
      return;
    }

    status.classList.remove("is-success", "is-error");
    if (type) {
      status.classList.add("is-" + type);
    }
    status.textContent = message;
  }

  function fieldValue(name) {
    var field = form.elements[name];
    return field ? String(field.value || "").trim() : "";
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    if (!form.checkValidity()) {
      form.reportValidity();
      setStatus("Please complete the required flight details before preparing the brief.", "error");
      return;
    }

    if (submitButton) {
      submitButton.setAttribute("aria-busy", "true");
      submitButton.disabled = true;
      submitButton.textContent = "Preparing brief...";
    }
    setStatus("Preparing your secure flight request...", "");

    var body = [
      "AriaJet flight brief request",
      "",
      "Name: " + fieldValue("name"),
      "Email: " + fieldValue("email"),
      "Phone: " + fieldValue("phone"),
      "Mission type: " + fieldValue("mission"),
      "Origin: " + fieldValue("origin"),
      "Destination: " + fieldValue("destination"),
      "Travel date/window: " + fieldValue("dates"),
      "Passengers: " + fieldValue("passengers"),
      "",
      "Concierge notes:",
      fieldValue("message") || "None provided"
    ].join("\n");

    window.setTimeout(function () {
      var subject = encodeURIComponent("AriaJet flight brief request");
      var mailBody = encodeURIComponent(body);
      window.location.href = "mailto:concierge@ariajet.site?subject=" + subject + "&body=" + mailBody;

      if (submitButton) {
        submitButton.removeAttribute("aria-busy");
        submitButton.disabled = false;
        submitButton.textContent = defaultLabel;
      }
      setStatus("Your email client should open with the flight brief. Send it to complete the request.", "success");
    }, 450);
  });
}());
