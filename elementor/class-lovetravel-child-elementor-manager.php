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
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-search-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-packages-widget.php';

		// Register widgets
		$widgets_manager->register( new \LoveTravelChildTypologyCardWidget() );
		$widgets_manager->register( new \LoveTravelChildTypologyCardsWidget() );
		$widgets_manager->register( new \LoveTravelChild_Search_Widget() );
		$widgets_manager->register( new \LoveTravelChild_Packages_Widget() );
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
	 * Register post meta fields with REST API.
	 *
	 * Enables WordPress ↔ Elementor sync via Dynamic Tags.
	 *
	 * @since    2.2.0
	 */
	public function register_post_meta() {
		// Load post meta manager
		require_once plugin_dir_path( __FILE__ ) . 'post-meta/class-post-meta-manager.php';

		// Register all meta fields
		$post_meta_manager = new LoveTravelChild_Post_Meta_Manager();
		$post_meta_manager->register_meta_fields();
	}

	/**
	 * Register Dynamic Tags group.
	 *
	 * Creates custom group in Elementor's dynamic tags menu.
	 *
	 * @since    2.2.0
	 * @param    \Elementor\Core\DynamicTags\Manager    $dynamic_tags_manager    Elementor dynamic tags manager.
	 */
	public function register_dynamic_tags_group( $dynamic_tags_manager ) {
		// Load dynamic tags manager
		require_once plugin_dir_path( __FILE__ ) . 'dynamic-tags/class-dynamic-tags-manager.php';

		// Register custom group
		$tags_manager = new LoveTravelChild_Dynamic_Tags_Manager();
		$tags_manager->register_group( $dynamic_tags_manager );
	}

	/**
	 * Register Dynamic Tags.
	 *
	 * Loads and registers custom Dynamic Tag classes for WordPress ↔ Elementor sync.
	 *
	 * @since    2.2.0
	 * @param    \Elementor\Core\DynamicTags\Manager    $dynamic_tags_manager    Elementor dynamic tags manager.
	 */
	public function register_dynamic_tags( $dynamic_tags_manager ) {
		// Load dynamic tags manager
		require_once plugin_dir_path( __FILE__ ) . 'dynamic-tags/class-dynamic-tags-manager.php';

		// Register custom tags
		$tags_manager = new LoveTravelChild_Dynamic_Tags_Manager();
		$tags_manager->register_tags( $dynamic_tags_manager );
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
	 * Enqueue plugin CSS in Elementor editor.
	 *
	 * Fixes editor preview by loading nd-elements and nd-travel CSS
	 * for Search and Packages widgets.
	 *
	 * @since    2.2.0
	 */
	public function enqueue_editor_styles() {
		// Enqueue nd-elements CSS (for Search widget column classes)
		$nd_elements_css = WP_PLUGIN_DIR . '/nd-elements/css/style.css';
		if ( file_exists( $nd_elements_css ) ) {
			wp_enqueue_style(
				'nd-elements-editor',
				plugins_url( 'nd-elements/css/style.css' ),
				array(),
				filemtime( $nd_elements_css )
			);
		}

		// Enqueue nd-travel CSS (for Packages widget grid layout)
		$nd_travel_css = WP_PLUGIN_DIR . '/nd-travel/assets/css/style.css';
		if ( file_exists( $nd_travel_css ) ) {
			wp_enqueue_style(
				'nd-travel-editor',
				plugins_url( 'nd-travel/assets/css/style.css' ),
				array(),
				filemtime( $nd_travel_css )
			);
		}

		// Add inline CSS to fix masonry layout in editor (float-based grid)
		$inline_css = '
			.elementor-editor-active .nd_travel_masonry_content {
				display: block;
				width: 100%;
			}
			.elementor-editor-active .nd_travel_masonry_item {
				display: inline-block;
				vertical-align: top;
			}
			.elementor-editor-active .nd_travel_width_25_percentage {
				width: 25% !important;
			}
			.elementor-editor-active .nd_travel_width_33_percentage {
				width: 33.33% !important;
			}
			.elementor-editor-active .nd_travel_width_50_percentage {
				width: 50% !important;
			}
			.elementor-editor-active .nd_travel_width_100_percentage {
				width: 100% !important;
			}
		';
		wp_add_inline_style( 'nd-travel-editor', $inline_css );
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
