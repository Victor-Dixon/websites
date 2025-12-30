/**
 * Flush Rewrite Rules on Theme Activation
 *
 * @package SimplifiedTradingTheme
 */

function simplifiedtheme_rewrite_flush() {
    // Register post types and taxonomies before flushing
    simplifiedtheme_register_cheat_sheet();
    simplifiedtheme_register_free_investor();
    simplifiedtheme_register_tbow_tactics();
    simplifiedtheme_register_stock_category();
    // Brand Core Custom Post Types (Phase 1 P0 Fixes)
    simplifiedtheme_register_positioning_statement();
    simplifiedtheme_register_offer_ladder();
    simplifiedtheme_register_icp_definition();

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'simplifiedtheme_rewrite_flush');
