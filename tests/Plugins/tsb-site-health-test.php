<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;

test( 'Site Health UI, scheduled check, and screen access are disabled', function () {
	Actions\expectAdded( 'admin_menu' )->once()->withArgs( function ( $callback ) {
		Functions\expect( 'remove_submenu_page' )->once()->with( 'tools.php', 'site-health.php' );
		$callback();
		return true;
	} );
	Actions\expectAdded( 'admin_init' )->once()->withArgs( function ( $callback ) {
		Functions\expect( 'wp_clear_scheduled_hook' )->once()->with( 'wp_site_health_scheduled_check' );
		$callback();
		return true;
	} );
	Actions\expectAdded( 'current_screen' )->once()->withArgs( function ( $callback ) {
		Functions\when( 'get_current_screen' )->justReturn( (object) [ 'id' => 'dashboard' ] );
		$callback();
		return true;
	} );

	$this->loadPlugin( 'tsb-site-health' );

	global $wp_meta_boxes;
	$wp_meta_boxes = [ 'dashboard' => [ 'normal' => [ 'core' => [ 'dashboard_site_health' => true, 'other' => true ] ] ] ];
	TSB\WP\MUPlugin\SiteHealth\wp_dashboard_setup();
	expect( $wp_meta_boxes['dashboard']['normal']['core'] )->toBe( [ 'other' => true ] );
} );

test( 'Site Health screen redirects to the admin dashboard', function () {
	$output = $this->runPhp( <<<'PHP'
require 'vendor/autoload.php';
Brain\Monkey\setUp();
Brain\Monkey\Functions\when( 'get_current_screen' )->justReturn( (object) [ 'id' => 'site-health' ] );
Brain\Monkey\Functions\when( 'admin_url' )->justReturn( 'https://site.test/wp-admin/' );
Brain\Monkey\Functions\when( 'wp_safe_redirect' )->alias( fn ( $url ) => print $url );
Brain\Monkey\Actions\expectAdded( 'current_screen' )->once()->withArgs( function ( $callback ) { $callback(); return true; } );
require 'plugins/tsb-site-health.php';
PHP );

	expect( $output )->toBe( 'https://site.test/wp-admin/' );
} );
