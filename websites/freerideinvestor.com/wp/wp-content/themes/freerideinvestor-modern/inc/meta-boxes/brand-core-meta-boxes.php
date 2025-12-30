<?php
/**
 * Brand Core Meta Boxes
 * Phase 1 P0 Fixes - Custom Fields for Positioning Statements, Offer Ladders, ICP Definitions
 *
 * @package SimplifiedTradingTheme
 */

/**
 * Add Brand Core Meta Boxes
 */
function stt_add_brand_core_meta_boxes() {
    // Positioning Statement Meta Box
    add_meta_box(
        'stt_positioning_statement_meta_box',
        __('Positioning Statement Details', 'simplifiedtradingtheme'),
        'stt_render_positioning_statement_meta_box',
        'positioning_statement',
        'normal',
        'high'
    );

    // Offer Ladder Meta Box
    add_meta_box(
        'stt_offer_ladder_meta_box',
        __('Offer Ladder Details', 'simplifiedtradingtheme'),
        'stt_render_offer_ladder_meta_box',
        'offer_ladder',
        'normal',
        'high'
    );

    // ICP Definition Meta Box
    add_meta_box(
        'stt_icp_definition_meta_box',
        __('ICP Definition Details', 'simplifiedtradingtheme'),
        'stt_render_icp_definition_meta_box',
        'icp_definition',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'stt_add_brand_core_meta_boxes');

/**
 * Render Positioning Statement Meta Box
 *
 * @param WP_Post $post Current post object.
 */
function stt_render_positioning_statement_meta_box($post) {
    wp_nonce_field('stt_save_positioning_statement', 'stt_positioning_statement_nonce');

    $target_audience = get_post_meta($post->ID, 'target_audience', true);
    $pain_points = get_post_meta($post->ID, 'pain_points', true);
    $unique_value = get_post_meta($post->ID, 'unique_value', true);
    $differentiation = get_post_meta($post->ID, 'differentiation', true);
    $site_assignment = get_post_meta($post->ID, 'site_assignment', true);

    ?>
    <table class="form-table">
        <tr>
            <th><label for="stt_target_audience"><?php esc_html_e('Target Audience', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="text" id="stt_target_audience" name="stt_target_audience" value="<?php echo esc_attr($target_audience); ?>" class="regular-text" placeholder="For [target audience]" /></td>
        </tr>
        <tr>
            <th><label for="stt_pain_points"><?php esc_html_e('Pain Points', 'simplifiedtradingtheme'); ?></label></th>
            <td><textarea id="stt_pain_points" name="stt_pain_points" rows="3" class="large-text"><?php echo esc_textarea($pain_points); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="stt_unique_value"><?php esc_html_e('Unique Value', 'simplifiedtradingtheme'); ?></label></th>
            <td><textarea id="stt_unique_value" name="stt_unique_value" rows="3" class="large-text"><?php echo esc_textarea($unique_value); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="stt_differentiation"><?php esc_html_e('Differentiation', 'simplifiedtradingtheme'); ?></label></th>
            <td><textarea id="stt_differentiation" name="stt_differentiation" rows="3" class="large-text" placeholder="Unlike competitors because..."><?php echo esc_textarea($differentiation); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="stt_site_assignment"><?php esc_html_e('Site Assignment', 'simplifiedtradingtheme'); ?></label></th>
            <td>
                <select id="stt_site_assignment" name="stt_site_assignment">
                    <option value=""><?php esc_html_e('Select Site', 'simplifiedtradingtheme'); ?></option>
                    <option value="freerideinvestor.com" <?php selected($site_assignment, 'freerideinvestor.com'); ?>>freerideinvestor.com</option>
                    <option value="tradingrobotplug.com" <?php selected($site_assignment, 'tradingrobotplug.com'); ?>>tradingrobotplug.com</option>
                    <option value="dadudekc.com" <?php selected($site_assignment, 'dadudekc.com'); ?>>dadudekc.com</option>
                    <option value="crosbyultimateevents.com" <?php selected($site_assignment, 'crosbyultimateevents.com'); ?>>crosbyultimateevents.com</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Render Offer Ladder Meta Box
 *
 * @param WP_Post $post Current post object.
 */
function stt_render_offer_ladder_meta_box($post) {
    wp_nonce_field('stt_save_offer_ladder', 'stt_offer_ladder_nonce');

    $ladder_level = get_post_meta($post->ID, 'ladder_level', true);
    $offer_name = get_post_meta($post->ID, 'offer_name', true);
    $offer_description = get_post_meta($post->ID, 'offer_description', true);
    $price_point = get_post_meta($post->ID, 'price_point', true);
    $cta_text = get_post_meta($post->ID, 'cta_text', true);
    $cta_url = get_post_meta($post->ID, 'cta_url', true);
    $site_assignment = get_post_meta($post->ID, 'site_assignment', true);

    ?>
    <table class="form-table">
        <tr>
            <th><label for="stt_ladder_level"><?php esc_html_e('Ladder Level', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="number" id="stt_ladder_level" name="stt_ladder_level" value="<?php echo esc_attr($ladder_level); ?>" min="1" max="10" /></td>
        </tr>
        <tr>
            <th><label for="stt_offer_name"><?php esc_html_e('Offer Name', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="text" id="stt_offer_name" name="stt_offer_name" value="<?php echo esc_attr($offer_name); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="stt_offer_description"><?php esc_html_e('Offer Description', 'simplifiedtradingtheme'); ?></label></th>
            <td><textarea id="stt_offer_description" name="stt_offer_description" rows="3" class="large-text"><?php echo esc_textarea($offer_description); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="stt_price_point"><?php esc_html_e('Price Point', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="text" id="stt_price_point" name="stt_price_point" value="<?php echo esc_attr($price_point); ?>" class="regular-text" placeholder="Free, $X, or price range" /></td>
        </tr>
        <tr>
            <th><label for="stt_cta_text"><?php esc_html_e('CTA Text', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="text" id="stt_cta_text" name="stt_cta_text" value="<?php echo esc_attr($cta_text); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="stt_cta_url"><?php esc_html_e('CTA URL', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="url" id="stt_cta_url" name="stt_cta_url" value="<?php echo esc_attr($cta_url); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="stt_site_assignment"><?php esc_html_e('Site Assignment', 'simplifiedtradingtheme'); ?></label></th>
            <td>
                <select id="stt_site_assignment" name="stt_site_assignment">
                    <option value=""><?php esc_html_e('Select Site', 'simplifiedtradingtheme'); ?></option>
                    <option value="freerideinvestor.com" <?php selected($site_assignment, 'freerideinvestor.com'); ?>>freerideinvestor.com</option>
                    <option value="tradingrobotplug.com" <?php selected($site_assignment, 'tradingrobotplug.com'); ?>>tradingrobotplug.com</option>
                    <option value="dadudekc.com" <?php selected($site_assignment, 'dadudekc.com'); ?>>dadudekc.com</option>
                    <option value="crosbyultimateevents.com" <?php selected($site_assignment, 'crosbyultimateevents.com'); ?>>crosbyultimateevents.com</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Render ICP Definition Meta Box
 *
 * @param WP_Post $post Current post object.
 */
function stt_render_icp_definition_meta_box($post) {
    wp_nonce_field('stt_save_icp_definition', 'stt_icp_definition_nonce');

    $target_demographic = get_post_meta($post->ID, 'target_demographic', true);
    $pain_points = get_post_meta($post->ID, 'pain_points', true);
    $desired_outcomes = get_post_meta($post->ID, 'desired_outcomes', true);
    $site_assignment = get_post_meta($post->ID, 'site_assignment', true);

    ?>
    <table class="form-table">
        <tr>
            <th><label for="stt_target_demographic"><?php esc_html_e('Target Demographic', 'simplifiedtradingtheme'); ?></label></th>
            <td><input type="text" id="stt_target_demographic" name="stt_target_demographic" value="<?php echo esc_attr($target_demographic); ?>" class="regular-text" placeholder="Who they are (age, income, role)" /></td>
        </tr>
        <tr>
            <th><label for="stt_pain_points"><?php esc_html_e('Pain Points', 'simplifiedtradingtheme'); ?></label></th>
            <td><textarea id="stt_pain_points" name="stt_pain_points" rows="3" class="large-text"><?php echo esc_textarea($pain_points); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="stt_desired_outcomes"><?php esc_html_e('Desired Outcomes', 'simplifiedtradingtheme'); ?></label></th>
            <td><textarea id="stt_desired_outcomes" name="stt_desired_outcomes" rows="3" class="large-text"><?php echo esc_textarea($desired_outcomes); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="stt_site_assignment"><?php esc_html_e('Site Assignment', 'simplifiedtradingtheme'); ?></label></th>
            <td>
                <select id="stt_site_assignment" name="stt_site_assignment">
                    <option value=""><?php esc_html_e('Select Site', 'simplifiedtradingtheme'); ?></option>
                    <option value="freerideinvestor.com" <?php selected($site_assignment, 'freerideinvestor.com'); ?>>freerideinvestor.com</option>
                    <option value="tradingrobotplug.com" <?php selected($site_assignment, 'tradingrobotplug.com'); ?>>tradingrobotplug.com</option>
                    <option value="dadudekc.com" <?php selected($site_assignment, 'dadudekc.com'); ?>>dadudekc.com</option>
                    <option value="crosbyultimateevents.com" <?php selected($site_assignment, 'crosbyultimateevents.com'); ?>>crosbyultimateevents.com</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Positioning Statement Meta Box Data
 *
 * @param int $post_id Post ID.
 */
function stt_save_positioning_statement_meta_box_data($post_id) {
    if (!isset($_POST['stt_positioning_statement_nonce']) || !wp_verify_nonce($_POST['stt_positioning_statement_nonce'], 'stt_save_positioning_statement')) {
        return;
    }
    if (wp_is_post_autosave($post_id) || !current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['target_audience', 'pain_points', 'unique_value', 'differentiation', 'site_assignment'];
    foreach ($fields as $field) {
        if (isset($_POST['stt_' . $field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST['stt_' . $field]));
        }
    }
}
add_action('save_post_positioning_statement', 'stt_save_positioning_statement_meta_box_data');

/**
 * Save Offer Ladder Meta Box Data
 *
 * @param int $post_id Post ID.
 */
function stt_save_offer_ladder_meta_box_data($post_id) {
    if (!isset($_POST['stt_offer_ladder_nonce']) || !wp_verify_nonce($_POST['stt_offer_ladder_nonce'], 'stt_save_offer_ladder')) {
        return;
    }
    if (wp_is_post_autosave($post_id) || !current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['stt_ladder_level'])) {
        update_post_meta($post_id, 'ladder_level', absint($_POST['stt_ladder_level']));
    }
    $fields = ['offer_name', 'offer_description', 'price_point', 'cta_text', 'cta_url', 'site_assignment'];
    foreach ($fields as $field) {
        if (isset($_POST['stt_' . $field])) {
            $value = ($field === 'cta_url') ? esc_url_raw($_POST['stt_' . $field]) : sanitize_text_field($_POST['stt_' . $field]);
            update_post_meta($post_id, $field, $value);
        }
    }
}
add_action('save_post_offer_ladder', 'stt_save_offer_ladder_meta_box_data');

/**
 * Save ICP Definition Meta Box Data
 *
 * @param int $post_id Post ID.
 */
function stt_save_icp_definition_meta_box_data($post_id) {
    if (!isset($_POST['stt_icp_definition_nonce']) || !wp_verify_nonce($_POST['stt_icp_definition_nonce'], 'stt_save_icp_definition')) {
        return;
    }
    if (wp_is_post_autosave($post_id) || !current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['target_demographic', 'pain_points', 'desired_outcomes', 'site_assignment'];
    foreach ($fields as $field) {
        if (isset($_POST['stt_' . $field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST['stt_' . $field]));
        }
    }
}
add_action('save_post_icp_definition', 'stt_save_icp_definition_meta_box_data');

