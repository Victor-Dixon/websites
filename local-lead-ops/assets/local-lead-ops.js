(function () {
  "use strict";

  var form = document.getElementById("demo-lead-form");
  var result = document.getElementById("demo-result");
  var timeline = document.getElementById("followup-timeline");

  if (!form || !result) {
    return;
  }

  function setTimelineStep(step) {
    if (!timeline) {
      return;
    }
    var steps = timeline.querySelectorAll(".timeline-step");
    steps.forEach(function (el, index) {
      el.classList.toggle("active", index === step);
    });
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    var name = form.querySelector('[name="name"]').value.trim() || "Demo Visitor";
    var service = form.querySelector('[name="service"]').value;
    var message = form.querySelector('[name="message"]').value.trim();

    result.innerHTML =
      "<strong>Demo lead captured</strong><br>" +
      "Name: " +
      escapeHtml(name) +
      "<br>Service: " +
      escapeHtml(service) +
      (message ? "<br>Message: " + escapeHtml(message) : "") +
      "<br><em>No network request was sent. This is a local demo only.</em>";

    result.classList.add("visible");
    setTimelineStep(1);

    var cockpitStatus = document.getElementById("cockpit-status");
    if (cockpitStatus) {
      cockpitStatus.textContent = "Awaiting operator approval";
    }

    var cockpitQueue = document.getElementById("cockpit-queue");
    if (cockpitQueue) {
      cockpitQueue.textContent = "1 demo lead staged";
    }
  });

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  setTimelineStep(0);
})();
