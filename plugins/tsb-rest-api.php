<?php
/**
 * @wordpress-plugin
 * Plugin Name: REST API Improvements
 * Description: Add or change the behaviour of the REST API in WordPress.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

namespace TSB\WP\MUPlugin\RESTAPI;

/**
 * Disable REST API link in HTTP headers
 * Link: <https://example.com/wp-json/>; rel="https://api.w.org/"
 */
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

/**
 * Disable REST API links in HTML <head>
 * <link rel='https://api.w.org/' href='https://example.com/wp-json/' />
 */
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );

/**
 * Control access to REST API.
 */
add_filter( 'rest_authentication_errors', __NAMESPACE__ . '\\disable_api' );

/**
 * Disable REST API for visitors not logged into WordPress
 *
 * @param mixed $access
 * @return mixed
 */
function disable_api( $access ) {

	if ( ! is_user_logged_in() && ! whitelisted() ) {

		$message = apply_filters( 'disable_wp_rest_api_error', __( 'REST API restricted to authenticated users.', 'disable-wp-rest-api' ) );

		return new \WP_Error(
			'rest_login_required',
			$message,
			[ 'status' => rest_authorization_required_code() ]
		);
	}

	return $access;
}

/**
 * Allow access to specific REST API routes without authentication
 *
 * @return bool True if access is allowed, false otherwise
 */
function whitelisted() {

	// TODO: Add blocking by query vars.

	/**
	 * Allow access to specific REST API routes without authentication
	 *
	 * @param array $routes_without_auth An array of REST API routes to allow access without authentication
	 */
	$routes_without_auth = apply_filters( 'disable_wp_rest_api_routes_without_auth', [] );

	foreach ( $routes_without_auth as $url ) {
		$regex = '/^' . str_replace( '/', '\/', $url ) . '/';
		if ( preg_match( $regex, $_SERVER['REQUEST_URI'] ) ) {
			return true;
		}
	}

	return false;
}
