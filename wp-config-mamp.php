<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'my-wp-blog');

/** MySQL database username */
define('DB_USER', 'my-wp-blog');

/** MySQL database password */
define('DB_PASSWORD', 'changeme');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'T[y9D3_xW:&6DU+uj-rV9&[5y|pEL`=T:{Q2O7SxHM%^4P8Qk^Q[HsaFT-[PAYb:');
define('SECURE_AUTH_KEY',  '-C1}p+f1id$a(A-O^LD8}yvnJ( Z5AG*0^z&9e^0~!k}hUk%q-?Ki)TDNN VLI3u');
define('LOGGED_IN_KEY',    '|*yZxbt0=Tov{hcRx6km&}XLMEmF9;#:]X_8hz8||+.)T%xx|EWi+-m9#@[J$vw?');
define('NONCE_KEY',        '0E(/l,bilP{QY9|klo}W/hK !F4K^&3`s3BeMcEeU{ACu~hlK0oK3pQ]5C#X[TzM');
define('AUTH_SALT',        'o4Sy`3Tm+2+~M90HV= )B^9M(:BRVwd<Wj,M*ydB2)Wi=~9q@eX/Jww~!~z7mX2n');
define('SECURE_AUTH_SALT', 'i-56..d:%l8;l(_-I /bbdzvBAk9gO.!Z!khY)#[-||BZ%guzmUm+OH@h(qKn)zf');
define('LOGGED_IN_SALT',   '!B/3x$-}5#{@a?J!!&$z$UQ{Y-:+Rc6Cz@[`4OMqFsNjwI2F3/V-lxsG.8jbiV{Z');
define('NONCE_SALT',       'O,zMj[(#S@4F%[mDmPHZ+%BWhZ0;3|I<z/)K(hwAnC8 7p68 |q(q%1Nj1<RO+)T');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
