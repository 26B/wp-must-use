<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

if ( ! class_exists( WP_Error::class ) ) {
	class WP_Error {
		public function __construct(
			public string $code,
			public string $message,
			public array $data
		) {}
	}
}

test( 'REST API access honors authentication, route allowlists, and query vars', function () {
	Actions\expectRemoved( 'template_redirect' )->once()->with( 'rest_output_link_header', 11 );
	Actions\expectRemoved( 'wp_head' )->once()->with( 'rest_output_link_wp_head', 10 );
	Actions\expectRemoved( 'xmlrpc_rsd_apis' )->once()->with( 'rest_output_rsd', 10 );

	$this->loadPlugin( 'tsb-rest-api' );
	expect( Filters\has( 'rest_authentication_errors', 'TSB\\WP\\MUPlugin\\RESTAPI\\disable_rest_api' ) )->toBe( 10 );

	$loggedIn = false;
	Functions\when( 'is_user_logged_in' )->alias( function () use ( &$loggedIn ) {
		return $loggedIn;
	} );
	Functions\when( '__' )->alias( fn ( $message ) => $message );
	Functions\when( 'rest_authorization_required_code' )->justReturn( 401 );
	$_SERVER['REQUEST_URI'] = '/blog/wp-json/public';
	$_GET = [ 'page' => '2' ];

	$slashRoute = [ [ 'url' => '/public', 'query_vars' => [ 'page' ] ] ];
	$bareRoute  = [ [ 'url' => 'public', 'query_vars' => null ] ];
	Filters\expectApplied( 'tsb_rest_api_routes_without_auth' )->times( 5 )->andReturn( $slashRoute, $bareRoute, $slashRoute, $slashRoute, [] );
	expect( TSB\WP\MUPlugin\RESTAPI\whitelisted() )->toBeTrue();
	expect( TSB\WP\MUPlugin\RESTAPI\whitelisted() )->toBeTrue();

	$_GET['unexpected'] = 'no';
	expect( TSB\WP\MUPlugin\RESTAPI\whitelisted() )->toBeFalse();
	unset( $_GET['unexpected'] );

	$_SERVER['REQUEST_URI'] = '/blog/not-wp-json/public';
	expect( TSB\WP\MUPlugin\RESTAPI\whitelisted() )->toBeFalse();
	$_SERVER['REQUEST_URI'] = '/blog/wp-json/public';

	Filters\expectApplied( 'tsb_rest_api_error' )->once()->andReturn( 'Custom restriction' );
	$error = TSB\WP\MUPlugin\RESTAPI\disable_rest_api( null );
	expect( $error )->toBeInstanceOf( WP_Error::class );
	expect( $error->code )->toBe( 'rest_login_required' );
	expect( $error->message )->toBe( 'Custom restriction' );
	expect( $error->data )->toBe( [ 'status' => 401 ] );

	$loggedIn = true;
	expect( TSB\WP\MUPlugin\RESTAPI\disable_rest_api( 'existing' ) )->toBe( 'existing' );
} );
