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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
 
define( 'WP_HOME', 'http://localhost' );
define( 'WP_SITEURL', 'http://localhost/' );

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "dsju" );

/** Database username */
define( 'DB_USER', "root" );

/** Database password */
define( 'DB_PASSWORD', "root" );

/** Database hostname */
define( 'DB_HOST', "localhost:3306" );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define('AUTH_KEY',         'DU9Jo%OSV_D+9Hi.PhO/+4Y_L?U%Op#m<=2{g0CL`~*V|Y$,Sqo|E V-1G!5IMa ');
define('SECURE_AUTH_KEY',  'R]s|lr-9dC@Z*&b{~c8/ieR:psy?_=tz0blUg0MrZ*8O;:QCXJ6CDTEC#[A~1,R|');
define('LOGGED_IN_KEY',    'uf|n$U(+zLfmfS?X*5E-(Us-+w6oYo8=hN@;%,<P%[bH.@5fI5U(O`oA7Z5kkiJ#');
define('NONCE_KEY',        'oVKsk<#9XhmZmax31gI(sz{X>!0<d,!F/d1P2qm02 %E_q3tnk9:9{=: mUiw`:`');
define('AUTH_SALT',        'pQS40 z-?{!uvqlWK4u,W3.hN2` XFky;.hKrdKQ{4G;-}.k%fDV]%`l@!h~Rpe_');
define('SECURE_AUTH_SALT', 'N3_ 1fNw[BQS*TY$,n!&<5]4znwf^K+m<rc;R@|$!n4f5 B3QuQN2a9E/:1j]&.G');
define('LOGGED_IN_SALT',   'IA90W+3zz.tvT#0TzgfHZ%/P(EH /48u-jYHu:9Fm-,OXWWh>1&-,-#4G(yd7Bjt');
define('NONCE_SALT',       '>iK-h!B;{:z|706smsEB-)p_ KKp3Tm!mQb#KyKd;)6;|jfo-jVIrv+Pg*6o)R`4');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', true);

/* Add any custom values between this line and the "stop editing" line. */



define( 'DUPLICATOR_AUTH_KEY', 'amo4~wO]SK+fIBgs,`XBmO(Qe.EfJcH;7R|:$E,IZL,Av/XVVR|gVPMX*?V$,:lH' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
