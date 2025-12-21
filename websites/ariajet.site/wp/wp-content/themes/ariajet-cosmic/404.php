<?php
/**
 * 404 Error Page Template
 * 
 * Displays a cosmic-themed 404 error page.
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main error-404">
    <div class="container">
        <div class="error-content">
            <!-- Animated 404 Display -->
            <div class="error-animation">
                <div class="planet-404">
                    <span class="digit">4</span>
                    <span class="planet">🪐</span>
                    <span class="digit">4</span>
                </div>
            </div>
            
            <h1 class="error-title">
                <?php _e('Lost in Space!', 'ariajet-cosmic'); ?>
            </h1>
            
            <p class="error-message">
                <?php _e("Oops! Looks like you've drifted into uncharted territory. The page you're looking for has floated away into the cosmic void.", 'ariajet-cosmic'); ?>
            </p>
            
            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cosmic-button">
                    <span class="button-icon">🚀</span>
                    <?php _e('Return to Base', 'ariajet-cosmic'); ?>
                </a>
                
                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="cosmic-button secondary">
                    <span class="button-icon">🎮</span>
                    <?php _e('Play Games', 'ariajet-cosmic'); ?>
                </a>
            </div>
            
            <!-- Floating Space Elements -->
            <div class="error-decorations">
                <div class="floating-item item-1">⭐</div>
                <div class="floating-item item-2">🌙</div>
                <div class="floating-item item-3">✨</div>
                <div class="floating-item item-4">🛸</div>
                <div class="floating-item item-5">💫</div>
            </div>
        </div>
    </div>
</main>

<style>
.error-404 {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-content {
    text-align: center;
    padding: var(--space-10);
    position: relative;
}

.error-animation {
    margin-bottom: var(--space-8);
}

.planet-404 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-4);
}

.planet-404 .digit {
    font-family: var(--font-display);
    font-size: 8rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--neon-purple) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: none;
    filter: drop-shadow(0 0 30px rgba(0, 255, 247, 0.5));
}

.planet-404 .planet {
    font-size: 6rem;
    animation: planet-spin 10s linear infinite;
}

@keyframes planet-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.error-title {
    font-size: var(--text-4xl);
    margin-bottom: var(--space-6);
    color: var(--text-primary);
}

.error-message {
    font-size: var(--text-lg);
    color: var(--text-secondary);
    max-width: 500px;
    margin: 0 auto var(--space-10);
    line-height: 1.8;
}

.error-actions {
    display: flex;
    gap: var(--space-4);
    justify-content: center;
    flex-wrap: wrap;
}

/* Floating Decorations */
.error-decorations {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.floating-item {
    position: absolute;
    font-size: 2rem;
    opacity: 0.5;
    animation: cosmic-float 6s ease-in-out infinite;
}

.item-1 { top: 10%; left: 10%; animation-delay: 0s; }
.item-2 { top: 20%; right: 15%; animation-delay: -1s; }
.item-3 { bottom: 30%; left: 5%; animation-delay: -2s; }
.item-4 { top: 40%; right: 5%; animation-delay: -3s; }
.item-5 { bottom: 10%; right: 20%; animation-delay: -4s; }

@keyframes cosmic-float {
    0%, 100% { 
        transform: translate(0, 0) rotate(0deg); 
        opacity: 0.3;
    }
    25% { 
        transform: translate(10px, -15px) rotate(90deg); 
        opacity: 0.7;
    }
    50% { 
        transform: translate(-5px, -25px) rotate(180deg); 
        opacity: 0.5;
    }
    75% { 
        transform: translate(15px, -10px) rotate(270deg); 
        opacity: 0.6;
    }
}

@media (max-width: 768px) {
    .planet-404 .digit {
        font-size: 5rem;
    }
    
    .planet-404 .planet {
        font-size: 4rem;
    }
    
    .error-title {
        font-size: var(--text-2xl);
    }
    
    .error-message {
        font-size: var(--text-base);
    }
    
    .error-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .floating-item {
        font-size: 1.5rem;
    }
}
</style>

<?php
get_footer();
