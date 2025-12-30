<?php
/**
 * Main Template File
 * 
 * A spacious, curiosity-driven homepage that invites exploration.
 * 
 * @package AriaJet_Studio
 */

get_header();
?>

<main id="main" class="site-main">
    
    <?php if (is_home() && !is_paged()) : ?>
    
    <!-- ═══════════════════════════════════════
         HERO SECTION
         Calm, confident, inviting
         ═══════════════════════════════════════ -->
    <section class="hero section">
        <div class="container">
            <div class="hero-content">
                <p class="hero-eyebrow">
                    <span class="handwritten">Hey there!</span>
                </p>
                
                <h1 class="hero-title">
                    <?php echo esc_html(get_bloginfo('name')); ?>
                </h1>
                
                <?php $tagline = get_bloginfo('description', 'display'); ?>
                <p class="hero-subtitle lead">
                    <?php if (!empty($tagline)) : ?>
                        <?php echo esc_html($tagline); ?>
                    <?php else : ?>
                        I make games, playlists, and all kinds of creative things.
                        <span class="text-coral">Come explore</span> — I think you'll like it here.
                    <?php endif; ?>
                </p>
                
                <div class="hero-actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="btn btn--primary btn--large">
                        <span class="btn-icon">🎮</span>
                        Check out my games
                    </a>
                    <a href="#about" class="btn btn--ghost btn--large">
                        <span class="btn-icon">✨</span>
                        Learn more about me
                    </a>
                </div>
            </div>
            
            <!-- Decorative elements -->
            <div class="hero-decoration">
                <div class="floating-shape shape-1"></div>
                <div class="floating-shape shape-2"></div>
                <div class="floating-shape shape-3"></div>
            </div>
        </div>
    </section>
    
    <!-- ═══════════════════════════════════════
         FEATURED GAMES
         Showing off what Aria creates
         ═══════════════════════════════════════ -->
    <section class="featured-games section section--white">
        <div class="container">
            <header class="section-header">
                <p class="section-eyebrow">
                    <span class="icon-box icon-box--blush">🎮</span>
                    Games I've Made
                </p>
                <h2 class="section-title">
                    Worlds to explore,<br>
                    adventures to have.
                </h2>
                <p class="section-subtitle">
                    I love making games that are fun to play. Here are a few of my favorites.
                </p>
            </header>
            
            <div class="games-showcase">
                <?php
                $games = new WP_Query(array(
                    'post_type' => 'game',
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                
                if ($games->have_posts()) :
                ?>
                <div class="grid grid--3">
                    <?php
                    while ($games->have_posts()) :
                        $games->the_post();
                        $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
                        $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
                        $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
                        ?>
                        <article class="card game-card reveal">
                            <div class="card-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large'); ?>
                                <?php else : ?>
                                    <div class="card-placeholder">
                                        <span class="placeholder-icon">🎮</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($game_status) : ?>
                                    <span class="game-status tag tag--<?php echo esc_attr($game_status === 'published' ? 'sage' : ($game_status === 'beta' ? 'honey' : 'lavender')); ?>">
                                        <?php 
                                        $status_labels = array(
                                            'published' => '✓ Ready to play',
                                            'beta' => '🧪 Beta',
                                            'development' => '🔨 In progress',
                                        );
                                        echo esc_html($status_labels[$game_status] ?? ucfirst($game_status));
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <h3 class="card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <?php if ($game_type) : ?>
                                    <span class="tag tag--coral">
                                        <?php echo esc_html(ucfirst($game_type)); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <p class="card-text">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </p>
                                
                                <div class="card-footer">
                                    <?php if ($game_url) : ?>
                                        <a href="<?php echo esc_url($game_url); ?>" class="btn btn--accent" target="_blank">
                                            Play now →
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>" class="btn btn--ghost">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <?php else : ?>
                <div class="empty-state">
                    <div class="empty-icon">🚀</div>
                    <h3>Games coming soon!</h3>
                    <p>I'm working on some really cool stuff. Check back soon!</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="section-cta text-center mt-8">
                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="btn btn--secondary btn--large">
                    See all my games
                    <span class="btn-icon">→</span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- ═══════════════════════════════════════
         ABOUT SECTION
         Personal, warm, inviting
         ═══════════════════════════════════════ -->
    <section id="about" class="about section">
        <div class="container container--narrow">
            <div class="about-content reveal">
                <div class="about-header">
                    <span class="about-wave">👋</span>
                    <p class="handwritten">Nice to meet you!</p>
                </div>
                
                <h2 class="about-title">
                    I'm Aria.
                </h2>
                
                <div class="about-text">
                    <p class="lead">
                        I love making things — games, music playlists, creative projects, 
                        and anything else that sounds fun to try.
                    </p>
                    <p>
                        This site is where I share all of it. Think of it as my digital 
                        treehouse where I hang out and show off what I'm working on.
                    </p>
                    <p>
                        When I'm not making games, you can find me listening to music, 
                        drawing, or coming up with my next big idea.
                    </p>
                </div>
                
                <div class="about-interests">
                    <h3 class="about-interests-title">Things I'm into right now:</h3>
                    <div class="interests-grid">
                        <div class="interest-item">
                            <span class="icon-box icon-box--blush">🎮</span>
                            <span>Making games</span>
                        </div>
                        <div class="interest-item">
                            <span class="icon-box icon-box--lavender">🎵</span>
                            <span>Music & playlists</span>
                        </div>
                        <div class="interest-item">
                            <span class="icon-box icon-box--sage">🎨</span>
                            <span>Art & design</span>
                        </div>
                        <div class="interest-item">
                            <span class="icon-box icon-box--sky">💡</span>
                            <span>New ideas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php endif; ?>
    
    <!-- ═══════════════════════════════════════
         LATEST POSTS / BLOG
         ═══════════════════════════════════════ -->
    <?php if (have_posts()) : ?>
    <section class="latest-posts section section--white">
        <div class="container">
            <?php if (is_home() && !is_paged()) : ?>
            <header class="section-header">
                <p class="section-eyebrow">
                    <span class="icon-box icon-box--lavender">📝</span>
                    What's New
                </p>
                <h2 class="section-title">
                    Latest thoughts<br>
                    and updates
                </h2>
            </header>
            <?php endif; ?>
            
            <div class="grid grid--2">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('card post-card reveal'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="card-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="post-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>" class="post-date tag">
                                    <?php echo get_the_date('M j, Y'); ?>
                                </time>
                            </div>
                            
                            <h3 class="card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <p class="card-text">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </p>
                            
                            <a href="<?php the_permalink(); ?>" class="read-more">
                                Read more <span class="arrow">→</span>
                            </a>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>
            
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '← Previous',
                'next_text' => 'Next →',
            ));
            ?>
        </div>
    </section>
    <?php else : ?>
    <section class="no-content section">
        <div class="container container--narrow text-center">
            <div class="empty-state">
                <div class="empty-icon animate-float">✨</div>
                <h2>Nothing here yet!</h2>
                <p class="lead">
                    But don't worry — I'm always working on something new. 
                    Check back soon!
                </p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary">
                    Go back home
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ═══════════════════════════════════════
         CTA SECTION
         Friendly sign-off
         ═══════════════════════════════════════ -->
    <?php if (is_home() && !is_paged()) : ?>
    <section class="cta section section--blush">
        <div class="container container--narrow text-center">
            <div class="cta-content reveal">
                <span class="cta-emoji">🌟</span>
                <h2 class="cta-title">
                    Thanks for stopping by!
                </h2>
                <p class="cta-text lead">
                    Feel free to explore, play some games, or just hang out. 
                    This is a judgment-free zone.
                </p>
                <div class="cta-actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="btn btn--primary btn--large">
                        <span class="btn-icon">🎮</span>
                        Play a game
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
</main>

<style>
/* ═══════════════════════════════════════
   HERO SECTION STYLES
   ═══════════════════════════════════════ */
.hero {
    padding: var(--space-24) 0 var(--space-32);
    position: relative;
    overflow: hidden;
}

.hero-content {
    max-width: 700px;
    position: relative;
    z-index: 2;
}

.hero-eyebrow {
    margin-bottom: var(--space-6);
}

.hero-title {
    font-size: var(--text-6xl);
    font-weight: 400;
    line-height: 1.1;
    margin-bottom: var(--space-8);
    letter-spacing: -0.03em;
}

.hero-subtitle {
    margin-bottom: var(--space-10);
    max-width: 520px;
}

.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-4);
}

