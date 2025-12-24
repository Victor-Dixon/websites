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
define( 'DB_NAME', 'u996867598_6cbPB' );

/** Database username */
define( 'DB_USER', 'u996867598_9dVzt' );

/** Database password */
define( 'DB_PASSWORD', '3aZq7XTxA6' );

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
define( 'AUTH_KEY',          '(KwCj+aZf6}GJZr0heL=1{+k,-7BhB-|B3]v7[kut=wj?hg-.^kdgMZp,!gZ!Rh]' );
define( 'SECURE_AUTH_KEY',   't,BUgr0xMpWEGZQF)*.7eugUBgE~DtBKZY:x(?pad#Ny3U2-OMU:JRYZi>GV5:Ln' );
define( 'LOGGED_IN_KEY',     '2mZQT0F;ZM75y/!XV*|6rhy$7W]U.MSo(?}J/IF?[[aTchY::emyTMY{[+E#04/D' );
define( 'NONCE_KEY',         'UU9+En/N1WuC9F-n@)//NR}+bOmA.Kdbqz*Y]h[2=`|%YQIh`q/O5zR|74!}YA&8' );
define( 'AUTH_SALT',         '|JbLG=Yj3}-.{Ng`/,tBQEb/k4z9-+c,Fk%Yt5^p]YVvEk(8xW,||e$!@d %ShjY' );
define( 'SECURE_AUTH_SALT',  'q _#:+9!.r<Y:%}c,,>N:=2~i7}:eVG~uiK[)ZmBbYDMvuFJ|9_Ug8=Jp%GXkeW_' );
define( 'LOGGED_IN_SALT',    'W:J**~avxn^u6<<. !jau<<MS6NEJ.HE18}UDzl~jAyj<AR%S.Spc^0AV2]L1C~m' );
define( 'NONCE_SALT',        '4Lah17P.l+N{z(z&# ~JHKlzDJ{UBnA:#K8,Yb*] Q2N>CO^[}#r]6Y$Bx}TXV1U' );
define( 'WP_CACHE_KEY_SALT', '%`]P7>E?h LPX]sv=8*?oCs=Wye9Z5.pm=%}ku|t_]KdB=rLQZ Te`C!;1:bCp.9' );


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
define( 'COOKIEHASH', '4d22f306a86667e9d349ccd82dfc67ab' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );

// Enable WordPress debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_MEMORY_LIMIT', '256M');
@ini_set('display_errors', 0);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
