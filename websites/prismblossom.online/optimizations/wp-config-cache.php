// WordPress Performance Optimization - Added by Agent-7
// Enable WordPress object cache
define('WP_CACHE', true);

// Increase memory limits
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// Disable file editing for security
define('DISALLOW_FILE_EDIT', true);

// Optimize database queries
define('WP_POST_REVISIONS', 3);
define('AUTOSAVE_INTERVAL', 300);

// Enable compression
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('ENFORCE_GZIP', true);
