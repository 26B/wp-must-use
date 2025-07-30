<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package   TSB\WP\Plugin\TSBPluginNamespace
 * @copyright Copyright (C) 2024-2025, 26B - IT Consulting <hello@26b.io>
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name:       tsb-wp-plugin
 * Description:       
 * Version:           0.0.1
 * Author:            26B - IT Consulting
 * Author URI:        https://26b.io/
 * License:           GPL v3
 * Requires at least: 6.7
 * Requires PHP:      8.2.0
 * Text Domain:       tsb-wp-plugin
 * Domain Path:       /languages
 */

// Useful global constants.
define( 'TSB_WP_PLUGIN_VERSION', '0.0.1' );
define( 'TSB_WP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TSB_WP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSB_WP_PLUGIN_INC', TSB_WP_PLUGIN_PATH . 'includes/' );
define( 'TSB_WP_PLUGIN_DIST_URL', TSB_WP_PLUGIN_URL . 'build/' );
define( 'TSB_WP_PLUGIN_DIST_PATH', TSB_WP_PLUGIN_PATH . 'build/' );
define( 'TSB_WP_PLUGIN_API_NAMESPACE', 'tsb-wp-plugin/v1' );

$is_local_env = in_array( wp_get_environment_type(), [ 'local', 'development' ], true );
$is_local_url = strpos( home_url(), '.test' ) || strpos( home_url(), '.local' );
$is_local     = $is_local_env || $is_local_url;

// Require Composer autoloader if it exists.
if ( file_exists( TSB_WP_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	include_once TSB_WP_PLUGIN_PATH . 'vendor/autoload.php';
}

// Include files.
require_once TSB_WP_PLUGIN_INC . '/utility.php';
require_once TSB_WP_PLUGIN_INC . '/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\TSB\WP\Plugin\TSBPluginNamespace\Core\activate' );
register_deactivation_hook( __FILE__, '\TSB\WP\Plugin\TSBPluginNamespace\Core\deactivate' );

// Bootstrap.
TSB\WP\Plugin\TSBPluginNamespace\Core\setup();