/* Floating decorative shapes */
.hero-decoration {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 50%;
    pointer-events: none;
}

.floating-shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.5;
}

.shape-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, var(--blush) 0%, var(--lavender) 100%);
    top: 10%;
    right: 10%;
    animation: float 8s ease-in-out infinite;
}

.shape-2 {
    width: 150px;
    height: 150px;
    background: var(--sage);
    top: 50%;
    right: 30%;
    animation: float 6s ease-in-out infinite;
    animation-delay: -2s;
}

.shape-3 {
    width: 80px;
    height: 80px;
    background: var(--honey);
    bottom: 20%;
    right: 15%;
    animation: float 5s ease-in-out infinite;
    animation-delay: -4s;
}

@media (max-width: 1024px) {
    .hero-decoration {
        opacity: 0.3;
    }
}

@media (max-width: 768px) {
    .hero {
        padding: var(--space-16) 0;
    }
    
    .hero-title {
        font-size: var(--text-4xl);
    }
    
    .hero-actions {
        flex-direction: column;
    }
    
    .hero-decoration {
        display: none;
    }
}

/* ═══════════════════════════════════════
   GAME CARD ADDITIONS
   ═══════════════════════════════════════ */
.game-status {
    position: absolute;
    top: var(--space-4);
    left: var(--space-4);
}

