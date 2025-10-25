<?php
/**
 * Dynamic Tags Manager
 *
 * Manages Elementor Dynamic Tags registration for WordPress ↔ Elementor sync.
 *
 * @link       https://github.com/richardevcom
 * @since      2.2.0
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/dynamic-tags
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Dynamic Tags Manager class.
 *
 * Registers custom Elementor Dynamic Tags that pull data from
 * WordPress post meta, enabling unified editing experience.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/dynamic-tags
 * @author     richardevcom <hello@richardev.com>
 */
class LoveTravelChild_Dynamic_Tags_Manager {

	/**
	 * Register custom dynamic tag group.
	 *
	 * Creates "LoveTravel Child" group in Elementor's dynamic tags menu.
	 *
	 * @since 2.2.0
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager Elementor dynamic tags manager.
	 */
	public function register_group( $dynamic_tags_manager ) {
		$dynamic_tags_manager->register_group(
			'lovetravel-child',
			array(
				'title' => __( 'LoveTravel Child', 'lovetravel-child' ),
			)
		);
	}

	/**
	 * Register all custom dynamic tags.
	 *
	 * Loads and registers Dynamic Tag classes with Elementor.
	 *
	 * @since 2.2.0
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager Elementor dynamic tags manager.
	 */
	public function register_tags( $dynamic_tags_manager ) {
		// Load tag classes
		require_once plugin_dir_path( __FILE__ ) . 'class-image-dynamic-tag.php';

		// Register tags
		$dynamic_tags_manager->register( new \LoveTravelChild_Image_Dynamic_Tag() );
	}

}
