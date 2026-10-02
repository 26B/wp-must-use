<?php

use Brain\Monkey\Actions;

test( 'customizer cleanup removes the custom CSS section', function () {
	$this->loadPlugin( 'tsb-customizer' );

	$customizer = Mockery::mock();
	$customizer->shouldReceive( 'remove_section' )->once()->with( 'custom_css' );
	TSB\WP\MUPlugin\Customizer\disable_custom_css_section( $customizer );

	expect( Actions\has( 'customize_register', 'TSB\\WP\\MUPlugin\\Customizer\\disable_custom_css_section', 20 ) )->toBeTrue();
} );