.card-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--cream-dark) 0%, var(--blush) 100%);
}

.placeholder-icon {
    font-size: 3rem;
    opacity: 0.5;
}

/* ═══════════════════════════════════════
   ABOUT SECTION
   ═══════════════════════════════════════ */
.about-content {
    text-align: center;
}

.about-header {
    margin-bottom: var(--space-6);
}

.about-wave {
    font-size: 3rem;
    display: inline-block;
    animation: wave 2s ease-in-out infinite;
    transform-origin: 70% 70%;
}

@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(20deg); }
    75% { transform: rotate(-10deg); }
}

.about-title {
    font-size: var(--text-5xl);
    margin-bottom: var(--space-8);
}

.about-text {
    margin-bottom: var(--space-12);
}

.about-text p {
    max-width: 540px;
    margin-left: auto;
    margin-right: auto;
}

.about-interests {
    background: var(--soft-white);
    border-radius: var(--radius-2xl);
    padding: var(--space-10);
    border: 1px solid var(--border);
}

.about-interests-title {
    font-family: var(--font-body);
    font-size: var(--text-sm);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: var(--tracking-wider);
    color: var(--ink-muted);
    margin-bottom: var(--space-6);
}

.interests-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
}

.interest-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-3);
    font-size: var(--text-sm);
    color: var(--ink-light);
}

@media (max-width: 768px) {
    .interests-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ═══════════════════════════════════════
   CTA SECTION
   ═══════════════════════════════════════ */
.cta-content {
    padding: var(--space-8) 0;
}

.cta-emoji {
    font-size: 3rem;
    display: block;
    margin-bottom: var(--space-6);
}

.cta-title {
    margin-bottom: var(--space-4);
}

.cta-text {
    margin-bottom: var(--space-8);
    max-width: 480px;
    margin-left: auto;
    margin-right: auto;
}

/* ═══════════════════════════════════════
   EMPTY STATE
   ═══════════════════════════════════════ */
.empty-state {
    padding: var(--space-16) var(--space-6);
    text-align: center;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: var(--space-6);
    display: block;
}

.empty-state h3 {
    margin-bottom: var(--space-4);
}

.empty-state p {
    max-width: 400px;
    margin: 0 auto var(--space-8);
}

/* ═══════════════════════════════════════
   READ MORE LINK
   ═══════════════════════════════════════ */
.read-more {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-weight: 500;
    font-size: var(--text-sm);
    color: var(--coral);
}

.read-more .arrow {
    transition: transform var(--duration-normal) var(--ease-smooth);
}

.read-more:hover .arrow {
    transform: translateX(4px);
}

/* ═══════════════════════════════════════
   PAGINATION
   ═══════════════════════════════════════ */
.pagination,
.nav-links {
    display: flex;
    justify-content: center;
    gap: var(--space-2);
    margin-top: var(--space-12);
}

.page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 var(--space-3);
    font-size: var(--text-sm);
    font-weight: 500;
    color: var(--ink-muted);
    background: var(--soft-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    transition: all var(--duration-normal) var(--ease-smooth);
}

.page-numbers:hover {
    border-color: var(--border-hover);
    color: var(--ink);
}

.page-numbers.current {
    background: var(--ink);
    color: var(--cream);
    border-color: var(--ink);
}
</style>

<?php
get_footer();
