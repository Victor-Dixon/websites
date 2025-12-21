(() => {
  const selectors = {
    timeline: '[data-els="timeline"]',
    gematriaForm: '#els-gematria-form',
    gematriaResults: '[data-els="gematria-results"]',
    library: '[data-els="library"]',
    librarySearch: '#els-library-search',
  };

  const restBase = (window.swarmElsSuite && window.swarmElsSuite.restBase)
    ? window.swarmElsSuite.restBase.replace(/\/$/, '')
    : '';

  if (!restBase) {
    return;
  }

  const endpoints = {
    timeline: `${restBase}/timeline/events`,
    gematria: `${restBase}/gematria/calculate`,
    library: `${restBase}/library/entries`,
  };

  const fetchJSON = async (url, options = {}) => {
    try {
      const response = await fetch(url, {
        headers: { 'Content-Type': 'application/json' },
        ...options,
      });
      if (!response.ok) throw new Error(`Request failed: ${response.status}`);
      return await response.json();
    } catch (error) {
      console.error('[ELS Suite] Request error', error);
      throw error;
    }
  };

  const renderTimeline = async () => {
    const container = document.querySelector(selectors.timeline);
    if (!container) return;

    container.innerHTML = '<div class="els-card els-card--placeholder"><p>Loading prophetical events…</p></div>';

    try {
      const data = await fetchJSON(endpoints.timeline);
      const events = data?.events || [];
      if (!events.length) {
        container.innerHTML = '<div class="els-card els-card--placeholder"><p>No timeline entries found.</p></div>';
        return;
      }

      container.innerHTML = events.map(event => `
        <article class="els-card">
          <header>
            <p class="els-pill">${event.era || 'Verified Evidence'}</p>
            <h3>${event.title || 'Unnamed Event'}</h3>
            <p class="els-meta">${event.year ? `Year: ${event.year}` : ''}</p>
          </header>
          <p>${event.description || 'No description available.'}</p>
          <div class="els-card__footer">
            ${event.permalink ? `<a href="${event.permalink}" target="_blank" rel="noopener">View Evidence →</a>` : ''}
            <span class="els-score">${event.score ? `${event.score} Score` : ''}</span>
          </div>
        </article>
      `).join('');
    } catch (error) {
      container.innerHTML = '<div class="els-card els-card--placeholder"><p>Unable to load timeline data.</p></div>';
    }
  };

  const renderGematria = () => {
    const form = document.querySelector(selectors.gematriaForm);
    const results = document.querySelector(selectors.gematriaResults);
    if (!form || !results) return;

    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const input = document.querySelector('#els-gematria-input');
      const terms = input.value.split(',').map(term => term.trim()).filter(Boolean);

      if (!terms.length) return;

      results.innerHTML = '<div class="els-card els-card--placeholder"><p>Calculating…</p></div>';

      try {
        const data = await fetchJSON(endpoints.gematria, {
          method: 'POST',
          body: JSON.stringify({ terms }),
        });

        const comparisons = data?.results || data;
        if (!comparisons || !comparisons.length) {
          results.innerHTML = '<div class="els-card els-card--placeholder"><p>No Gematria results returned.</p></div>';
          return;
        }

        results.innerHTML = comparisons.map(item => `
          <article class="els-card">
            <header>
              <h3>${item.term || 'Term'}</h3>
              <p class="els-meta">Value: ${item.value ?? '—'}</p>
            </header>
            <p>${item.explanation || 'No commentary provided.'}</p>
          </article>
        `).join('');
      } catch (error) {
        results.innerHTML = '<div class="els-card els-card--placeholder"><p>Gematria calculation failed.</p></div>';
      }
    });
  };

  const renderLibrary = async (query = '') => {
    const container = document.querySelector(selectors.library);
    if (!container) return;

    container.innerHTML = '<div class="els-card els-card--placeholder"><p>Loading entries…</p></div>';

    const url = new URL(endpoints.library);
    if (query) {
      url.searchParams.set('keyword', query);
    }

    try {
      const data = await fetchJSON(url.toString());
      const entries = data?.entries || data;

      if (!entries || !entries.length) {
        container.innerHTML = '<div class="els-card els-card--placeholder"><p>No entries found for that query.</p></div>';
        return;
      }

      container.innerHTML = entries.map(entry => `
        <article class="els-card">
          <header>
            <p class="els-pill">${entry.book || 'Library Entry'}</p>
            <h3>${entry.title || 'Untitled finding'}</h3>
          </header>
          <p>${entry.summary || 'Summary unavailable.'}</p>
          <div class="els-card__footer">
            <span>${entry.chapter ? `Chapter ${entry.chapter}` : ''}</span>
            ${entry.reference ? `<span>${entry.reference}</span>` : ''}
          </div>
        </article>
      `).join('');
    } catch (error) {
      container.innerHTML = '<div class="els-card els-card--placeholder"><p>Unable to load library data.</p></div>';
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    renderTimeline();
    renderGematria();
    renderLibrary();

    document.querySelectorAll('[data-els-refresh="timeline"]').forEach(button => {
      button.addEventListener('click', renderTimeline);
    });

    const librarySearchInput = document.querySelector(selectors.librarySearch);
    const searchButton = document.querySelector('[data-els-search="library"]');
    if (searchButton && librarySearchInput) {
      searchButton.addEventListener('click', () => {
        renderLibrary(librarySearchInput.value.trim());
      });
    }
  });
})();

