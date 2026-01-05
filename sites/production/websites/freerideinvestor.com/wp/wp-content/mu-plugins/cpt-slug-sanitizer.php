<?php
/**
 * Plugin Name: CPT Slug Sanitizer (FreeRideInvestor)
 * Description: Ensures custom post type slugs are 1–20 characters before registration.
 */

function fri_sanitize_cpt_slug(string $slug, string $fallback = ''): string {
    $slug = sanitize_key($slug);
    if ($slug === '' && $fallback !== '') {
        $slug = sanitize_key($fallback);
    }
    if ($slug === '') {
        return '';
    }
    if (strlen($slug) > 20) {
        $slug = substr($slug, 0, 20);
    }
    return $slug;
}

function fri_log_cpt_slug_change(string $source, string $old_slug, string $new_slug): void {
    if ($old_slug === $new_slug) {
        return;
    }
    error_log(sprintf(
        '[fri-cpt] %s slug adjusted: "%s" -> "%s"',
        $source,
        $old_slug,
        $new_slug
    ));
}

function fri_filter_cptui_post_types($post_types) {
    if (!is_array($post_types)) {
        return $post_types;
    }

    $sanitized = [];
    foreach ($post_types as $slug => $settings) {
        $fallback = '';
        if (is_array($settings)) {
            $fallback = $settings['label'] ?? ($settings['singular_label'] ?? '');
        }
        $new_slug = fri_sanitize_cpt_slug((string) $slug, (string) $fallback);
        if ($new_slug === '') {
            fri_log_cpt_slug_change('cptui', (string) $slug, '[skipped]');
            continue;
        }
        if (is_array($settings)) {
            $settings['name'] = $new_slug;
        }
        fri_log_cpt_slug_change('cptui', (string) $slug, $new_slug);
        $sanitized[$new_slug] = $settings;
    }

    return $sanitized;
}
add_filter('option_cptui_post_types', 'fri_filter_cptui_post_types', 1);

function fri_filter_acf_post_types($post_types) {
    if (!is_array($post_types)) {
        return $post_types;
    }

    foreach ($post_types as $index => $settings) {
        if (!is_array($settings)) {
            continue;
        }
        $old_slug = (string) ($settings['post_type'] ?? '');
        $fallback = (string) ($settings['label'] ?? ($settings['plural_label'] ?? ''));
        $new_slug = fri_sanitize_cpt_slug($old_slug, $fallback);
        if ($new_slug === '') {
            fri_log_cpt_slug_change('acf', $old_slug, '[skipped]');
            continue;
        }
        if ($new_slug !== $old_slug) {
            $post_types[$index]['post_type'] = $new_slug;
            fri_log_cpt_slug_change('acf', $old_slug, $new_slug);
        }
    }

    return $post_types;
}
add_filter('option_acf_post_types', 'fri_filter_acf_post_types', 1);
