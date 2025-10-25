<?php
/**
 * Post Meta Manager
 *
 * Registers post meta fields with WordPress REST API to enable
 * WordPress ↔ Elementor synchronization via Dynamic Tags.
 *
 * @link       https://github.com/richardevcom
 * @since      2.2.0
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/post-meta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Meta Manager class.
 *
 * Registers post meta fields with show_in_rest enabled,
 * making them accessible to both WordPress and Elementor.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/post-meta
 * @author     richardevcom <hello@richardev.com>
 */
class LoveTravelChild_Post_Meta_Manager {

	/**
	 * Register all post meta fields.
	 *
	 * Called on init hook to register meta with WordPress and REST API.
	 *
	 * @since 2.2.0
	 */
	public function register_meta_fields() {
		// Typology Card Settings (nd_travel_cpt_2)
		$this->register_typology_card_meta();
	}

	/**
	 * Register typology card meta fields.
	 *
	 * These fields are used by both WordPress metabox and Elementor Dynamic Tags.
	 *
	 * @since 2.2.0
	 */
	private function register_typology_card_meta() {
		// Card Icon URL
		register_post_meta(
			'nd_travel_cpt_2',
			'lovetravel_child_meta_box_card_icon',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'description'   => __( 'Icon URL for typology card', 'lovetravel-child' ),
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// Card Background Image URL
		register_post_meta(
			'nd_travel_cpt_2',
			'lovetravel_child_meta_box_card_bg_image',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'description'   => __( 'Background image URL for typology card', 'lovetravel-child' ),
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

}
