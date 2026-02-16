<?php
/**
 * @wordpress-plugin
 * Plugin Name: ACF Improvements
 * Description: Add or change the behaviour of Advanced Custom Fields (ACF) in WordPress.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

namespace TSB\WP\MUPlugin\ACF;

/**
 * Filter the values in
 *
 * @since 0.0.0
 * @param  array  $values
 * @param  array  &$empty
 * @param  string $prefix_key
 * @return array
 */
function filter_values( array $values, array &$empty, string $prefix_key = '' ) : array {
	$new_values = $values;
	foreach ( $values as $field_key => $value ) {
		$field = acf_get_field( $field_key );
		if ( ! $field ) {
			continue;
		}
		$field_name = $field['name'];
		if ( is_array( $value ) && in_array( $field['type'], [ 'repeater', 'flexible_content' ], true ) ) {
			$index = 0;
			foreach ( $value as $row_key => $row_values ) {
				$new_values[ $field_key ][ $row_key ] = filter_values( $row_values, $empty, "{$prefix_key}{$field_name}_{$index}_" );
				++$index;
			}
			continue;
		}

		if ( $value === '' ) {

			/**
			 * Taxonomy empty values need to be passed to ACF so it clears the relationships, so
			 * we cannot unset them. We still add them to $empty so they are deleted after ACF
			 * has done its part.
			 */
			if ( get_field_object( $field_key )['type'] !== 'taxonomy' ) {
				unset( $new_values[ $field_key ] );
			}

			$empty[ "{$prefix_key}{$field_name}" ] = $field_key;
			continue;
		}

		if ( apply_filters( 'fcg_acf_prevent_empty_meta', false, $field, $value ) ) {

			/**
			 * Taxonomy empty values need to be passed to ACF so it clears the relationships, so
			 * we cannot unset them. We still add them to $empty so they are deleted after ACF
			 * has done its part.
			 */
			if ( get_field_object( $field_key )['type'] !== 'taxonomy' ) {
				unset( $new_values[ $field_key ] );
			}

			$empty[ "{$prefix_key}{$field_name}" ] = $field_key;
		}
	}
	return $new_values;
}

/**
 * Delete old meta values that were supposed to be empty.
 *
 * @since  0.0.0
 * @param  int   $post_id
 * @param  array $empty
 * @return void
 */
function delete_old_meta( int $post_id, array $empty ) : void {
	$current_values = get_post_meta( $post_id );
	foreach ( $empty as $empty_key => $field_key ) {
		if ( ! isset( $current_values[ $empty_key ] ) ) {
			continue;
		}

		delete_post_meta( $post_id, $empty_key );
		delete_post_meta( $post_id, "_{$empty_key}", $field_key );
	}
}

/**
 * Prevent empty meta from being saved and delete old values for coherence.
 *
 * @since  0.0.0
 * @param  int|string $post_id
 * @return void
 * @SuppressWarnings(PHPMD.Superglobals)
 */
function prevent_empty_meta( $post_id ) : void {
	if ( ! is_numeric( $post_id ) || 0 > (int) $post_id ) {
		return;
	}

	// Needed for meta removal to work during rewrite/republish.
	if ( get_post_meta( $post_id, '_dp_is_rewrite_republish_copy', true ) ) {
		return;
	}

	// Cast to int for safety.
	$post_id = (int) $post_id;

	// Get submitted values.
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$values = $_POST['acf'];

	// Get filtered values that should be saved and the empty values that were supposed to be saved.
	$empty_saves  = [];
	$_POST['acf'] = filter_values( $values, $empty_saves );

	// Delete empty values (if they exist) where acf was supposed to save to prevent incoherence.
	add_action(
		'acf/save_post',
		fn() => delete_old_meta( $post_id, $empty_saves ),
		20 // Run after ACF saves the post/meta.
	);
}

// Priority needs to be before 10, so we can change what ACF saves.
add_action( 'acf/save_post', __NAMESPACE__ . '\\prevent_empty_meta', 9 );
