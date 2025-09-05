<?php
/**
 * @wordpress-plugin
 * Plugin Name: Disable Site Health
 * Description: Remove all related features for Site Health.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

namespace TSB\WP\MUPlugin\SiteHealth;

/**
 * Disable the widget.
 *
 * @since  1.0.0
 * @return void
 */
function wp_dashboard_setup() {
	global $wp_meta_boxes;

	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_site_health'] );
}
add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\wp_dashboard_setup' );

/**
 * Remove Tools Submenu Item for Site Health.
 *
 * @since  1.0.0
 * @return void
 */
add_action( 'admin_menu', function () {
	remove_submenu_page( 'tools.php', 'site-health.php' );
} );

/**
 * Prevent Site Health access.
 *
 * @since  1.0.0
 * @return void
 */
add_action( 'current_screen', function () {
	$screen = get_current_screen();
	if ( 'site-health' === $screen->id ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
} );

/**
 * Remove the Site Health scheduled check.
 *
 * @since  1.0.0
 * @return void
 */
add_action( 'admin_init', function () {
	wp_clear_scheduled_hook( 'wp_site_health_scheduled_check' );
});
