<?php
/**
 * Image Dynamic Tag
 *
 * Dynamic tag for image URLs from post meta.
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
 * Image Dynamic Tag class.
 *
 * Returns image URL from WordPress post meta for use in Elementor.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/dynamic-tags
 * @author     richardevcom <hello@richardev.com>
 */
class LoveTravelChild_Image_Dynamic_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	/**
	 * Get tag name.
	 *
	 * @since 2.2.0
	 * @return string Tag name.
	 */
	public function get_name() {
		return 'lovetravel-child-image';
	}

	/**
	 * Get tag title.
	 *
	 * @since 2.2.0
	 * @return string Tag title.
	 */
	public function get_title() {
		return __( 'Post Meta Image', 'lovetravel-child' );
	}

	/**
	 * Get tag group.
	 *
	 * @since 2.2.0
	 * @return array Tag group.
	 */
	public function get_group() {
		return array( 'lovetravel-child' );
	}

	/**
	 * Get tag categories.
	 *
	 * @since 2.2.0
	 * @return array Tag categories.
	 */
	public function get_categories() {
		return array( \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY );
	}

	/**
	 * Register tag controls.
	 *
	 * @since 2.2.0
	 */
	protected function register_controls() {
		$this->add_control(
			'meta_key',
			array(
				'label'   => __( 'Meta Key', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'lovetravel_child_meta_box_card_icon',
			)
		);
	}

	/**
	 * Get tag value.
	 *
	 * Returns image URL from post meta.
	 *
	 * @since 2.2.0
	 * @param array $options Tag options.
	 * @return array Image data array.
	 */
	public function get_value( array $options = array() ) {
		$meta_key = $this->get_settings( 'meta_key' );
		$image_url = get_post_meta( get_the_ID(), $meta_key, true );

		if ( empty( $image_url ) ) {
			return array();
		}

		return array(
			'id'  => '',
			'url' => $image_url,
		);
	}

}
