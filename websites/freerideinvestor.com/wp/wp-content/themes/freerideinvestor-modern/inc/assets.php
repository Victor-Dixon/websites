function simplifiedtheme_assets() {
    wp_enqueue_style('main-css', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    
    // Navigation CSS - CRITICAL for menu styling
    wp_enqueue_style(
        'freeride-navigation-css',
        get_template_directory_uri() . '/css/styles/components/_navigation.css',
        ['main-css'],
        wp_get_theme()->get('Version')
    );
    
    // Header/Footer CSS - CRITICAL for header styling
    wp_enqueue_style(
        'freeride-header-footer-css',
        get_template_directory_uri() . '/css/styles/layout/_header-footer.css',
        ['main-css'],
        wp_get_theme()->get('Version')
    );
    
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap', [], null);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], '5.15.4');
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    wp_enqueue_style('custom-css', get_template_directory_uri() . '/css/custom.css', ['main-css', 'freeride-navigation-css', 'freeride-header-footer-css'], '1.1');
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/js/custom.js', ['jquery'], '1.1', true);

    wp_localize_script('custom-js', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'simplifiedtheme_assets');
