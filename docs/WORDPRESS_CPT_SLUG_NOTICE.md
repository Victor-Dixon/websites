# CPT slug too long notice (register_post_type)

📜 COPY

What this means

You (or a plugin/theme) are calling register_post_type() with a post type slug longer than 20 characters (or empty). WordPress hard-limits CPT slugs to 1–20 chars.

Common causes:
	•	A CPT slug like freerideinvestor_trade_signals (too long)
	•	Passing a label/title instead of a slug
	•	Concatenating strings and ending up with a long slug

⸻

Fastest way to identify the culprit (no code edits)

1) Turn on debug logging (not display)

Edit wp-config.php:

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

Reload the site once, then open:
/wp-content/debug.log

Search for register_post_type or the notice line. Often it’ll show the plugin/theme file that triggered it.

⸻

Surgical tracer (add once, catch offender, remove)

Drop this into wp-content/mu-plugins/cpt-trace.php (create folders if needed):

<?php
/**
 * Plugin Name: CPT Trace (temporary)
 */

add_action('init', function () {
    // nothing here; we only hook the filter below
}, 0);

add_filter('register_post_type_args', function ($args, $post_type) {
    if (!is_string($post_type) || strlen($post_type) < 1 || strlen($post_type) > 20) {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
        $lines = [];
        foreach ($bt as $i => $t) {
            $file = $t['file'] ?? '';
            $line = $t['line'] ?? '';
            if ($file) $lines[] = "#$i $file:$line";
        }

        error_log("CPT INVALID SLUG: '{$post_type}' len=" . (is_string($post_type) ? strlen($post_type) : -1));
        error_log("BACKTRACE:\n" . implode("\n", $lines));
    }
    return $args;
}, 10, 2);

Reload the site → check wp-content/debug.log.
You’ll see:
	•	the invalid slug
	•	the exact file/line that registered it

Then delete mu-plugins/cpt-trace.php.

⸻

Fix once you find it

Wherever you see something like:

register_post_type('freerideinvestor_trade_signals', $args);

Change to a <=20 char slug, e.g.:

register_post_type('tbow_signals', $args); // 11 chars

And if you need a nice name, that goes in labels:

$args['labels']['name'] = 'TBOW Signals';


⸻

Quick sanity list (valid CPT slugs)

✅ tbow_signals
✅ trades
✅ signals
❌ freerideinvestor_trading_signals (too long)

⸻

If you paste the CPT registration line (or the backtrace log snippet), I’ll tell you the exact 5-second fix.
