<?php
/**
 * 404 Error Page
 * 
 * A friendly, helpful 404 page.
 * 
 * @package AriaJet_Studio
 */

get_header();
?>

<main id="main" class="site-main">
    <section class="section error-404-section">
        <div class="container container--narrow">
            <div class="error-content text-center">
                
                <div class="error-illustration animate-float">
                    <span class="error-emoji">🔍</span>
                </div>
                
                <h1 class="error-title">
                    Hmm, that page doesn't exist.
                </h1>
                
                <p class="error-message lead">
                    The page you're looking for might have been moved, 
                    deleted, or maybe it never existed. No worries though!
                </p>
                
                <div class="error-suggestions">
                    <h2 class="suggestions-title">Here's what you can do:</h2>
                    
                    <div class="suggestions-grid">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="suggestion-card">
                            <span class="icon-box icon-box--blush">🏠</span>
                            <span class="suggestion-text">Go back home</span>
                        </a>
                        
                        <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="suggestion-card">
                            <span class="icon-box icon-box--sage">🎮</span>
                            <span class="suggestion-text">Play a game</span>
                        </a>
                        
                        <a href="#" onclick="history.back(); return false;" class="suggestion-card">
                            <span class="icon-box icon-box--lavender">←</span>
                            <span class="suggestion-text">Go back</span>
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
</main>

<style>
.error-404-section {
    min-height: calc(100vh - 300px);
    display: flex;
    align-items: center;
}

.error-content {
    padding: var(--space-16) 0;
}

.error-illustration {
    margin-bottom: var(--space-8);
}

.error-emoji {
    font-size: 5rem;
    display: inline-block;
}

.error-title {
    font-size: var(--text-4xl);
    margin-bottom: var(--space-5);
}

.error-message {
    max-width: 440px;
    margin: 0 auto var(--space-12);
}

.suggestions-title {
    font-family: var(--font-body);
    font-size: var(--text-sm);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: var(--tracking-wider);
    color: var(--ink-muted);
    margin-bottom: var(--space-6);
}

.suggestions-grid {
    display: flex;
    justify-content: center;
    gap: var(--space-4);
    flex-wrap: wrap;
}

.suggestion-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-6);
    background: var(--soft-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    min-width: 140px;
    text-decoration: none;
    transition: all var(--duration-normal) var(--ease-smooth);
}

.suggestion-card:hover {
    border-color: var(--border-hover);
    box-shadow: 0 4px 20px var(--shadow-soft);
    transform: translateY(-3px);
}

.suggestion-text {
    font-size: var(--text-sm);
    font-weight: 500;
    color: var(--ink);
}

@media (max-width: 640px) {
    .error-title {
        font-size: var(--text-2xl);
    }
    
    .suggestions-grid {
        flex-direction: column;
        align-items: stretch;
    }
    
    .suggestion-card {
        flex-direction: row;
        justify-content: flex-start;
    }
}
</style>

<?php
get_footer();
