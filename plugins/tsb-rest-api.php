<?php
/**
 * @wordpress-plugin
 * Plugin Name: REST API Improvements
 * Description: Add or change the behaviour of the REST API in WordPress.
 * Version:     1.0.1
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
add_filter( 'rest_authentication_errors', __NAMESPACE__ . '\\disable_rest_api' );

/**
 * Disable REST API for visitors not logged into WordPress
 *
 * @param mixed $access
 * @return mixed
 */
function disable_rest_api( $access ) {

	if ( ! is_user_logged_in() && ! whitelisted() ) {

		/**
		 * Filter the error message for REST API access restriction
		 *
		 * @param string $message The error message to display
		 */
		$message = apply_filters( 'tsb_rest_api_error', __( 'REST API restricted to authenticated users.', 'disable-wp-rest-api' ) );

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

	/**
	 * Allow access to specific REST API routes without authentication
	 *
	 * - `url` - The URL pattern to match (e.g. `/wp-json/wp/v2/posts`)
	 * - `query_vars` - An array of query vars to allow (e.g. `['page', 'per_page']`). Null if all query vars are allowed. Empty array if no query vars are allowed.
	 *
	 * @param array $routes_without_auth An array of REST API routes to allow access without authentication
	 */
	$routes_without_auth = apply_filters( 'tsb_rest_api_routes_without_auth', [] );

	// Get the current URL.
	$current_url = $_SERVER['REQUEST_URI'];

	// Remove the blog path from the current URL if it exists.
	$blog_name = get_blog_details()->path;
	if ( str_starts_with( $current_url, $blog_name ) ) {
		$current_url = '/' . substr( $current_url, strlen( $blog_name ) );
	}

	foreach ( $routes_without_auth as $route ) {

		// Check if the URL matches the route.
		$regex = '/^' . str_replace( '/', '\/', $route['url'] ) . '/';
		if ( ! preg_match( $regex, $current_url ) ) {
			continue;
		}

		// Check if the query vars are allowed.
		if ( $route['query_vars'] !== null && is_array( $route['query_vars'] ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( ! in_array( $key, $route['query_vars'] ) ) {
					return false;
				}
			}
		}

		return true;
	}

	return false;
}
