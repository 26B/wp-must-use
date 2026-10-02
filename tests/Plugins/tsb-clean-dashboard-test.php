<?php

use Brain\Monkey\Actions;

test( 'dashboard cleanup removes the welcome panel and default widgets', function () {
	$this->loadPlugin( 'tsb-clean-dashboard' );

	global $wp_meta_boxes;
	$wp_meta_boxes = [
		'dashboard' => [
			'normal' => [ 'core' => array_fill_keys( [ 'dashboard_activity', 'dashboard_incoming_links', 'dashboard_plugins', 'dashboard_recent_comments', 'dashboard_right_now' ], true ) ],
			'side'   => [ 'core' => array_fill_keys( [ 'dashboard_primary', 'dashboard_quick_press', 'dashboard_recent_drafts', 'dashboard_secondary' ], true ) ],
		],
	];

	Actions\expectRemoved( 'welcome_panel' )->once()->with( 'wp_welcome_panel' );
	TSB\WP\MUPlugin\Dashboard\wp_dashboard_setup();

	expect( $wp_meta_boxes['dashboard']['normal']['core'] )->toBeEmpty();
	expect( $wp_meta_boxes['dashboard']['side']['core'] )->toBeEmpty();
	expect( Brain\Monkey\Actions\has( 'wp_dashboard_setup', 'TSB\\WP\\MUPlugin\\Dashboard\\wp_dashboard_setup' ) )->toBe( 10 );
} );
