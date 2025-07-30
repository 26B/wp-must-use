<?php

/**
 * The plugin bootstrap file
 *
 * A collection of Must-Use plugins for WordPress to use in 26B projects.
 *
 * @copyright Copyright (C) 2024-2025, 26B - IT Consulting <hello@26b.io>
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name:       wp-must-use
 * Description:
 * Version:           0.0.1
 * Author:            26B - IT Consulting
 * Author URI:        https://26b.io/
 * License:           GPL v3
 * Requires at least: 6.7
 * Requires PHP:      8.2.0
 * Text Domain:       wp-must-use
 * Domain Path:       /languages
 */

// Useful global constants.
define( 'TSB_WP_PLUGIN_MUST_USE_VERSION', '0.0.1' );
define( 'TSB_WP_PLUGIN_MUST_USE_URL', plugin_dir_url( __FILE__ ) );
define( 'TSB_WP_PLUGIN_MUST_USE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSB_WP_PLUGIN_MUST_USE_INC', TSB_WP_PLUGIN_MUST_USE_PATH . 'includes/' );
define( 'TSB_WP_PLUGIN_MUST_USE_DIST_URL', TSB_WP_PLUGIN_MUST_USE_URL . 'build/' );
define( 'TSB_WP_PLUGIN_MUST_USE_DIST_PATH', TSB_WP_PLUGIN_MUST_USE_PATH . 'build/' );
define( 'TSB_WP_PLUGIN_MUST_USE_API_NAMESPACE', 'wp-must-use/v1' );

// Include all plugins.
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-author.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-composer-autoload.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-customizer.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-clean-dashboard.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-disable-updates.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-emojis.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-head.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-https.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-lock-site.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-ping-track.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-release-version.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-site-health.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-svg.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-two-factor.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-undangit.php';
require_once TSB_WP_PLUGIN_MUST_USE_PATH . 'plugins/tsb-xml-rpc.php';
