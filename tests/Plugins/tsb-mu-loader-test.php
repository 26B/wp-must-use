<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;

test( 'MU loader reuses valid cache and rebuilds stale cache from nested plugins', function () {
	$this->loadPlugin( 'tsb-mu-loader' );

	$cached = [ 'group/plugin.php' => [ 'Name' => 'Cached' ] ];
	Functions\when( 'get_site_transient' )->alias( function () use ( &$cached ) {
		return $cached;
	} );
	expect( TSB\WP\MUPlugin\Loader\get_mu_plugins() )->toBe( $cached );

	$cached = [ 'missing/plugin.php' => [ 'Name' => 'Stale' ] ];
	$saved = null;
	Functions\when( 'get_plugins' )->justReturn( [
		'root.php'             => [],
		'group/plugin.php'     => [],
		'another/nested.php'   => [],
	] );
	Functions\when( 'get_plugin_data' )->alias( fn ( $path ) => [ 'Name' => basename( dirname( $path ) ) ] );
	Functions\when( 'set_site_transient' )->alias( function ( $key, $value ) use ( &$saved ) {
		$saved = $value;
	} );

	$plugins = TSB\WP\MUPlugin\Loader\get_mu_plugins();
	expect( array_keys( $plugins ) )->toBe( [ 'group/plugin.php', 'another/nested.php' ] );
	expect( $saved )->toBe( $plugins );
	expect( Actions\has( 'muplugins_loaded' ) )->toBeTrue();
} );
