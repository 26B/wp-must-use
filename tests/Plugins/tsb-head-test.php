<?php

use Brain\Monkey\Actions;

test( 'head cleanup removes default output and disables gallery styling', function () {
	$this->loadPlugin( 'tsb-head' );

	foreach ( [
		[ 'wp_head', 'feed_links_extra', 3 ],
		[ 'wp_head', 'wp_generator', 10 ],
		[ 'wp_head', 'wp_shortlink_wp_head', 10 ],
		[ 'wp_head', 'wlwmanifest_link', 10 ],
		[ 'wp_head', 'rsd_link', 10 ],
		[ 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 ],
		[ 'wp_head', 'wp_oembed_add_discovery_links', 10 ],
		[ 'wp_head', 'wp_oembed_add_host_js', 10 ],
		[ 'wp_head', 'rest_output_link_wp_head', 10 ],
		[ 'wp_head', 'wp_custom_css_cb', 11 ],
		[ 'wp_head', 'wp_custom_css_cb', 101 ],
	] as [ $hook, $callback, $priority ] ) {
		Actions\expectRemoved( $hook )->once()->with( $callback, $priority );
	}
	TSB\WP\MUPlugin\Header\clean_head();

	expect( Actions\has( 'init', 'TSB\\WP\\MUPlugin\\Header\\clean_head', 10 ) )->toBeTrue();
	expect( Brain\Monkey\Filters\has( 'the_generator', '__return_empty_string' ) )->toBe( 10 );
	expect( Brain\Monkey\Filters\has( 'use_default_gallery_style', '__return_false' ) )->toBe( 10 );
} );
