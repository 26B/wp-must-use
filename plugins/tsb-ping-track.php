<?php
/**
 * @wordpress-plugin
 * Plugin Name: Disable Pings and Trackbacks
 * Description: Disable all pings and trackbacks on all posts.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-2.0+
 */

add_filter( 'pings_open', '__return_false' );
