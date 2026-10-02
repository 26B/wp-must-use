<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

test( 'comments are disabled in content, admin, and post type support', function () {
	Filters\expectAdded( 'comments_open' )->once()->with( '__return_false', 10, 1 );

	Actions\expectAdded( 'admin_menu' )->once()->withArgs( function ( $callback ) {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\expect( 'remove_menu_page' )->once()->with( 'edit-comments.php' );
		$callback();
		return true;
	} );

	Actions\expectAdded( 'init' )->once()->withArgs( function ( $callback ) {
		Functions\when( 'get_post_types' )->justReturn( [ 'post', 'page', 'attachment' ] );
		Functions\when( 'post_type_supports' )->alias( fn ( $type, $feature ) => $type !== 'attachment' && $feature === 'comments' );
		Functions\expect( 'remove_post_type_support' )->twice()->withArgs( fn ( $type, $feature ) => in_array( $type, [ 'post', 'page' ], true ) && 'comments' === $feature );
		$callback();
		return true;
	} );

	$this->loadPlugin( 'tsb-disable-comments' );
	expect( Filters\has( 'comments_open', '__return_false' ) )->toBe( 10 );
} );
