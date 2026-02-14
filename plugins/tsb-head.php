<?php
/**
 * @wordpress-plugin
 * Plugin Name: Clean up the WordPress header output
 * Description: Clean up the WordPress header output.
 * Version:     1.1.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

namespace TSB\WP\MUPlugin\Header;

/**
 * Clean up the WordPress header output.
 * @return void
 */
function clean_head() {
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

	// Remove oEmbed discovery links and JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );

	// Remove REST API link tag from head.
	remove_action( 'wp_head', 'rest_output_link_wp_head' );

	// Disable Custom CSS in the frontend head.
	remove_action( 'wp_head', 'wp_custom_css_cb', 11 );
	remove_action( 'wp_head', 'wp_custom_css_cb', 101 );
}
add_action( 'init', __NAMESPACE__ . '\\clean_head', 10, 2 );

/**
 * Hide WP version strings from generator meta tag.
 * @return string
 */
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Disable the default gallery style.
 * @return bool
 */
add_filter( 'use_default_gallery_style', '__return_false' );
