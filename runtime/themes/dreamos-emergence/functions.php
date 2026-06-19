<?php
/**
 * DreamOS Emergence theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

function dreamos_emergence_setup(): void {
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'dreamos_emergence_setup');

function dreamos_emergence_assets(): void {
    wp_enqueue_style(
        'dreamos-emergence-style',
        get_stylesheet_uri(),
        [],
        (string) filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'dreamos_emergence_assets');

function dreamos_emergence_placeholder_needles(): array {
    return [
        'trans-' . 'menu',
        'trans-' . 'contacts',
        'email@' . 'email.com',
        '+' . '123456789',
        'trans-' . 'socials',
        'trans-' . 'newsletter',
    ];
}

function dreamos_emergence_cleanup_placeholder_text(string $content): string {
    return str_replace(dreamos_emergence_placeholder_needles(), '', $content);
}
add_filter('the_content', 'dreamos_emergence_cleanup_placeholder_text', 20);

function dreamos_emergence_shortcode_frame(string $content): string {
    if (is_admin()) {
        return $content;
    }

    $shortcodes = [
        'spark_generator',
        'spark_battle_sim',
        'spark_battle',
        'emergence_character_generator',
    ];

    foreach ($shortcodes as $shortcode) {
        if (has_shortcode($content, $shortcode)) {
            return '<div class="dreamos-plugin-frame">' . $content . '</div>';
        }
    }

    return $content;
}
add_filter('the_content', 'dreamos_emergence_shortcode_frame', 8);

function dreamos_emergence_dispatch_paths(): array {
    return [
        'missions',
        'mission',
        'dispatch',
        'mission-dispatch',
        'meridian-dispatch',
        'open-mission',
    ];
}

function dreamos_emergence_route_path(): string {
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return strtolower($path);
}

function dreamos_emergence_render_mission_dispatch(): void {
    status_header(200);
    nocache_headers();

    $generator_url = home_url('/spark-generator/?mission=first-awakening');
    $battle_url = home_url('/battles/');
    $dashboard_url = home_url('/spark-dashboard/');
    $home_url = home_url('/');
    ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('dreamos-mission-dispatch-page'); ?>>
<?php wp_body_open(); ?>
<main id="mission-dispatch" class="dreamos-dispatch" aria-labelledby="mission-dispatch-title">
    <section class="dreamos-dispatch-hero">
        <a class="dreamos-dispatch-back" href="<?php echo esc_url($home_url); ?>">Back to Spark OS</a>
        <div class="dreamos-dispatch-grid">
            <div class="dreamos-dispatch-copy">
                <p class="dreamos-dispatch-kicker">Mission dispatch is live</p>
                <h1 id="mission-dispatch-title">Open Mission</h1>
                <p class="dreamos-dispatch-lede">
                    Start with one clear assignment: generate your Spark, read the briefing, then choose whether to train, save, or test that Spark in the arena.
                </p>
                <div class="dreamos-dispatch-actions" aria-label="Primary mission actions">
                    <a class="dreamos-dispatch-button dreamos-dispatch-button-primary" href="<?php echo esc_url($generator_url); ?>">Start First Awakening</a>
                    <a class="dreamos-dispatch-button" href="<?php echo esc_url($dashboard_url); ?>">Open Command Post</a>
                </div>
            </div>
            <aside class="dreamos-dispatch-panel" aria-label="Current mission briefing">
                <div class="dreamos-dispatch-status"><span></span> Ready for dispatch</div>
                <h2>First Awakening</h2>
                <p>
                    Your first mission is built for a smooth start. Create the character profile first, then use the saved Spark links for reload, sharing, and battle testing.
                </p>
                <ol class="dreamos-dispatch-steps">
                    <li><strong>Generate</strong><span>Create your Spark identity and power domain.</span></li>
                    <li><strong>Save</strong><span>Use the character record so your work is easy to reload.</span></li>
                    <li><strong>Test</strong><span>Open the battle simulator when you are ready for a projection.</span></li>
                </ol>
            </aside>
        </div>
    </section>

    <section class="dreamos-dispatch-routes" aria-labelledby="dispatch-route-title">
        <div class="dreamos-dispatch-section-head">
            <p class="dreamos-dispatch-kicker">Choose a route</p>
            <h2 id="dispatch-route-title">Everything important is one click away.</h2>
        </div>
        <div class="dreamos-dispatch-card-grid">
            <a class="dreamos-dispatch-card" href="<?php echo esc_url($generator_url); ?>">
                <span>01</span>
                <strong>New player</strong>
                <p>Begin the mission by generating the Spark dossier the rest of the flow uses.</p>
            </a>
            <a class="dreamos-dispatch-card" href="<?php echo esc_url($battle_url); ?>">
                <span>02</span>
                <strong>Already have a Spark</strong>
                <p>Jump into the What-If Arena and run a controlled battle projection.</p>
            </a>
            <a class="dreamos-dispatch-card" href="<?php echo esc_url($dashboard_url); ?>">
                <span>03</span>
                <strong>Command Post</strong>
                <p>Log in only when you need protected dashboard tools or account-specific progress.</p>
            </a>
        </div>
    </section>
</main>
<?php wp_footer(); ?>
</body>
</html>
    <?php
}

function dreamos_emergence_route_aliases(): void {
    if (is_admin()) {
        return;
    }

    $path = dreamos_emergence_route_path();

    if (in_array($path, dreamos_emergence_dispatch_paths(), true)) {
        if ($path === 'meridian-dispatch' && is_user_logged_in()) {
            return;
        }

        dreamos_emergence_render_mission_dispatch();
        exit;
    }
}
add_action('template_redirect', 'dreamos_emergence_route_aliases', 1);
