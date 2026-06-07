<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="stock-research-dashboard">
    <h1><?php esc_html_e('Stock Research Tool', 'freeride-investor'); ?></h1>
    <form id="stock-research-form">
        <?php wp_nonce_field('fri_stock_research_nonce', 'security'); ?>
        <label for="stock-symbol"><?php esc_html_e('Enter Stock Symbols:', 'freeride-investor'); ?></label>
        <input
            type="text"
            id="stock-symbol"
            name="stock_symbols"
            placeholder="<?php esc_attr_e('e.g., TSLA, AAPL, GOOGL', 'freeride-investor'); ?>"
            required
            aria-required="true"
        />
        <button type="submit"><?php esc_html_e('Fetch Data', 'freeride-investor'); ?></button>
    </form>

    <!-- Results Container -->
    <div id="stocks-container" aria-live="polite" aria-atomic="true"></div>
</div>
