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
define( 'WP_MEMORY_LIMIT', '128M' );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'i5224863_wp1');

/** MySQL database username */
define('DB_USER', 'i5224863_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'G.ann37fZvlKs6BCMqy46');

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
define('AUTH_KEY',         'Q4UzcLEsKXNrkNy65sT6bTOgyv0fEbIxaSe4zLOOKa9IHV9KWc9VZHkFBZD4XPZY');
define('SECURE_AUTH_KEY',  '9RoIa3wiG2phFbCu5GzFHOuMYAtV96Cm05jtHoTu9KBiBm8VQVAK00wWYP2U63Gn');
define('LOGGED_IN_KEY',    'OI11Oc7kNnxYIeg0wRHyFReewJ1qM7TEeJtTkdnukHM47SeNlGOwxmT0vPlhiFjE');
define('NONCE_KEY',        'HlevCbWsm3knRkaxtBqbvgJW7SYkfS6GuX6x88meMHLXchTdYKsxFDTEHMTE5Kc9');
define('AUTH_SALT',        'Eze1oTVBo3Www8v7lHw22imGHdo57j2suVyXqFgVGMdZUABpML0KbcqVOXtVdWp3');
define('SECURE_AUTH_SALT', 'dJSNgCa2eQ15fK3nKlwh7v9Ct0tpVPszJnUw8XheWRldcXxJM15LUSY8xXLtH8QT');
define('LOGGED_IN_SALT',   'FB4tH7gDE64pjntjWs9rqQojomZdonTnhvyQJf7DGQzPCvMUW5DX32DysNRywmrc');
define('NONCE_SALT',       'CF2n5MPkkkUSpaAfoMksLBVFqg118W8NWIhcnmLA6tmmOmjG9pMQT8QN6eZJ1dvJ');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


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