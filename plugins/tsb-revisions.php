<?php
/**
 * @wordpress-plugin
 * Plugin Name: Limit post revisions
 * Description: Limit post revisions.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

if ( ! defined( 'WP_POST_REVISIONS' ) ) {
	define( 'WP_POST_REVISIONS', 10 );
}

// TODO: Add a timebased mode and combine both through settings.
