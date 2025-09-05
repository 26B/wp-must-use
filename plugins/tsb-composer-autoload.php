<?php
/**
 * @wordpress-plugin
 * Plugin Name: Autoload Composer dependencies
 * Description: Autoloads Composer dependencies at project root.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

/**
 * Check if the composer autoload file exists in one of 3 places and require it.
 * 1. The WordPress root
 * 2. One folder above the WordPress root (sub-folder installs)
 * 3. Inside the `wp-content` folder
 */
if ( file_exists( ABSPATH . '/vendor/autoload.php' ) ) {
	require_once ABSPATH . '/vendor/autoload.php';

} else if ( file_exists( ABSPATH . '../vendor/autoload.php' ) ) {
	require_once ABSPATH . '../vendor/autoload.php';

} else if ( file_exists( ABSPATH . '/wp-content/vendor/autoload.php' ) ) {
	require_once ABSPATH . '/wp-content/vendor/autoload.php';
}

