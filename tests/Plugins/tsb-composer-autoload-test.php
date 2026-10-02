<?php

test( 'composer autoload plugin loads the WordPress-root autoloader', function () {
	$this->loadPlugin( 'tsb-composer-autoload' );

	expect( $GLOBALS['composer_autoload_fixture_loaded'] ?? false )->toBeTrue();
} );
