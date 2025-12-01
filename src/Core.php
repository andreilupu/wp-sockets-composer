<?php
/**
 * WP Sockets Core Class
 *
 * @package WPSockets\Core
 * @since   1.0.0
 */

namespace WPSockets\Core;

/**
 * Class Core
 *
 * Main entry point for the WP Sockets library.
 * Handles initialization, asset enqueueing, and page registration.
 *
 * @since 1.0.0
 */
class Core {

	/**
	 * The single instance of the class.
	 *
	 * @var Core
	 */
	protected static $instance = null;

	/**
	 * Whether the library has been initialized.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Registered pages.
	 *
	 * @var array
	 */
	protected $pages = array();

	/**
	 * Custom assets URL.
	 *
	 * @var string
	 */
	protected $assets_url;

	/**
	 * Main Core Instance.
	 *
	 * Ensures only one instance of Core is loaded or can be loaded.
	 *
	 * @return Core - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set a custom URL for the assets directory.
	 * Useful when the library is symlinked or plugins_url() fails.
	 *
	 * @param string $url The URL to the assets directory.
	 */
	public function set_assets_url( $url ) {
		$this->assets_url = rtrim( $url, '/' );
	}

	/**
	 * Initialize the library.
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		$this->initialized = true;
	}

	/**
	 * Register a new admin page.
	 *
	 * @param string $slug   The slug of the page.
	 * @param array  $config The configuration for the page.
	 */
	public function register_socket( $slug, $config ) {
		// Basic validation.
		if ( empty( $slug ) || empty( $config['page_title'] ) || empty( $config['menu_title'] ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WP Sockets: Invalid page configuration for slug: ' . sanitize_key( $slug ) );
			return;
		}
		$this->pages[ $slug ] = $config;
	}

	/**
	 * Get all registered pages.
	 *
	 * @return array
	 */
	public function get_pages() {
		return apply_filters( 'wp_sockets_pages', $this->pages );
	}

	/**
	 * Register settings for the admin pages.
	 */
	public function register_settings() {
		$pages = $this->get_pages();

		foreach ( $pages as $page_slug => $config ) {
			// If the page has a specific setting ID, use it. Otherwise use the page slug.
			$option_name = $config['id'] ?? $page_slug;

			register_setting(
				'general', // Group? Or maybe a custom group? 'general' makes it available in options.
				$option_name,
				array(
					'type'         => 'object',
					'show_in_rest' => array(
						'schema' => array(
							'type'                 => 'object',
							'additionalProperties' => true,
						),
					),
					'default'      => array(),
				)
			);
		}
	}

	/**
	 * Register admin pages based on the configuration.
	 */
	public function register_admin_pages() {
		$pages = $this->get_pages();

		foreach ( $pages as $page_slug => $config ) {
			$capability = $config['capability'] ?? 'manage_options';

			add_menu_page(
				$config['page_title'],
				$config['menu_title'],
				$capability,
				$page_slug,
				function () use ( $page_slug ) {
					echo '<div id="wp-sockets-root-' . esc_attr( $page_slug ) . '"></div>';
				}
			);
		}
	}

	/**
	 * Enqueue the necessary scripts and styles.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		$pages               = $this->get_pages();
		$current_page_slug   = null;
		$current_page_config = null;

		// Check if we are on one of our pages.
		foreach ( $pages as $slug => $config ) {
			if ( 'toplevel_page_' . $slug === $hook ) {
				$current_page_slug   = $slug;
				$current_page_config = $config;
				break;
			}
		}

		if ( ! $current_page_slug ) {
			return;
		}

		$asset_file = include dirname( __DIR__ ) . '/assets/index.asset.php';

		// Determine the assets URL
		// If set_assets_url was called, use that.
		// Otherwise, try to determine it relative to this file.
		$js_url = $this->assets_url
			? $this->assets_url . '/index.js'
			: plugins_url( '../assets/index.js', __FILE__ );

		wp_enqueue_script(
			'wp-sockets-js',
			$js_url,
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		// Initialize the app.
		$init_config = array(
			'selector'       => '#wp-sockets-root-' . $current_page_slug,
			'mode'           => $current_page_config['mode'] ?? 'panel',
			'sockets'        => $current_page_config['sockets'] ?? array(),
			'title'          => $current_page_config['page_title'] ?? '',
			'withSaveButton' => $current_page_config['withSaveButton'] ?? true,
		);

		// Use wp.domReady to ensure the DOM is fully loaded before mounting React.
		wp_add_inline_script(
			'wp-sockets-js',
			sprintf(
				'wp.domReady( function() { window.WPSockets.createSocketsWpRoot( "%s", %s ); } );',
				esc_js( $current_page_slug ),
				wp_json_encode( $init_config )
			)
		);

		if ( file_exists( dirname( __DIR__ ) . '/assets/index.css' ) ) {
			$css_url = $this->assets_url
				? $this->assets_url . '/index.css'
				: plugins_url( '../assets/index.css', __FILE__ );

			wp_enqueue_style(
				'wp-sockets-css',
				$css_url,
				array( 'wp-components' ), // Depend on WordPress components styles.
				$asset_file['version']
			);
		}

		// Enqueue WordPress components styles for proper Gutenberg UI.
		wp_enqueue_style( 'wp-components' );
	}
}
