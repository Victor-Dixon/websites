<?php
/**
 * Template Name: ELS Research Suite
 *
 * Dedicated landing page for the Equidistant Letter Sequence (ELS) plugin outputs.
 *
 * @package Swarm_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="els-suite">
    <section class="els-hero">
        <div class="els-hero__content">
            <p class="els-pill">Prophetical Analysis · Live Data</p>
            <h1>ELS Research Suite</h1>
            <p class="els-lede">
                Explore authentic Equidistant Letter Sequence discoveries, run live Gematria comparisons,
                and browse the continually growing library of prophetical insights—all powered by the
                We Are Swarm plugin.
            </p>
            <div class="els-cta">
                <a class="els-button" href="#els-timeline">View Timeline</a>
                <a class="els-button els-button--ghost" href="#els-gematria">Run Gematria</a>
            </div>
        </div>
        <div class="els-hero__stats" id="els-statistics" aria-live="polite">
            <div class="els-stat-card">
                <p class="els-stat-label">Books Analyzed</p>
                <p class="els-stat-value">39</p>
            </div>
            <div class="els-stat-card">
                <p class="els-stat-label">Chapters Scanned</p>
                <p class="els-stat-value">929</p>
            </div>
            <div class="els-stat-card">
                <p class="els-stat-label">Patterns Logged</p>
                <p class="els-stat-value" id="els-pattern-count">50K+</p>
            </div>
        </div>
    </section>

    <section class="els-panel" id="els-timeline">
        <header class="els-panel__header">
            <div>
                <p class="els-pill">Timeline Intelligence</p>
                <h2>Prophetical Evidence Timeline</h2>
                <p>Live feed of verified ELS discoveries, chronological context, and impact scoring.</p>
            </div>
            <button class="els-refresh" type="button" data-els-refresh="timeline">↻ Refresh</button>
        </header>
        <div class="els-grid" data-els="timeline" aria-live="polite">
            <div class="els-card els-card--placeholder">
                <p>Loading prophetical events…</p>
            </div>
        </div>
    </section>

    <section class="els-panel" id="els-gematria">
        <header class="els-panel__header">
            <div>
                <p class="els-pill">Gematria Engine</p>
                <h2>Compare Hebrew Word Signatures</h2>
                <p>Quantify the numerical relationships between Hebrew terms to surface divine-order patterns.</p>
            </div>
        </header>
        <form class="els-form" id="els-gematria-form">
            <label for="els-gematria-input">Terms (comma separated Hebrew or transliterated words)</label>
            <input type="text" id="els-gematria-input" name="terms" placeholder="משיח, ברית, תורה" required>
            <button class="els-button" type="submit">Calculate Gematria</button>
        </form>
        <div class="els-grid" data-els="gematria-results" aria-live="polite">
            <div class="els-card els-card--placeholder">
                <p>Submit your terms to see comparative results.</p>
            </div>
        </div>
    </section>

    <section class="els-panel" id="els-library">
        <header class="els-panel__header">
            <div>
                <p class="els-pill">Research Library</p>
                <h2>Pattern Library Explorer</h2>
                <p>Browse indexed ELS findings with keyword filters and direct Hebrew/English references.</p>
            </div>
            <div class="els-library-filter">
                <input type="search" id="els-library-search" placeholder="Search keyword or book" aria-label="Search library entries">
                <button class="els-button els-button--ghost" type="button" data-els-search="library">Search</button>
            </div>
        </header>
        <div class="els-grid" data-els="library" aria-live="polite">
            <div class="els-card els-card--placeholder">
                <p>Loading library entries…</p>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

