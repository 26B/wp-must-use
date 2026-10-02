<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

if ( ! class_exists( Two_Factor_Core::class ) ) {
	class Two_Factor_Core {
		public const ENABLED_PROVIDERS_USER_META_KEY = '_enabled_two_factor_providers';
	}
}

if ( ! class_exists( Two_Factor_Totp::class ) ) {
	class Two_Factor_Totp {
		public const SECRET_META_KEY = '_two_factor_totp_secret';
	}
}

test( 'two-factor configuration permits TOTP and enables it when its secret is saved', function () {
	define( 'WP_DEBUG', false );

	Filters\expectAdded( 'two_factor_providers' )->once()->withArgs( function ( $callback ) {
		$providers = [ 'Two_Factor_Totp' => 'totp', 'Two_Factor_Email' => 'email' ];
		expect( $callback( $providers ) )->toBe( [ 'Two_Factor_Totp' => 'totp' ] );
		return true;
	} );
	Filters\expectAdded( 'two_factor_enabled_providers_for_user' )->once()->withArgs( function ( $callback ) {
		$providers = [ 'Two_Factor_Totp' ];
		expect( $callback( $providers ) )->toBe( $providers );
		return true;
	} );
	Filters\expectAdded( 'update_user_metadata' )->once()->withArgs( function ( $callback ) {
		Functions\when( 'get_user_meta' )->justReturn( [] );
		Functions\expect( 'update_user_meta' )->once()->with( 8, Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY, [ 'Two_Factor_Totp' ] );
		expect( $callback( null, 8, Two_Factor_Totp::SECRET_META_KEY, 'secret' ) )->toBeNull();
		return true;
	} );
	Actions\expectAdded( 'admin_init' )->once()->withArgs( function ( $callback ) {
		Functions\when( 'wp_doing_ajax' )->justReturn( true );
		$callback();
		return true;
	} );

	$this->loadPlugin( 'tsb-two-factor' );
} );

test( 'users without TOTP are redirected to their profile', function () {
	$output = $this->runPhp( <<<'PHP'
require 'vendor/autoload.php';
class Two_Factor_Totp { public const SECRET_META_KEY = '_totp_secret'; }
class Two_Factor_Core {
    public const ENABLED_PROVIDERS_USER_META_KEY = '_enabled_providers';
    public static function get_primary_provider_for_user( $id ) { return new stdClass(); }
}
define( 'WP_DEBUG', false );
Brain\Monkey\setUp();
Brain\Monkey\Functions\when( 'wp_doing_ajax' )->justReturn( false );
Brain\Monkey\Functions\when( 'wp_get_current_user' )->justReturn( (object) [ 'ID' => 8 ] );
Brain\Monkey\Functions\when( 'site_url' )->justReturn( 'https://site.test/wp-admin/profile.php' );
Brain\Monkey\Functions\when( 'wp_safe_redirect' )->alias( fn ( $url ) => print $url );
$_SERVER['REQUEST_URI'] = '/wp-admin/index.php';
Brain\Monkey\Actions\expectAdded( 'admin_init' )->once()->withArgs( function ( $callback ) { $callback(); return true; } );
require 'plugins/tsb-two-factor.php';
PHP );

	expect( $output )->toBe( 'https://site.test/wp-admin/profile.php' );
} );
