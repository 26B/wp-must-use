<?php

namespace TenupFramework\Facades;

/**
 * Class WordPress
 *
 * This class provides static methods to interact with WordPress functions which might vary
 * under certain plugins and conditions.
 *
 * @package TenupFramework\Facades
 * @since 1.3.0
 */
class WordPress {

	/**
	 * Get the home URL.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_home_url() : string {

		// If Polylang is active, use the Polylang home URL function.
		if ( function_exists( 'pll_home_url' ) ) {
			$slug = pll_current_language( 'slug' );
			if ( $slug !== pll_default_language( 'slug' ) ) {
				return home_url( $slug . '/' );
			}
		}

		// Otherwise, return the default home URL.
		return home_url();
	}

	/**
	 * Get the locale.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_locale() : string {

		// If Polylang is active, use the Polylang pll_current_language function.
		if ( function_exists( 'pll_current_language' ) ) {
			$locale = pll_current_language( 'locale' );
			if ( $locale ) {
				return $locale;
			}
			return pll_default_language( 'locale' );
		}

		// Otherwise, return the default locale.
		return get_locale();
	}

	/**
	 * Get the default locale.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public static function get_default_locale() : string {

		// If Polylang is active, use the Polylang pll_default_language function.
		if ( function_exists( 'pll_default_language' ) ) {
			$default = pll_default_language( 'locale' );
			if ( $default ) {
				return $default;
			}
		}

		if ( is_plugin_active( 'unbabble/unbabble.php' ) && class_exists( 'TwentySixB\WP\Plugin\Unbabble\LangInterface' ) ) {
			$default = \TwentySixB\WP\Plugin\Unbabble\LangInterface::get_default_language();
			if ( $default ) {
				return $default;
			}
		}

		// Otherwise, return the default locale.
		return get_locale();
	}
}
