(function () {
  function handleCopy(event) {
    var button = event.currentTarget;
    var card = button.closest('.swarm-heartbeat-card');
    if (!card) {
      return;
    }
    var snapshot = card.getAttribute('data-swarm-snapshot');
    if (!snapshot) {
      return;
    }
    navigator.clipboard.writeText(snapshot).then(function () {
      button.classList.add('is-copied');
      button.textContent = 'Copied';
      setTimeout(function () {
        button.classList.remove('is-copied');
        button.textContent = 'Copy status';
      }, 2000);
    });
  }

  document.addEventListener('click', function (event) {
    if (!event.target.classList.contains('swarm-heartbeat-copy')) {
      return;
    }
    handleCopy(event);
  });
})();
