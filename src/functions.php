<?php
/**
 * WP Sockets Helper Functions
 *
 * @package WPSockets\Core
 * @since   1.0.0
 */

namespace WPSockets\Core;

use WPSockets\Core\Core;

/**
 * Register a new WP Socket (Admin Page).
 *
 * @since 1.0.0
 *
 * @param string $slug   The slug of the page.
 * @param array  $config {
 *     The configuration for the page.
 *
 *     @type string $page_title Page title.
 *     @type string $menu_title Menu title.
 *     @type string $capability Optional. Capability required. Default 'manage_options'.
 *     @type string $mode       Optional. View mode. Default 'panel'.
 *     @type array  $sockets    Array of socket definitions.
 * }
 */
function wp_sockets_register_page( $slug, $config ) {
	Core::instance()->register_socket( $slug, $config );
}

/**
 * Initialize the WP Sockets library.
 *
 * OPTIONAL: The framework auto-initializes when you call wp_sockets_register_page().
 * Only call this if you need to set a custom assets URL before registering pages.
 *
 * @since 1.0.0
 *
 * @param string|null $assets_url Optional. Custom URL for the assets directory.
 *                                Useful if the library is installed in a non-standard location
 *                                or symlinked (e.g. local composer package).
 */
function wp_sockets_init( $assets_url = null ) {
	$core = Core::instance();

	if ( $assets_url ) {
		$core->set_assets_url( $assets_url );
	}

	$core->init();
}
