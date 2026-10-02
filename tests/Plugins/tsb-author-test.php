<?php

use Brain\Monkey\Functions;

test( 'author archives return 404 and other requests pass through', function () {
	$this->loadPlugin( 'tsb-author' );

	global $wp_query;
	$wp_query = Mockery::mock();
	$wp_query->shouldReceive( 'set_404' )->once();

	Functions\when( 'is_author' )->justReturn( true );
	Functions\expect( 'status_header' )->once()->with( 404 );
	TSB\WP\MUPlugin\Author\disable_author_archives();

	Functions\when( 'is_author' )->justReturn( false );
	TSB\WP\MUPlugin\Author\disable_author_archives();

	expect( Brain\Monkey\Actions\has( 'template_redirect', 'TSB\\WP\\MUPlugin\\Author\\disable_author_archives', 0 ) )->toBeTrue();
} );
