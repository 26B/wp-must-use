<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;

test( 'emoji callbacks remove assets and filter emoji data', function () {
	$this->loadPlugin( 'tsb-emojis' );

	foreach ( [ 'wp_head', 'admin_print_scripts', 'wp_print_styles', 'admin_print_styles' ] as $hook ) {
		Actions\expectRemoved( $hook )->once();
	}
	foreach ( [ 'the_content_feed', 'comment_text_rss', 'wp_mail' ] as $hook ) {
		Filters\expectRemoved( $hook )->once();
	}
	TSB\WP\MUPlugin\Emojis\disable_emojis();

	expect( TSB\WP\MUPlugin\Emojis\disable_emojis_tinymce( [ 'wpemoji', 'other' ] ) )->toBe( [ 1 => 'other' ] );
	expect( TSB\WP\MUPlugin\Emojis\disable_emojis_tinymce( null ) )->toBe( [] );

	$urls = [ 'https://s.w.org/images/core/emoji/emoji.svg', 'https://example.test/resource' ];
	expect( TSB\WP\MUPlugin\Emojis\disable_emojis_remove_dns_prefetch( $urls, 'dns-prefetch' ) )->toBe( [ 1 => 'https://example.test/resource' ] );
	expect( TSB\WP\MUPlugin\Emojis\disable_emojis_remove_dns_prefetch( $urls, 'preconnect' ) )->toBe( $urls );
	expect( Filters\has( 'emoji_svg_url', '__return_false' ) )->toBe( 10 );
} );
