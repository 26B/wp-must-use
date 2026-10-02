<?php

use Brain\Monkey\Functions;

test( 'SVG mime types, filetype, and image sizing are handled', function () {
	$this->loadPlugin( 'tsb-svg' );

	expect( TSB\WP\MUPlugin\SVG\upload_mimes( [ 'jpg' => 'image/jpeg' ] ) )->toBe( [
		'jpg'  => 'image/jpeg',
		'svg'  => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
	] );

	Functions\when( 'wp_check_filetype' )->justReturn( [ 'ext' => 'svg', 'type' => 'image/svg+xml' ] );
	expect( TSB\WP\MUPlugin\SVG\wp_check_filetype_and_ext( [ 'proper_filename' => null ], '/tmp/icon.svg', 'icon.svg', [] ) )->toBe( [
		'ext'             => 'svg',
		'type'            => 'image/svg+xml',
		'proper_filename' => null,
	] );

	Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.test/icon.svg' );
	expect( TSB\WP\MUPlugin\SVG\image_downsize( false, 7 ) )->toBe( [ 'https://example.test/icon.svg', null, null, false ] );
	Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.test/photo.jpg' );
	expect( TSB\WP\MUPlugin\SVG\image_downsize( false, 8 ) )->toBeFalse();
} );
