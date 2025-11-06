<?php
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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          'irs!<?B,[3v-jYKDXm&z+1p|7 C;qzoMQEE_@(ci~s+T~iEoTd62%(^_wfylU`P<' );
define( 'SECURE_AUTH_KEY',   'TY0_D!wL62(pK-R6F ietUp tryBMv4Sgn>tdsq:5WW9SEd[saJz0kmT#fUF0wHC' );
define( 'LOGGED_IN_KEY',     '82)sdQy{oP.%Ulh$EMH>-Ym%]gO,/U:1he6C;A6rz/@&v}g/xh|obSYg?9<TVR*:' );
define( 'NONCE_KEY',         'PCL4Y/ssrOV-WCa7-?hG,,JorF!2bE^I$]1.WgU0o),$k>Bb@Mw@>2$GgAi^]~^6' );
define( 'AUTH_SALT',         'SGj^ya7igI&Jt:Av=*0>^lUgrc6=`^-,f0|@xcw?~&dq%/Xv$20:2UpF]eT~!AGH' );
define( 'SECURE_AUTH_SALT',  'yGj-mzM@)64Jm_DMBHpL3k8(|i:r<*Hn!n{$}3G87Gq.r.y}*t]JOk|dJ3z`Zsh&' );
define( 'LOGGED_IN_SALT',    'Hhewq^}A@6~F*U2}Uxp(0V^MW47lHqfL]G?G=Q:Q,>n|EMYeh8X2Cu1Zu_MT#$Jw' );
define( 'NONCE_SALT',        'I`@9NYkRRlA$udF~rC#rwaJ1qg^WnTaj,@]g1/S},T=|g!uT,4(T bZ(D.x|zHi ' );
define( 'WP_CACHE_KEY_SALT', 'Tz0f=QX<A~Bfy2<ALU b^O,zH);X|mny;a7}.Va9;3l%6swI{B*5m$$k{[omA/i}' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

/* TPB Development Settings */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
	define( 'WP_DEBUG_DISPLAY', false );
}
if ( ! defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', true );
}

/* Increase memory limit and upload limits - Set to values greater than backup size */
ini_set('memory_limit', '2048M');
ini_set('upload_max_filesize', '2048M');
ini_set('post_max_size', '2048M');
ini_set('max_execution_time', 0);  // 0 = infinite
ini_set('max_input_time', -1);     // -1 = infinite
ini_set('max_input_vars', 10000);
ini_set('default_socket_timeout', 600);

/* TPB Modal Settings */
if ( ! defined( 'TPB_QV_DEBUG' ) ) {
	define( 'TPB_QV_DEBUG', true );
}

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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
