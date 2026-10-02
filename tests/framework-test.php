<?php

use Brain\Monkey\Functions;

test( 'Brain Monkey can stub WordPress functions', function () {
	Functions\when( 'is_admin' )->justReturn( true );

	expect( is_admin() )->toBeTrue();
} );
