<?php
/**
 * @wordpress-plugin
 * Plugin Name: Release Version
 * Description: Inject release version constant.
 * Version:     1.1.0
 * Author:      Gulbenkian
 * Author URI:  https://github.com/gulbenkian/
 * License:     GPL-3.0+
 */

add_action(
	'plugins_loaded',
	function () {

		// The constant already exists.
		if ( defined( 'RELEASE_VERSION' ) ) {
			return;
		}

		// Don't do anything if in debug/development mode.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			return;
		}

		// Configure
		$cache_expiry_seconds = MINUTE_IN_SECONDS;
		$release              = get_site_transient( 'release-version' );

		// Rebuild from file when cache expires.
		if ( false === $release ) {
			$file = dirname( __DIR__, 2 ) . '/.release-version';

			// Check if the file exists and is readable.
			if ( ! file_exists( $file ) || ! is_readable( $file ) ) {
				return;
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$release = file_get_contents( $file, false, null, 0, 200 );

			// 30 seconds to expire
			set_site_transient( 'release-version', $release, $cache_expiry_seconds );
		}

		define( 'RELEASE_VERSION', $release );
	},
	1
);
