<?php
/**
 * @wordpress-plugin
 * Plugin Name: Disable comments
 * Description: Hide or remove commenting feature.
 * Version:     Unreleased
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-2.0+
 */

namespace TSB\WP\MUPlugin\Customizer;

add_filter( 'comments_open', '__return_false' );

/**
 * Remove comments menu page in admin.
 *
 * @return void
 */
add_action(
	'admin_menu',
	function () : void {
		if ( ! is_admin() ) {
			return;
		}
		remove_menu_page( 'edit-comments.php' );
	}
);


/**
 * Disable the support for comments in every post_type.
 *
 * @return void
 */
add_action(
	'init',
	function () : void {
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
			}
		}
	}
);
