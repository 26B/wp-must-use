<?php

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;

test( 'site lock callback allows admin requests and registers early', function () {
	$_SERVER['REQUEST_URI'] = '/public-page';
	Functions\when( 'is_admin' )->justReturn( true );

	Actions\expectAdded( 'init' )->once()->withArgs( function ( $callback, $priority ) {
		$callback();
		return 1 === $priority;
	} );

	$this->loadPlugin( 'tsb-lock-site' );
} );

test( 'unauthenticated visitors on staging are redirected to login', function () {
	$output = $this->runPhp( <<<'PHP'
require 'vendor/autoload.php';
Brain\Monkey\setUp();
Brain\Monkey\Functions\when( 'is_admin' )->justReturn( false );
Brain\Monkey\Functions\when( 'wp_doing_cron' )->justReturn( false );
Brain\Monkey\Functions\when( 'wp_get_environment_type' )->justReturn( 'staging' );
Brain\Monkey\Functions\when( 'is_user_logged_in' )->justReturn( false );
Brain\Monkey\Functions\when( 'wp_login_url' )->justReturn( 'https://site.test/login' );
Brain\Monkey\Functions\when( 'add_query_arg' )->alias( fn ( $key, $value, $url ) => $url . '?' . $key . '=' . $value );
Brain\Monkey\Functions\when( 'nocache_headers' )->justReturn( null );
Brain\Monkey\Functions\when( 'wp_safe_redirect' )->alias( fn ( $url ) => print $url );
function getallheaders() { return []; }
$_SERVER = [ 'HTTPS' => 'on', 'HTTP_HOST' => 'site.test', 'REQUEST_URI' => '/private' ];
Brain\Monkey\Actions\expectAdded( 'init' )->once()->withArgs( function ( $callback ) { $callback(); return true; } );
require 'plugins/tsb-lock-site.php';
PHP );

	expect( $output )->toBe( 'https://site.test/login?redirect_to=https%3A%2F%2Fsite.test%2Fprivate' );
} );
