<?php
/**
 * @wordpress-plugin
 * Plugin Name: Lock Site
 * Description: Limit site access to back office users.
 * Version:     1.1.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

add_action(
	'init',
	function () {

		$uri = $_SERVER['REQUEST_URI'];

		if (
			is_admin()
			|| wp_doing_cron()
			|| ( defined( 'WP_CLI' ) && WP_CLI )
			|| preg_match( '/wp-login|wp-activate/', $uri ) === 1
		) {
			return;
		}

		// Bypass for non staging environments and auth users.
		if ( ! in_array( wp_get_environment_type(), [ 'staging', 'development' ], true ) || is_user_logged_in() ) {
			return;
		}

		// Bypass when header is present and valid.
		$headers = getallheaders();

		if (
			defined( 'LOCK_BYPASS_KEYS' )
			&& isset( $headers['X-Bypass-Lock'] )
			&& in_array( $headers['X-Bypass-Lock'], LOCK_BYPASS_KEYS, true )
		) {
			return;
		}

		$current_url =
			( $_SERVER['HTTPS'] ? 'https://' : 'http://' )
			. $_SERVER['HTTP_HOST']
			. $_SERVER['REQUEST_URI'];

		$login_url = wp_login_url();

		if ( $current_url !== $login_url ) {
			$login_url = add_query_arg( 'redirect_to', rawurlencode( $current_url ), $login_url );
		}

		nocache_headers();
		wp_safe_redirect( $login_url );
		exit;
	},
	1
);
