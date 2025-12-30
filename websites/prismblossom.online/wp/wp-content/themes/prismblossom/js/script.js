/**
 * PrismBlossom theme JavaScript
 *
 * Keep this lightweight: templates include their own scripts,
 * but we still need a real file so WP can enqueue + localize it.
 */

(() => {
  // Fallback in case wp_localize_script isn't printed for some reason.
  window.prismblossomAjax =
    window.prismblossomAjax ||
    ({
      ajaxurl: "/wp-admin/admin-ajax.php",
      nonce: "",
    });

  // No-op for now; reserved for site-wide enhancements.
})();

