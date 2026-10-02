<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

test( 'automatic updates, notices, and update schedules are disabled', function () {
	Actions\expectRemoved( 'init' )->once()->with( 'wp_schedule_update_checks' );
	$cleared = [];
	Functions\expect( 'wp_clear_scheduled_hook' )->times( 3 )->withArgs( function ( $hook ) use ( &$cleared ) {
		$cleared[] = $hook;
		return in_array( $hook, [ 'wp_version_check', 'wp_update_plugins', 'wp_update_themes' ], true );
	} );

	Actions\expectAdded( 'admin_menu' )->once()->withArgs( function ( $callback ) {
		Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
		Actions\expectRemoved( 'admin_notices' )->once()->with( 'update_nag', 3 );
		$callback();
		return true;
	} );

	$this->loadPlugin( 'tsb-disable-updates' );

	foreach (	[
		'automatic_updater_disabled'      => '__return_true',
		'allow_dev_auto_core_updates'    => '__return_false',
		'allow_minor_auto_core_updates'  => '__return_false',
		'allow_major_auto_core_updates'  => '__return_false',
		'auto_core_update_send_email'    => '__return_false',
		'auto_plugin_update_send_email'  => '__return_false',
		'auto_theme_update_send_email'   => '__return_false',
		'auto_update_plugin'             => '__return_false',
		'auto_update_theme'              => '__return_false',
		'auto_update_translation'        => '__return_false',
	] as $filter => $callback ) {
		expect( Filters\has( $filter, $callback ) )->toBe( 10 );
	}
	expect( $cleared )->toBe( [ 'wp_version_check', 'wp_update_plugins', 'wp_update_themes' ] );
} );
