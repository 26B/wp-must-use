<?php
/**
 * @wordpress-plugin
 * Plugin Name: Customize Two-factor Plugin
 * Description: Defines restrictions on the Two-Factor plugin.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

namespace TSB\WP\MUPlugin\TwoFactor;

// Require the Two-Factor plugin classes to exist.
if ( ! class_exists( Two_Factor_Core::class ) || ! class_exists( Two_Factor_Totp::class ) ) {
	return;
}

// Don't do anything if in debug/development mode.
if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
	return;
}

add_action(
	'admin_init',
	function () {
		if ( wp_doing_ajax() ) {
			return;
		}
		$user             = wp_get_current_user();
		$primary_provider = \Two_Factor_Core::get_primary_provider_for_user( $user->ID );

		// User will be redirected to their profile page if TOTP is not their primary provider.
		if ( $primary_provider instanceof \Two_Factor_Totp ) {
			return;
		}

		// If the user is not in profile page, redirect them there to set up TOTP.
		if ( ! str_ends_with( $_SERVER['REQUEST_URI'], '/wp-admin/profile.php' ) ) {
			wp_safe_redirect( site_url( '/wp-admin/profile.php' ) );
			exit();
		}
	}
);

add_filter(
	'two_factor_providers',
	function ( $providers ) {
		$allowed_providers = [ 'Two_Factor_Totp' ];
		return array_filter( $providers, fn ( $provider ) => in_array( $provider, $allowed_providers, true ), ARRAY_FILTER_USE_KEY );
	}
);

add_filter(
	'two_factor_enabled_providers_for_user',
	function ( $providers ) {
		return $providers;
	},
	10,
	1
);

// Make sure TOTP gets enabled when secret key is set.
add_filter(
	'update_user_metadata',
	function ( $return, $user_id, $meta_key, $meta_value ) {
		if (
			! class_exists( \Two_Factor_Totp::class )
			|| $meta_key !== \Two_Factor_Totp::SECRET_META_KEY
			|| empty( $meta_value )
		) {
			return $return;
		}

		$providers = get_user_meta( $user_id, \Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY, true );
		if ( ! in_array( 'Two_Factor_Totp', $providers, true ) ) {
			update_user_meta( $user_id, \Two_Factor_Core::ENABLED_PROVIDERS_USER_META_KEY, [ 'Two_Factor_Totp' ] );
		}

		return $return;
	},
	10,
	4
);
