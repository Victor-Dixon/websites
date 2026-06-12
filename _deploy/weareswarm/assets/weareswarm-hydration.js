(() => {
  const ROUTES = {
    '/projects/': {
      title: 'Live project data contract',
      file: '/data/planner/projects_full.json',
      list: 'projects',
      fallbackTitle: 'Fallback project snapshot ready',
      fallbackText: 'The verified static project cards remain visible if the JSON path is missing.',
      render: (p, esc) => `<article class="card"><h3>${esc(p.title)}</h3><p>${esc(p.one_line)}</p><div class="proof">STATUS=${esc(p.status)}<br>DOMAIN=${esc(p.domain)}<br>NEXT=${esc(p.next_unlock)}</div><div class="cta"><a class="btn" href="${esc(p.live_url || '#')}">Open</a></div></article>`,
    },
    '/tasks/': {
      title: 'Hydrated task feed',
      file: '/data/planner/all_tasks.json',
      list: 'tasks',
      fallbackTitle: 'Fallback task snapshot ready',
      fallbackText: 'The static task board remains visible if the public JSON path is missing.',
      render: (t, esc) => `<article class="card"><span class="tag">${esc(t.priority)}</span><span class="tag blue">${esc(t.executor)}</span><h3>${esc(t.title)}</h3><p>${esc(t.summary)}</p><div class="proof">LANE=${esc(t.lane)}<br>STATUS=${esc(t.status)}<br>NEXT=${esc(t.next_action)}</div></article>`,
    },
    '/feed/': {
      title: 'Public closeout JSON',
      file: '/data/planner/closeouts.json',
      list: 'closeouts',
      fallbackTitle: 'Fallback closeouts ready',
      fallbackText: 'The verified static closeouts remain visible if the JSON path is missing.',
      render: (c, esc) => `<article class="card"><span class="tag">${esc(c.date)}</span><h3>${esc(c.title)}</h3><p>${esc(c.summary)}</p><div class="proof">${esc(c.proof)}</div><div class="cta"><a class="btn" href="${esc(c.url)}">Open proof</a></div></article>`,
    },
    '/skill-tree/': {
      title: 'Hydrated capability map',
      file: '/data/planner/skill_tree.json',
      list: 'nodes',
      fallbackTitle: 'Fallback capability snapshot ready',
      fallbackText: 'The static skill tree remains visible if the JSON path is missing.',
      render: (n, esc) => `<article class="card"><span class="tag">${esc(n.status)}</span><h3>${esc(n.title)}</h3><div class="proof">PROOF=${esc(n.proof)}</div></article>`,
    },
  };

  const route = ROUTES[window.location.pathname];
  if (!route) return;

  const esc = (value) => String(value || '').replace(/[&<>]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[char]));
  const section = document.createElement('section');
  section.id = 'data-contract';
  section.innerHTML = `<h2>${route.title}</h2><p class="proof" data-sync>Sync source: ${route.file}</p><div class="grid" data-cards><article class="card"><h3>${route.fallbackTitle}</h3><p>${route.fallbackText}</p><div class="proof">MISSING_FILE=${route.file}<br>FALLBACK=static route snapshot</div></article></div>`;

  const main = document.querySelector('main');
  const footer = main?.querySelector('footer');
  if (!main || !footer) return;
  main.insertBefore(section, footer);

  const sync = section.querySelector('[data-sync]');
  const cards = section.querySelector('[data-cards]');
  fetch(route.file)
    .then((response) => {
      if (!response.ok) throw new Error(`${route.file} returned ${response.status}`);
      return response.json();
    })
    .then((data) => {
      const items = data[route.list] || [];
      sync.textContent = `Last sync: ${data.last_sync || 'unknown'} · Source: ${route.file}`;
      cards.innerHTML = items.map((item) => route.render(item, esc)).join('');
    })
    .catch((error) => {
      sync.textContent = `Data fallback active: ${error.message}`;
    });
})();
