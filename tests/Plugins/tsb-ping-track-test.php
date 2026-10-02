<?php

use Brain\Monkey\Filters;

test( 'all pings are disabled', function () {
	Filters\expectAdded( 'pings_open' )->once()->with( '__return_false', 10, 1 );
	$this->loadPlugin( 'tsb-ping-track' );

	expect( Filters\has( 'pings_open', '__return_false' ) )->toBe( 10 );
} );
