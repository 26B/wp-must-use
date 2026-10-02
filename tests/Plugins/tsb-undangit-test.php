<?php

use Brain\Monkey\Actions;

test( 'capital P dangit callbacks are removed from configured filters only', function () {
	global $wp_filter;
	$callbacks = static fn () => null;
	$wp_filter = [];
	foreach ( [ 'the_content', 'the_title', 'wp_title', 'document_title', 'comment_text', 'widget_text_content', 'acf_the_content', 'unrelated' ] as $hook ) {
		$wp_filter[ $hook ] = (object) [ 'callbacks' => [ 10 => [ 'capital_P_dangit' => $callbacks, 'other_callback' => static fn () => null ] ] ];
	}

	Actions\expectAdded( 'init' )->once()->withArgs( function ( $callback, $priority ) {
		$callback();
		return PHP_INT_MAX === $priority;
	} );
	$this->loadPlugin( 'tsb-undangit' );

	foreach ( [ 'the_content', 'the_title', 'wp_title', 'document_title', 'comment_text', 'widget_text_content', 'acf_the_content' ] as $hook ) {
		expect( isset( $wp_filter[ $hook ]->callbacks[10]['capital_P_dangit'] ) )->toBeFalse();
		expect( isset( $wp_filter[ $hook ]->callbacks[10]['other_callback'] ) )->toBeTrue();
	}
	expect( isset( $wp_filter['unrelated']->callbacks[10]['capital_P_dangit'] ) )->toBeTrue();
} );
