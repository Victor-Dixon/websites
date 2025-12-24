<?php

define( 'ITSEC_ENCRYPTION_KEY', 'WTguVDRISk5ePXF5NGFVQktiY0RHJjIvO0g8P2Q6N3BHQ3RqITlyc19EUSxaWE9GRnp9NCZ1KEhAMVUvN3AxKA==' );

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
define( 'DB_NAME', 'u996867598_vh2Yg' );

/** Database username */
define( 'DB_USER', 'u996867598_KFf6G' );

/** Database password */
define( 'DB_PASSWORD', 'tCqiZyJgMX' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'H~0JQl:3M= u:8c^=*[@=|_x-QN,G&( zGP?G c+?@q6wv-`c{3TeO?kG7IvX5`J' );
define( 'SECURE_AUTH_KEY',   '7isr9Q1nuE!Xla<<X]Xg0JW`1uNQV@%E)TUuh2|=/0Nb_[8Ptdh[I/1CO}WK]Np=' );
define( 'LOGGED_IN_KEY',     ';lEX4ti,Id<~EODh?2[F0//00M)^d6Dgo{CnCL.)QvYlA7GgcA2E$$Ql5Ma+5wXw' );
define( 'NONCE_KEY',         'oiTl]3ibg[J^j^./Y[V#-1#|C1[EY;Rrok{#r*n0ahCRzG5f<HZhqC=d;hF)p=1%' );
define( 'AUTH_SALT',         'p5>d#UI+6(~5Y8MRWgZna:X|4u*juG-%W`:Eo`I9xlBL+(JNYNeC({`e~)/ mU{D' );
define( 'SECURE_AUTH_SALT',  '$;:6dx^erP)ykq::#v# $Ws*JKu~-Y61[8,`^QfxIFDO{!Tr$_BSpF8JQLjhmWky' );
define( 'LOGGED_IN_SALT',    ')|uL$Kn^lojm<8jmL>pC71sM+B&;iWNW<2~Px^R;5,D|y};s2g/aFV49Xp bwRxa' );
define( 'NONCE_SALT',        'b?[HfTns6%wiz)RIqy3:6VKY9uH)mSn)![<JrKB20[89R`{qVq2UizTK8tQN98W0' );
define( 'WP_CACHE_KEY_SALT', 'k)n)J(I;627Y*Yiw#-F-3r4nd#&%9zT~{|cH*Iye5D4mZL/Mdf2/|*euqf?k;+QU' );


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
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '59965d7997ff44e16666b63f95701db8' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
