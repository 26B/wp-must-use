<?php

test( 'revision limit defaults to ten', function () {
	$this->loadPlugin( 'tsb-revisions' );

	expect( WP_POST_REVISIONS )->toBe( 10 );
} );
