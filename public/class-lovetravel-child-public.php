<?php
/**
 * Public Functionality
 *
 * Frontend-specific hooks and asset enqueuing.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/public
 * @since      2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public Class
 *
 * Manages frontend functionality including asset loading.
 */
class LoveTravelChildPublic {

	/**
	 * Theme identifier.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $themeName Theme slug.
	 */
	private $themeName;

	/**
	 * Theme version.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $version Theme version.
	 */
	private $version;

	/**
	 * Initialize the class.
	 *
	 * @since 2.0.0
	 * @param string $themeName Theme identifier.
	 * @param string $version   Theme version.
	 */
	public function __construct( $themeName, $version ) {
		$this->themeName = $themeName;
		$this->version   = $version;
	}

	/**
	 * Enqueue public styles.
	 *
	 * Priority 20 ensures child theme styles override parent theme and plugins.
	 * Parent theme styles enqueued first, then child theme overrides.
	 *
	 * @since 2.0.0
	 */
	public function enqueueStyles() {
		// Enqueue parent theme stylesheet (if not already loaded)
		wp_enqueue_style(
			'lovetravel-parent',
			get_template_directory_uri() . '/style.css',
			array(),
			wp_get_theme( 'lovetravel' )->get( 'Version' )
		);

		// Enqueue child theme stylesheet
		wp_enqueue_style(
			$this->themeName,
			get_stylesheet_directory_uri() . '/style.css',
			array( 'lovetravel-parent' ),
			$this->version
		);

		// Enqueue child theme public styles
		wp_enqueue_style(
			$this->themeName . '-public',
			get_stylesheet_directory_uri() . '/public/assets/css/public.css',
			array( $this->themeName ),
			$this->version,
			'all'
		);
	}

	/**
	 * Enqueue public scripts.
	 *
	 * Priority 20 ensures child theme scripts override parent theme and plugins.
	 * Uses WordPress 6.5+ Script Modules API for modern ES6 modules.
	 *
	 * @since 2.0.0
	 */
	public function enqueueScripts() {
		// For now, use a combined approach that works with current setup
		// Load the main module as a regular script with module type
		wp_enqueue_script(
			$this->themeName . '-main',
			get_stylesheet_directory_uri() . '/assets/js/modules/main.js',
			array(), // No jQuery dependency for ES6 modules
			$this->version,
			true
		);
		
		// Add module type attribute
		add_filter( 'script_loader_tag', array( $this, 'add_module_to_script' ), 10, 3 );
		
		// Add basic localized data for WordPress integration
		wp_localize_script( $this->themeName . '-main', 'lovetravelTheme', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'lovetravel_nonce' ),
			'isAdmin' => is_admin(),
		) );

		// Localize Load More specific data (used by packages load-more component).
		wp_localize_script( $this->themeName . '-main', 'lovetravelLoadMore', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'lovetravel_load_more_nonce' ),
		) );
	}
	
	/**
	 * Add module type to main script
	 *
	 * @since 2.3.0
	 */
	public function add_module_to_script( $tag, $handle, $src ) {
		if ( $this->themeName . '-main' === $handle ) {
			$tag = '<script type="module" src="' . esc_url( $src ) . '" id="' . esc_attr( $handle ) . '-js"></script>';
		}
		return $tag;
	}
}
