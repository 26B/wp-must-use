<?php

/**
 * @wordpress-plugin
 * Plugin Name: TSB Undangit
 * Description: Remove capital_P_dangit from the WordPress filter.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-2.0+
 */

add_action(
	'init',
	function () {
		global $wp_filter;
		$filters = [ 'the_content', 'the_title', 'wp_title', 'document_title', 'comment_text', 'widget_text_content', 'acf_the_content' ];
		foreach ( $filters as $filter ) {
			if ( isset( $wp_filter[ $filter ] ) ) {
				foreach ( $wp_filter[ $filter ]->callbacks as $priority => $callbacks ) {
					foreach ( $callbacks as $index => $callback ) {
						if ( $index === 'capital_P_dangit' ) {
							unset( $wp_filter[ $filter ]->callbacks[ $priority ][ $index ] );
						}
					}
				}
			}
		}
	},
	PHP_INT_MAX
);
