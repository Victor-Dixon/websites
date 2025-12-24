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
define( 'DB_NAME', 'u996867598_S1A5Q' );

/** Database username */
define( 'DB_USER', 'u996867598_8iwdE' );

/** Database password */
define( 'DB_PASSWORD', 'tU0I5x8AmH' );

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
define( 'AUTH_KEY',          'MvN4fOeZ#VtLBaQltT|%zm68oexkiI+=@$JIe|FC^XF0?)1zn]+sD#:Y%EMWOqJl' );
define( 'SECURE_AUTH_KEY',   'Xl&}?oq=Ht@]K[{~bFb{` [gd]SCH%U`vRHodwvIs`n6KT}dU%|`mN!KC8JmnZo~' );
define( 'LOGGED_IN_KEY',     'eh?NAWcuUM-jY4<oVz+u[PguAx?ey^D$(gl;Ow_C%!^E/>sLoIH|kba!8C4`,2,2' );
define( 'NONCE_KEY',         'aQx9>v?Qf+bfx8J125XQ<AE$a& `>YQohODB,!yi1=Z.@r &7@$HjTCJ= vB)EP{' );
define( 'AUTH_SALT',         '0m`V]sj6DDINFjb)z8f{rhniv)VRYN]A[sG2RBRiu1sXXDqfQ<Y/*qch{TsX[~)F' );
define( 'SECURE_AUTH_SALT',  '6U+sWpFlJZF7#GvZL]8HaKJBR7i;2DN3r!rYrZh#C7tRZ8ZjW@-i<S==~k)ot!bg' );
define( 'LOGGED_IN_SALT',    'HJzum%IO}:.+&7SSUmnF9E5tx#z8-rShe3C[LpPN3=F`Dwt9C2Cf08C~Ur_g$A^C' );
define( 'NONCE_SALT',        'lO?V{|fH?NbW)iu)Oh?G<)4#}/uwd tcXu uP.QH.~QyDu+KSbc~>v,-_o+{/[<S' );
define( 'WP_CACHE_KEY_SALT', '3@p.o}58F|yO[o=fDAZf,S@*7]wR9yVURc.u>k^_Lx;Dyvt)N!CX)Fig8tYr/Q}I' );


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
define( 'COOKIEHASH', '39e19782f574a593583cc71dea6b0c94' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


// Enable WordPress debug mode - Added by Agent-7
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
