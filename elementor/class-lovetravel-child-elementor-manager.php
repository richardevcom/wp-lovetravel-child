<?php
/**
 * Elementor Manager
 *
 * Manages all Elementor-related functionality for the theme including
 * widgets, dynamic tags, post meta, and metaboxes.
 *
 * @link       https://github.com/richardevcom
 * @since      2.0.0
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Manager class.
 *
 * This class manages all Elementor integrations including:
 * - Custom widget registration
 * - Dynamic Tags for WordPress ↔ Elementor sync
 * - Post meta registration
 * - Admin metaboxes
 *
 * @since      2.0.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor
 * @author     richardevcom <hello@richardev.com>
 */
class LoveTravelChild_Elementor_Manager {

	/**
	 * The ID of this theme.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $theme_name    The ID of this theme.
	 */
	private $theme_name;

	/**
	 * The version of this theme.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this theme.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string    $theme_name    The name of this theme.
	 * @param    string    $version       The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {
		$this->theme_name = $theme_name;
		$this->version    = $version;
	}

	/**
	 * Register custom Elementor widget category.
	 *
	 * Adds "LoveTravel Child" category to Elementor editor.
	 *
	 * @since    2.0.0
	 * @param    object    $elements_manager    Elementor's elements manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'lovetravel-child',
			array(
				'title' => __( 'LoveTravel Child', 'lovetravel-child' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register custom Elementor widgets.
	 *
	 * Loads widget classes and registers them with Elementor.
	 *
	 * @since    2.0.0
	 * @param    object    $widgets_manager    Elementor's widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		// Load widget files
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-typology-card-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-typology-cards-widget.php';

		// Register widgets
		$widgets_manager->register( new \LoveTravelChildTypologyCardWidget() );
		$widgets_manager->register( new \LoveTravelChildTypologyCardsWidget() );
	}

	/**
	 * Register metaboxes for Elementor integration.
	 *
	 * Loads metabox classes that provide WordPress admin UI for
	 * data that syncs with Elementor via Dynamic Tags.
	 *
	 * @since    2.0.0
	 */
	public function register_metaboxes() {
		// Load metabox files
		require_once plugin_dir_path( __FILE__ ) . 'metaboxes/class-typology-card-metabox.php';

		// Metaboxes auto-register via hooks in their constructors
		new LoveTravelChild_Typology_Card_Metabox( $this->theme_name, $this->version );
	}

	/**
	 * Enqueue Elementor-specific styles.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {
		// Future: Elementor-specific styles
		// wp_enqueue_style( $this->theme_name . '-elementor', ... );
	}

	/**
	 * Enqueue Elementor-specific scripts.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {
		// Future: Elementor-specific scripts
		// wp_enqueue_script( $this->theme_name . '-elementor', ... );
	}

}
