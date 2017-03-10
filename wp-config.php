<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'jambacgr_test');

/** MySQL database username */
define('DB_USER', 'jambacgr_test');

/** MySQL database password */
define('DB_PASSWORD', '!Test2015@#$');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '~j#:*5l%7rsGUNDyZo}1MGGzrZ6KA{ ~eT_6Fe(jMr%lP3YvOmr0=vP._AL&W1,f');
define('SECURE_AUTH_KEY',  '.Ajvpr.E*YU9..eW*p:K/+NIf55u//HFCHzy9*SzvrT7WWwW(6%la@`qFqlDTYht');
define('LOGGED_IN_KEY',    'z^g]8Y[6tk&k*LM|/R(FQ:hYq!.8[YQggLJ^m>tur!`,<#_zh&vgA*,jU{ I?(hX');
define('NONCE_KEY',        '0YSeLsO.H5Pkhn4KM@W0ac>pj+M+|TYjcJ9V3 ZbGXEc&d7Mc+Rp,aM(]tXTSD-d');
define('AUTH_SALT',        '{MY8{L&1FmX+, K%CR}yu3ZPGaJ~~>$?6A5l[<*]Jg}{</z*xr(lD<8C u&mwRX}');
define('SECURE_AUTH_SALT', '*e6d?pBb^M:&W:i*RO-ZqbIgGD)~Y=JVpK<olT]Zi##FvQa4*c;H6|n3yD3(#VUf');
define('LOGGED_IN_SALT',   'oz -sPj]@;,:)i*|uRZOsdiF^vp#F~2)J{uQd6c`_16BBBA[j#X;@eDuN=CfkzpG');
define('NONCE_SALT',       '0;sccu,{fj-RlqyLG7SB *`mN_rev?#Q,]32U!D7/7sI5Wo%s=CqxPwFppLd&],-');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
