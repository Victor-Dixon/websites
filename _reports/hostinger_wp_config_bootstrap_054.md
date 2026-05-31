# Hostinger WP Config Bootstrap Diagnostics 054

## Target

- domain: `freerideinvestor.com`
- wp_root: `/home/u996867598/domains/freerideinvestor.com/public_html`

## Diagnostics
```text

== WP CONFIG EXISTS ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && pwd && ls -la wp-config.php && file wp-config.php
/home/u996867598/domains/freerideinvestor.com/public_html
-rw-r--r-- 1 u996867598 o1008028115 3663 May 28 10:57 wp-config.php
wp-config.php: PHP script, ASCII text
RC=0

== WP CONFIG PHP LINT ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && /opt/alt/php82/usr/bin/php -l wp-config.php
Errors parsing wp-config.php
RC=255

== WP CONFIG FIRST 120 LINES REDACTED ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && sed -n '1,120p' wp-config.php | sed -E "s/(DB_NAME|DB_USER|DB_PASSWORD|DB_HOST|AUTH_KEY|SECURE_AUTH_KEY|LOGGED_IN_KEY|NONCE_KEY|AUTH_SALT|SECURE_AUTH_SALT|LOGGED_IN_SALT|NONCE_SALT)', *'[^']*'/\1', 'REDACTED'/g"
<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'REDACTED' );

/** Database username */
define( 'DB_USER', 'REDACTED' );

/** Database password */
define( 'DB_PASSWORD', 'REDACTED' );

/** Database hostname */
define( 'DB_HOST', 'REDACTED' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', 'REDACTED' );
define( 'SECURE_AUTH_KEY', 'REDACTED' );
define( 'LOGGED_IN_KEY', 'REDACTED' );
define( 'NONCE_KEY', 'REDACTED' );
define( 'AUTH_SALT', 'REDACTED' );
define( 'SECURE_AUTH_SALT', 'REDACTED' );
define( 'LOGGED_IN_SALT', 'REDACTED' );
define( 'NONCE_SALT', 'REDACTED' );
define( 'WP_CACHE_KEY_SALT', 'WQt&b~MR.F`Rnf&<{<^cg1WmO5x2P,RkkgX=|T=l&QF4K<<PJ#S[kq]_rT>lZD5@' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '4d22f306a86667e9d349ccd82dfc67ab' );
define( 'WP_AUTO_UPDATE_CORE', true );

// Debug policy (ops/harden_wp_debug_config.py)
if ( ! defined('WP_DEBUG') ) define('WP_DEBUG', false);
if ( ! defined('WP_DEBUG_LOG') ) define('WP_DEBUG_LOG', false);
if ( ! defined('WP_DEBUG_DISPLAY') ) define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
RC=0

== WP CONFIG CONTROL CHARS ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && LC_ALL=C sed -n l wp-config.php | sed -n '1,80p'
<?php$
define( 'WP_CACHE', true );$
$
/**$
 * The base configuration for WordPress$
 *$
 * The wp-config.php creation script uses this file during the instal\
lation.$
 * You don't have to use the web site, you can copy this file to "wp-\
config.php"$
 * and fill in the values.$
 *$
 * This file contains the following configurations:$
 *$
 * * Database settings$
 * * Secret keys$
 * * Database table prefix$
 * * Localized language$
 * * ABSPATH$
 *$
 * @link https://wordpress.org/support/article/editing-wp-config-php/$
 *$
 * @package WordPress$
 */$
$
// ** Database settings - You can get this info from your web host **\
 //$
/** The name of the database for WordPress */$
define( 'DB_NAME', 'u996867598_md3bQ' );$
$
/** Database username */$
define( 'DB_USER', 'u996867598_V7qQ1' );$
$
/** Database password */$
define( 'DB_PASSWORD', 'HbNiagHhtq' );$
$
/** Database hostname */$
define( 'DB_HOST', '127.0.0.1' );$
$
/** Database charset to use in creating database tables. */$
define( 'DB_CHARSET', 'utf8' );$
$
/** The database collate type. Don't change this if in doubt. */$
define( 'DB_COLLATE', '' );$
$
/**#@+$
 * Authentication unique keys and salts.$
 *$
 * Change these to different unique phrases! You can generate these u\
sing$
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPres\
s.org secret-key service}.$
 *$
 * You can change these at any point in time to invalidate all existi\
ng cookies.$
 * This will force all users to have to log in again.$
 *$
 * @since 2.6.0$
 */$
define( 'AUTH_KEY',          'eZtl:rG+8B{:Q#4l~I%&GFk7loUM&>sy d1DQ8=\
}r-CWQ8d6x)_SspP8.BeSn9dQ' );$
define( 'SECURE_AUTH_KEY',   'z98Y8;Q%c+w+;_/eU95/;U*T<51*p7^olow{KG9\
yk)K3?+1bZpGcx={6Mz;Sfgjo' );$
define( 'LOGGED_IN_KEY',     'KOSqUrb7Z^}V>L(:nj$|6h9pv-nf8^;):4dPHcA\
OP/~Y(4Lzz.:)T&K;3h6<1SRb' );$
define( 'NONCE_KEY',         '<Iqo|5vCq#p*uefxm,$eJ8HCKHcwi68E6.{Zw#2\
Lj.Dh.RRZ@6aK,^wAxzimB8Zo' );$
define( 'AUTH_SALT',         '<.rD#:zBhVf<Mn61<O$VcXC|j)6!5XVNnJy+,>.\
xI;p~?6k<n?pMVO,WV|f{5w&i' );$
define( 'SECURE_AUTH_SALT',  '3`/TyqwuNN18v[l{SSfV7<.mzv}&eXqD=DYoL?I\
m=rcKtmU;:`F e16XR>q>#bVT' );$
define( 'LOGGED_IN_SALT',    '#VeUGr]2m|M1vpzahUk3yi$xf4/JT*XUR*O48}w\
]4Z4H&RJ1)&A5F#YRtCBB#{=e' );$
define( 'NONCE_SALT',        '`~[,&}!8`07ne^oNgMM*&_M /u-EGn@se]F~>|?\
1OYwr+nt65nrM9&Z<<W-Z[JE;' );$
define( 'WP_CACHE_KEY_SALT', 'WQt&b~MR.F`Rnf&<{<^cg1WmO5x2P,RkkgX=|T=\
l&QF4K<<PJ#S[kq]_rT>lZD5@' );$
$
$
/**#@-*/$
RC=0

== DIRECT PHP LOAD WP CONFIG ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && /opt/alt/php82/usr/bin/php -d display_errors=1 -d error_reporting=E_ALL -r 'define("ABSPATH", getcwd()."/"); require "wp-config.php"; echo "WP_CONFIG_LOAD=PASS\n";'

Parse error: Unmatched '}' in /home/u996867598/domains/freerideinvestor.com/public_html/wp-config.php on line 91
RC=255

== PHP LOAD WORDPRESS SETTINGS ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && /opt/alt/php82/usr/bin/php -d display_errors=1 -d error_reporting=E_ALL -r 'define("WP_USE_THEMES", false); require "wp-load.php"; echo "WP_LOAD=PASS\n";'

Parse error: Unmatched '}' in /home/u996867598/domains/freerideinvestor.com/public_html/wp-config.php on line 91
RC=255

== WP CLI WITH PHP ERROR DISPLAY ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && WP_CLI_PHP_ARGS='-d display_errors=1 -d error_reporting=E_ALL' wp option get siteurl --skip-plugins --skip-themes --debug
RC=255
```

## Status

STATUS=DIAGNOSTIC_COMPLETE
