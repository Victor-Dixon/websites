<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_Cache_Manager {

    /**
     * Retrieve cached data
     *
     * @param string $key The cache key.
     * @return mixed|false Cached data or false if the cache is expired or not found.
     */
    public static function get($key) {
        return get_transient($key);
    }

    /**
     * Store data in cache
     *
     * @param string $key The cache key.
     * @param mixed $data The data to store.
     * @param int $expiration Expiration time in seconds.
     * @return bool True if the data was successfully cached, false otherwise.
     */
    public static function set($key, $data, $expiration = HOUR_IN_SECONDS) {
        return set_transient($key, $data, $expiration);
    }

    /**
     * Delete a specific cache entry
     *
     * @param string $key The cache key.
     * @return bool True if the cache was successfully deleted, false otherwise.
     */
    public static function delete($key) {
        return delete_transient($key);
    }

    /**
     * Clear all plugin-related cache
     *
     * @param string $prefix Optional. Clear only keys with this prefix.
     */
    public static function clear($prefix = 'fri_') {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                $wpdb->esc_like("_transient_$prefix") . '%',
                $wpdb->esc_like("_transient_timeout_$prefix") . '%'
            )
        );
    }

    /**
     * Fallback for persistent caching in the database
     *
     * @param string $key The cache key.
     * @return mixed|false Cached data from the database or false if not found.
     */
    public static function get_persistent($key) {
        global $wpdb;
        $table = $wpdb->prefix . 'fri_cache';
        $row = $wpdb->get_row($wpdb->prepare("SELECT data FROM $table WHERE cache_key = %s", $key));

        return $row ? maybe_unserialize($row->data) : false;
    }

    /**
     * Store data in the persistent cache
     *
     * @param string $key The cache key.
     * @param mixed $data The data to store.
     * @param int $expiration Expiration time in seconds.
     */
    public static function set_persistent($key, $data, $expiration = 3600) {
        global $wpdb;
        $table = $wpdb->prefix . 'fri_cache';

        $wpdb->replace(
            $table,
            [
                'cache_key'   => $key,
                'data'        => maybe_serialize($data),
                'expiration'  => time() + $expiration,
            ],
            ['%s', '%s', '%d']
        );
    }

    /**
     * Create the persistent cache table
     */
    public static function create_cache_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'fri_cache';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            cache_key VARCHAR(255) PRIMARY KEY,
            data LONGTEXT NOT NULL,
            expiration BIGINT NOT NULL
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Delete expired entries from the persistent cache
     */
    public static function clean_expired() {
        global $wpdb;
        $table = $wpdb->prefix . 'fri_cache';

        $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE expiration < %d", time()));
    }
}
