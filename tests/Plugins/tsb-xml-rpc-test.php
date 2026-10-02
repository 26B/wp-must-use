<?php

use Brain\Monkey\Filters;

test( 'XML-RPC is disabled', function () {
	Filters\expectAdded( 'xmlrpc_enabled' )->once()->with( '__return_false', 10, 1 );
	$this->loadPlugin( 'tsb-xml-rpc' );

	expect( Filters\has( 'xmlrpc_enabled', '__return_false' ) )->toBe( 10 );
} );
