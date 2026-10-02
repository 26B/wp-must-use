<?php

use Brain\Monkey\Functions;

test( 'HTTPS filters normalize image and embed URLs to the active scheme', function () {
	$this->loadPlugin( 'tsb-https' );

	Functions\when( 'is_ssl' )->justReturn( true );
	Functions\when( 'set_url_scheme' )->alias( fn ( $url, $scheme ) => preg_replace( '#^https?:#', $scheme . ':', $url ) );
	$sources = [ [ 'url' => 'http://example.test/image.jpg' ] ];
	expect( TSB\WP\MUPlugin\HTTPS\wp_calculate_image_srcset( $sources ) )->toBe( [ [ 'url' => 'https://example.test/image.jpg' ] ] );
	expect( TSB\WP\MUPlugin\HTTPS\secure_oembed_result( '<iframe src="http://example.test"></iframe>' ) )->toContain( 'https://example.test' );
	expect( TSB\WP\MUPlugin\HTTPS\secure_embed_oembed_html( 'http://example.test' ) )->toBe( 'https://example.test' );

	Functions\when( 'is_ssl' )->justReturn( false );
	expect( TSB\WP\MUPlugin\HTTPS\secure_oembed_result( 'http://example.test' ) )->toBe( 'http://example.test' );
} );
