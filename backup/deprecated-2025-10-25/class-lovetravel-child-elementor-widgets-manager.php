<?php
/**
 * Elementor Widgets Manager
 *
 * Registers all custom Elementor widgets and creates custom category.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Widgets Manager class.
 *
 * @since 2.0.0
 */
class LoveTravelChildElementorWidgetsManager {

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
	 * @param    string $theme_name The name of this theme.
	 * @param    string $version    The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {
		$this->theme_name = $theme_name;
		$this->version    = $version;
	}

	/**
	 * Register custom Elementor category.
	 *
	 * Hooks: elementor/elements/categories_registered
	 *
	 * @since 2.0.0
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function registerCategory( $elements_manager ) {
		$elements_manager->add_category(
			'lovetravel-child',
			array(
				'title' => __( 'LoveTravel Child', 'lovetravel-child' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register custom widgets.
	 *
	 * Hooks: elementor/widgets/register
	 *
	 * @since 2.0.0
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function registerWidgets( $widgets_manager ) {
		// Require widget files
		require_once plugin_dir_path( __FILE__ ) . 'elementor-widgets/class-lovetravel-child-typology-card-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'elementor-widgets/class-lovetravel-child-typology-cards-widget.php';

		// Register widgets
		$widgets_manager->register( new \LoveTravelChildTypologyCardWidget() );
		$widgets_manager->register( new \LoveTravelChildTypologyCardsWidget() );
	}

}
