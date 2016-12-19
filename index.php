<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name: Inpsyde Menu Cache
 * Plugin URI:  https://github.com/inpsyde/menu-cache
 * Description: Easily cache rendered menus using the Transients API.
 * Author:      Inpsyde GmbH, Thorsten Frommen, David Naber
 * Author URI:  https://inpsyde.com
 * Version:     1.1.0
 * License:     MIT
 */

namespace Inpsyde\MenuCache;

defined( 'ABSPATH' ) or die();

if ( is_admin() ) {
	return;
}

/**
 * Bootstraps the plugin.
 *
 * @since   1.0.0
 * @wp-hook plugins_loaded
 *
 * @return void
 */
function bootstrap() {

	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		/**
		 * Composer-generated autoload file.
		 */
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$cache = new MenuCache();

	add_filter( 'pre_wp_nav_menu', [ $cache, 'get_menu' ], 10, 2 );

	// Unfortunately, there is no appropriate action, so we have to (mis)use a filter here. Almost as late as possible.
	add_filter( 'wp_nav_menu', [ $cache, 'cache_menu' ], PHP_INT_MAX - 1, 2 );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\bootstrap' );
