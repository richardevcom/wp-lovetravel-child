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
	 *
	 * @since 2.0.0
	 */
	public function enqueueScripts() {
		wp_enqueue_script(
			$this->themeName . '-public',
			get_stylesheet_directory_uri() . '/public/assets/js/public.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}
}
