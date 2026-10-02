<?php

use Brain\Monkey\Functions;

test( 'ACF empty values are removed and tracked for meta cleanup', function () {
	$this->loadPlugin( 'tsb-acf' );

	$fields = [
		'field_title' => [ 'name' => 'title', 'type' => 'text' ],
		'field_tax'   => [ 'name' => 'topics', 'type' => 'taxonomy' ],
	];
	Functions\when( 'acf_get_field' )->alias( fn ( $key ) => $fields[ $key ] ?? null );
	Functions\when( 'get_field_object' )->alias( fn ( $key ) => $fields[ $key ] );
	Brain\Monkey\Filters\expectApplied( 'tsb_acf_prevent_empty_meta' )->andReturn( false );

	$empty = [];
	$filtered = TSB\WP\MUPlugin\ACF\filter_values(
		[ 'field_title' => '', 'field_tax' => '', 'unknown' => 'keep' ],
		$empty
	);

	expect( $filtered )->toBe( [ 'field_tax' => '', 'unknown' => 'keep' ] );
	expect( $empty )->toBe( [ 'title' => 'field_title', 'topics' => 'field_tax' ] );

	Functions\when( 'get_post_meta' )->justReturn( [ 'title' => [ 'old' ], '_title' => [ 'field_title' ] ] );
	Functions\expect( 'delete_post_meta' )->once()->with( 42, 'title' );
	Functions\expect( 'delete_post_meta' )->once()->with( 42, '_title', 'field_title' );
	TSB\WP\MUPlugin\ACF\delete_old_meta( 42, [ 'title' => 'field_title' ] );
} );
