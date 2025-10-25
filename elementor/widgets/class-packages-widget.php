<?php
/**
 * Packages Widget (Standalone)
 *
 * Custom Elementor widget for adventure packages grid with custom layouts.
 * Replaces hook-based extension with self-contained widget.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Packages Widget Class
 *
 * Standalone Elementor widget with built-in custom layout support.
 * No dependency on nd-travel Packages widget hooks.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 */
class LoveTravelChild_Packages_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since  2.2.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-child-packages';
	}

	/**
	 * Get widget title.
	 *
	 * @since  2.2.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Adventure Packages', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  2.2.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * @since  2.2.0
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'lovetravel-child' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since  2.2.0
	 */
	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'packages_layout',
			array(
				'label'   => esc_html__( 'Layout', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout-1',
				'options' => array(
					'layout-1' => esc_html__( 'Layout 1', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'packages_qnt',
			array(
				'label'   => esc_html__( 'Number of Packages', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => -1,
			)
		);

		$this->add_control(
			'packages_id',
			array(
				'label'       => esc_html__( 'Specific Package ID', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => '',
				'description' => esc_html__( 'Leave empty to show all packages', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'packages_width',
			array(
				'label'   => esc_html__( 'Item Width', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'nd_travel_width_100_percentage',
				'options' => array(
					'nd_travel_width_100_percentage' => esc_html__( '100%', 'lovetravel-child' ),
					'nd_travel_width_50_percentage'  => esc_html__( '50%', 'lovetravel-child' ),
					'nd_travel_width_33_percentage'  => esc_html__( '33%', 'lovetravel-child' ),
					'nd_travel_width_25_percentage'  => esc_html__( '25%', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'thumbnail_size',
			array(
				'label'   => esc_html__( 'Thumbnail Size', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'thumbnail' => esc_html__( 'Thumbnail', 'lovetravel-child' ),
					'medium'    => esc_html__( 'Medium', 'lovetravel-child' ),
					'large'     => esc_html__( 'Large', 'lovetravel-child' ),
					'full'      => esc_html__( 'Full', 'lovetravel-child' ),
				),
			)
		);

		$this->end_controls_section();

		// Query Section
		$this->start_controls_section(
			'query_section',
			array(
				'label' => esc_html__( 'Query', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'packages_orderby',
			array(
				'label'   => esc_html__( 'Order By', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'Date', 'lovetravel-child' ),
					'title'      => esc_html__( 'Title', 'lovetravel-child' ),
					'menu_order' => esc_html__( 'Menu Order', 'lovetravel-child' ),
					'rand'       => esc_html__( 'Random', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'packages_order',
			array(
				'label'   => esc_html__( 'Order', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'lovetravel-child' ),
					'DESC' => esc_html__( 'Descending', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'destination_id',
			array(
				'label'       => esc_html__( 'Destination ID', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => '',
				'description' => esc_html__( 'Filter by destination (leave empty for all)', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'typology_slug',
			array(
				'label'       => esc_html__( 'Typology Slug', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Filter by typology (leave empty for all)', 'lovetravel-child' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since  2.2.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Check if in Elementor editor
		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		// Enqueue required scripts and styles (only if nd-travel plugin is active)
		if ( function_exists( 'nd_travel_scripts' ) || post_type_exists( 'nd_travel_cpt_1' ) ) {
			wp_enqueue_style(
				'nd_travel_style',
				plugins_url( 'nd-travel/assets/css/style.css' ),
				array(),
				null
			);
			wp_enqueue_script( 'masonry' );
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script(
				'nd_travel_elementor_packages_js',
				plugins_url( 'nd-travel/addons/elementor/packages/js/packages.js' ),
				array( 'jquery', 'masonry', 'imagesloaded' ),
				null,
				true
			);
		}

		// Extract settings
		$nd_travel_postgrid_order   = ! empty( $settings['packages_order'] ) ? $settings['packages_order'] : 'DESC';
		$nd_travel_postgrid_orderby = ! empty( $settings['packages_orderby'] ) ? $settings['packages_orderby'] : 'date';
		$packages_qnt               = isset( $settings['packages_qnt'] ) ? $settings['packages_qnt'] : -1;
		$packages_width             = ! empty( $settings['packages_width'] ) ? $settings['packages_width'] : 'nd_travel_width_100_percentage';
		$packages_layout            = ! empty( $settings['packages_layout'] ) ? $settings['packages_layout'] : 'layout-1';
		$packages_id                = ! empty( $settings['packages_id'] ) ? $settings['packages_id'] : '';
		$nd_travel_destination_id   = ! empty( $settings['destination_id'] ) ? $settings['destination_id'] : '';
		$nd_travel_typology_slug    = ! empty( $settings['typology_slug'] ) ? $settings['typology_slug'] : '';
		$packagesgrid_image_size    = ! empty( $settings['thumbnail_size'] ) ? $settings['thumbnail_size'] : 'large';

		// Build query args
		$args = array(
			'post_type'      => 'nd_travel_cpt_1',
			'posts_per_page' => $packages_qnt,
			'order'          => $nd_travel_postgrid_order,
			'orderby'        => $nd_travel_postgrid_orderby,
		);

		// Add specific package ID if set
		if ( ! empty( $packages_id ) ) {
			$args['p'] = $packages_id;
		}

		// Filter by destination
		if ( ! empty( $nd_travel_destination_id ) ) {
			$nd_travel_archive_form_destinations_array = array( $nd_travel_destination_id );

			// Handle current destination (value = 1)
			if ( 1 === absint( $nd_travel_destination_id ) ) {
				$nd_travel_destination_id                     = get_the_ID();
				$nd_travel_archive_form_destinations_array[0] = get_the_ID();
			}

			// Include child destinations if function exists
			if ( function_exists( 'nd_travel_get_destinations_with_parent' ) ) {
				$nd_travel_children_destinations_array = nd_travel_get_destinations_with_parent( $nd_travel_destination_id );
				if ( ! empty( $nd_travel_children_destinations_array ) ) {
					$nd_travel_destinations_query_i = 1;
					foreach ( $nd_travel_children_destinations_array as $nd_travel_children_destination_id ) {
						$nd_travel_archive_form_destinations_array[ $nd_travel_destinations_query_i ] = $nd_travel_children_destination_id;
						++$nd_travel_destinations_query_i;
					}
				}
			}

			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'nd_travel_meta_box_destinations',
					'type'  => 'numeric',
					'value' => $nd_travel_archive_form_destinations_array,
				),
			);
		} elseif ( ! empty( $nd_travel_typology_slug ) ) {
			// Filter by typology
			$nd_travel_get_current_typology_slug = $nd_travel_typology_slug;

			// Handle current typology (value = 1)
			if ( 1 === absint( $nd_travel_typology_slug ) ) {
				$nd_travel_get_current_typology_id   = get_the_ID();
				$nd_travel_get_current_typology_slug = get_post_field( 'post_name', $nd_travel_get_current_typology_id );
			}

			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'nd_travel_meta_box_typologies',
					'value'   => $nd_travel_get_current_typology_slug,
					'compare' => 'LIKE',
				),
			);
		}

		// Run query
		$the_query = new WP_Query( $args );

		// Start output
		echo '<div class="nd_travel_section nd_travel_masonry_content">';

		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			// Package info
			$nd_travel_id        = get_the_ID();
			$nd_travel_title     = get_the_title();
			$nd_travel_excerpt   = get_the_excerpt();
			$nd_travel_permalink = get_permalink( $nd_travel_id );

			// Color
			$nd_travel_meta_box_color = get_post_meta( get_the_ID(), 'nd_travel_meta_box_color', true );
			if ( '' === $nd_travel_meta_box_color ) {
				$nd_travel_meta_box_color = '#EA5B10';
			}

			// Meta fields
			$nd_travel_meta_box_tab_gallery_content    = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_gallery_content', true );
			$nd_travel_meta_box_tab_map_content        = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_map_content', true );
			$nd_travel_meta_box_featured_image_replace = get_post_meta( get_the_ID(), 'nd_travel_meta_box_featured_image_replace', true );

			// Load layout template (pass $is_edit_mode for conditional rendering)
			$nd_travel_layout_path = get_stylesheet_directory() . '/elementor/templates/packages/' . $packages_layout . '.php';
			if ( file_exists( $nd_travel_layout_path ) ) {
				include $nd_travel_layout_path;
			} else {
				echo '<p>' . esc_html__( 'Layout template not found', 'lovetravel-child' ) . '</p>';
			}
		}

		echo '</div>';

		wp_reset_postdata();
	}
}
