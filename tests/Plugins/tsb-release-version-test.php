<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;

test( 'release version is defined from the cached value', function () {
	define( 'MINUTE_IN_SECONDS', 60 );
	Functions\when( 'get_site_transient' )->justReturn( 'release-2026.10' );

	Actions\expectAdded( 'plugins_loaded' )->once()->withArgs( function ( $callback, $priority ) {
		$callback();
		return 1 === $priority;
	} );

	$this->loadPlugin( 'tsb-release-version' );
	expect( RELEASE_VERSION )->toBe( 'release-2026.10' );
} );
