<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="alert-setup-dashboard">
    <h1><?php esc_html_e('Stock Alert Setup', 'freeride-investor'); ?></h1>

    <form id="alert-setup-form">
        <?php wp_nonce_field('fri_stock_research_nonce', 'security'); ?>
        
        <!-- Email Address -->
        <label for="alert-email"><?php esc_html_e('Your Email Address:', 'freeride-investor'); ?></label>
        <input
            type="email"
            id="alert-email"
            name="alert_email"
            placeholder="<?php esc_attr_e('your-email@example.com', 'freeride-investor'); ?>"
            required
            aria-required="true"
        />

        <!-- Stock Symbol -->
        <label for="alert-symbol"><?php esc_html_e('Stock Symbol:', 'freeride-investor'); ?></label>
        <input
            type="text"
            id="alert-symbol"
            name="alert_symbol"
            placeholder="<?php esc_attr_e('e.g., TSLA', 'freeride-investor'); ?>"
            required
            aria-required="true"
        />

        <!-- Alert Type -->
        <label for="alert-type"><?php esc_html_e('Alert Type:', 'freeride-investor'); ?></label>
        <select id="alert-type" name="alert_type" required>
            <option value="price_above"><?php esc_html_e('Price Above', 'freeride-investor'); ?></option>
            <option value="price_below"><?php esc_html_e('Price Below', 'freeride-investor'); ?></option>
            <option value="sentiment_above"><?php esc_html_e('Sentiment Above', 'freeride-investor'); ?></option>
            <option value="sentiment_below"><?php esc_html_e('Sentiment Below', 'freeride-investor'); ?></option>
        </select>

        <!-- Condition Value -->
        <label for="alert-value"><?php esc_html_e('Condition Value:', 'freeride-investor'); ?></label>
        <input
            type="number"
            id="alert-value"
            name="alert_value"
            placeholder="<?php esc_attr_e('Enter the value', 'freeride-investor'); ?>"
            required
        />

        <!-- Submit Button -->
        <button type="submit"><?php esc_html_e('Set Alert', 'freeride-investor'); ?></button>
    </form>

    <!-- Message Display -->
    <div id="alert-message" aria-live="polite" aria-atomic="true"></div>
</div>
