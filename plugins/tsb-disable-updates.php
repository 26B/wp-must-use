<?php
/**
 * @wordpress-plugin
 * Plugin Name: Disable updates
 * Description: Disable any core, plugin and theme automatic updates.
 * Version:     1.1.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

// Disable core updates
add_filter( 'automatic_updater_disabled', '__return_true' );
add_filter( 'allow_dev_auto_core_updates', '__return_false' );
add_filter( 'allow_minor_auto_core_updates', '__return_false' );
add_filter( 'allow_major_auto_core_updates', '__return_false' );

// Disable plugin and theme checks
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

// Disable translation updates
add_filter( 'auto_update_translation', '__return_false' );

// Update core checks from WordPress
remove_action( 'init', 'wp_schedule_update_checks' );
wp_clear_scheduled_hook( 'wp_version_check' );
wp_clear_scheduled_hook( 'wp_update_plugins' );
wp_clear_scheduled_hook( 'wp_update_themes' );
