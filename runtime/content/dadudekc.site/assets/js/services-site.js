(function () {
  var toggle = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.nav-links');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', nav.classList.contains('open'));
    });
  }

  var form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var data = new FormData(form);
      var name = data.get('name') || '';
      var email = data.get('email') || '';
      var business = data.get('business') || '';
      var website = data.get('website') || '';
      var problem = data.get('problem') || '';
      var body = [
        'Name: ' + name,
        'Email: ' + email,
        'Business: ' + business,
        'Current website: ' + website,
        '',
        'Biggest workflow problem:',
        problem
      ].join('\n');
      var subject = encodeURIComponent('Automation Audit Request — ' + (business || name));
      var mailBody = encodeURIComponent(body);
      window.location.href = 'mailto:hello@dadudekc.site?subject=' + subject + '&body=' + mailBody;
    });
  }
})();
