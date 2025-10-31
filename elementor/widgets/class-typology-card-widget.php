<?php
/**
 * Typology Card Widget - Brand New
 *
 * Elementor widget for displaying a single typology card with horizontal layout.
 * Features icon on left, title + subtitle on right, with nd-travel meta field integration.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Typology Card Widget class.
 *
 * @since 2.2.0
 */
class LoveTravelChild_Typology_Card_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.2.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-child-typology-card';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.2.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Typology Card', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.2.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-info-box';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 2.2.0
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'lovetravel-child' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 2.2.0
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'typology', 'card', 'travel', 'nd-travel' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since 2.2.0
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register content tab controls.
	 *
	 * @since 2.2.0
	 */
	private function register_content_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Typology Selection with dynamic search
		$this->add_control(
			'typology_id',
			array(
				'label'       => esc_html__( 'Select Typology', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => '',
				'options'     => $this->get_typology_options(),
				'description' => esc_html__( 'Start typing to search typologies', 'lovetravel-child' ),
			)
		);

		// Custom title option
		$this->add_control(
			'custom_title',
			array(
				'label'       => esc_html__( 'Custom Title', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Optional custom title', 'lovetravel-child' ),
				'description' => esc_html__( 'Leave empty to use typology title', 'lovetravel-child' ),
			)
		);

		// Custom subtitle option
		$this->add_control(
			'custom_subtitle',
			array(
				'label'       => esc_html__( 'Custom Subtitle', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'placeholder' => esc_html__( 'Optional custom subtitle', 'lovetravel-child' ),
				'description' => esc_html__( 'Leave empty to use typology preview text', 'lovetravel-child' ),
			)
		);

		$this->end_controls_section();

		// Display Options Section
		$this->start_controls_section(
			'display_section',
			array(
				'label' => esc_html__( 'Display Options', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => esc_html__( 'Show Icon', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_subtitle',
			array(
				'label'        => esc_html__( 'Show Subtitle', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		// Link Section
		$this->start_controls_section(
			'link_section',
			array(
				'label' => esc_html__( 'Link', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'   => esc_html__( 'Link Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'typology',
				'options' => array(
					'none'     => esc_html__( 'No Link', 'lovetravel-child' ),
					'typology' => esc_html__( 'Typology Page', 'lovetravel-child' ),
					'custom'   => esc_html__( 'Custom URL', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'custom_link',
			array(
				'label'         => esc_html__( 'Custom Link', 'lovetravel-child' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'lovetravel-child' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'     => array(
					'link_type' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style tab controls.
	 *
	 * @since 2.2.0
	 */
	private function register_style_controls() {
		// Card Style Section
		$this->start_controls_section(
			'card_style',
			array(
				'label' => esc_html__( 'Card Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_width',
			array(
				'label'      => esc_html__( 'Card Width', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 200,
						'max' => 1200,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card-container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_height',
			array(
				'label'      => esc_html__( 'Card Height', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 80,
						'max'  => 300,
						'step' => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 120,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Background Type
		$this->add_control(
			'background_type',
			array(
				'label'   => esc_html__( 'Background Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'color',
				'options' => array(
					'color' => esc_html__( 'Color', 'lovetravel-child' ),
					'image' => esc_html__( 'Image', 'lovetravel-child' ),
					'meta'  => esc_html__( 'Typology Meta Image', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .typology-card' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'background_type' => 'color',
				),
			)
		);

		$this->add_control(
			'background_image',
			array(
				'label'     => esc_html__( 'Background Image', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'condition' => array(
					'background_type' => 'image',
				),
			)
		);

		$this->add_control(
			'background_overlay',
			array(
				'label'     => esc_html__( 'Background Overlay', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.9)',
				'selectors' => array(
					'{{WRAPPER}} .typology-card::before' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'background_type!' => 'color',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .typology-card',
			)
		);

		$this->add_control(
			'card_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 15,
					'right'    => 15,
					'bottom'   => 15,
					'left'     => 15,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .typology-card',
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => 20,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_gap',
			array(
				'label'      => esc_html__( 'Gap Between Icon & Content', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Icon Style Section
		$this->start_controls_section(
			'icon_style',
			array(
				'label' => esc_html__( 'Icon', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'use_custom_icon',
			array(
				'label'        => esc_html__( 'Override Icon', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Override the typology meta field icon', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'custom_icon',
			array(
				'label'            => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'             => \Elementor\Controls_Manager::ICONS,
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'use_custom_icon' => 'yes',
				),
				'skin'             => 'inline',
				'exclude_inline_options' => array( 'svg' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 10,
						'max'  => 200,
						'step' => 2,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .typology-card-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .typology-card-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .typology-card-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .typology-card-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_container_size',
			array(
				'label'      => esc_html__( 'Icon Container Size', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 30,
						'max'  => 200,
						'step' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_background_color',
			array(
				'label'     => esc_html__( 'Icon Background Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .typology-card-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_border_color',
			array(
				'label'     => esc_html__( 'Icon Border Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .typology-card-icon' => 'border: 1px solid {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => esc_html__( 'Icon Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => esc_html__( 'Icon Margin', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 16,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Text Style Section
		$this->start_controls_section(
			'text_style',
			array(
				'label' => esc_html__( 'Text Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// Title styles
		$this->add_control(
			'title_heading',
			array(
				'label' => esc_html__( 'Title', 'lovetravel-child' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .typology-card-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .typology-card-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Title Margin', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 5,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .typology-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// Subtitle styles
		$this->add_control(
			'subtitle_heading',
			array(
				'label'     => esc_html__( 'Subtitle', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_subtitle' => 'yes',
				),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#666666',
				'selectors' => array(
					'{{WRAPPER}} .typology-card-subtitle' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_subtitle' => 'yes',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'subtitle_typography',
				'selector'  => '{{WRAPPER}} .typology-card-subtitle',
				'condition' => array(
					'show_subtitle' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Hover Effects Section
		$this->start_controls_section(
			'hover_effects',
			array(
				'label' => esc_html__( 'Hover Effects', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hover_animation',
			array(
				'label'   => esc_html__( 'Hover Animation', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::HOVER_ANIMATION,
				'default' => '',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'hover_box_shadow',
				'label'    => esc_html__( 'Hover Shadow', 'lovetravel-child' ),
				'selector' => '{{WRAPPER}} .typology-card:hover',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get typology options for select control.
	 *
	 * @since 2.2.0
	 * @return array Typology options.
	 */
	private function get_typology_options() {
		$options = array( '' => esc_html__( 'Select Typology', 'lovetravel-child' ) );

		$typologies = get_posts(
			array(
				'post_type'      => 'nd_travel_cpt_2',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
			)
		);

		foreach ( $typologies as $typology ) {
			$options[ $typology->ID ] = $typology->post_title;
		}

		return $options;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 2.2.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['typology_id'] ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="typology-card-placeholder">';
				echo '<p>' . esc_html__( 'Please select a typology from the content settings.', 'lovetravel-child' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		$typology = get_post( $settings['typology_id'] );
		if ( ! $typology || 'nd_travel_cpt_2' !== $typology->post_type ) {
			return;
		}

		// Get typology data
		$typology_data = $this->get_typology_data( $typology, $settings );

		// Render the card
		$this->render_card( $typology_data, $settings );
	}

	/**
	 * Get typology data from post and meta fields.
	 *
	 * @since 2.2.0
	 * @param WP_Post $typology Typology post object.
	 * @param array   $settings Widget settings.
	 * @return array Typology data.
	 */
	private function get_typology_data( $typology, $settings ) {
		$data = array();

		// Basic data
		$data['id']  = $typology->ID;
		$data['url'] = get_permalink( $typology->ID );

		// Title - custom or post title
		if ( ! empty( $settings['custom_title'] ) ) {
			$data['title'] = $settings['custom_title'];
		} else {
			$data['title'] = $typology->post_title;
		}

		// Subtitle - custom, meta field, or excerpt
		if ( ! empty( $settings['custom_subtitle'] ) ) {
			$data['subtitle'] = $settings['custom_subtitle'];
		} else {
			// Try meta field first
			$preview_text = get_post_meta( $typology->ID, 'nd_travel_meta_box_cpt_2_text_preview', true );
			if ( ! empty( $preview_text ) ) {
				$data['subtitle'] = $preview_text;
			} else {
				// Fallback to excerpt
				$excerpt = get_the_excerpt( $typology->ID );
				$data['subtitle'] = ! empty( $excerpt ) ? $excerpt : '';
			}
		}

		// Icon - custom or meta field
		if ( 'yes' === $settings['use_custom_icon'] && ! empty( $settings['custom_icon']['value'] ) ) {
			$data['icon'] = $settings['custom_icon'];
		} else {
			$meta_icon = get_post_meta( $typology->ID, 'nd_travel_meta_box_cpt_2_icon', true );
			if ( ! empty( $meta_icon ) ) {
				// Create icon array for meta field (assume it's an image URL)
				$data['icon'] = array(
					'value'   => $meta_icon,
					'library' => '',
				);
			}
		}

		// Icon background color - meta field or default
		$meta_color = get_post_meta( $typology->ID, 'nd_travel_meta_box_cpt_2_color', true );
		$data['icon_bg_color'] = ! empty( $meta_color ) ? $meta_color : '#f5f5f5';

		// Background image (for meta background type)
		if ( 'meta' === $settings['background_type'] ) {
			// Try header image first
			$header_image = get_post_meta( $typology->ID, 'nd_travel_meta_box_image_cpt_2', true );
			if ( ! empty( $header_image ) ) {
				$data['background_image'] = $header_image;
			} else {
				// Fallback to post thumbnail
				$thumbnail_id = get_post_thumbnail_id( $typology->ID );
				if ( $thumbnail_id ) {
					$thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'large' );
					$data['background_image'] = $thumbnail_url ? $thumbnail_url[0] : '';
				}
			}
		}

		return $data;
	}

	/**
	 * Render the card HTML.
	 *
	 * @since 2.2.0
	 * @param array $data     Typology data.
	 * @param array $settings Widget settings.
	 */
	private function render_card( $data, $settings ) {
		// Determine link URL
		$link_url = $this->get_link_url( $data, $settings );

		// Prepare card style
		$card_style = '';
		$card_classes = array( 'typology-card' );

		// Background styling
		if ( 'image' === $settings['background_type'] && ! empty( $settings['background_image']['url'] ) ) {
			$card_style .= 'background-image: url(' . esc_url( $settings['background_image']['url'] ) . '); background-size: cover; background-position: center;';
			$card_classes[] = 'has-background-image';
		} elseif ( 'meta' === $settings['background_type'] && ! empty( $data['background_image'] ) ) {
			$card_style .= 'background-image: url(' . esc_url( $data['background_image'] ) . '); background-size: cover; background-position: center;';
			$card_classes[] = 'has-background-image';
		}

		// Hover animation
		if ( ! empty( $settings['hover_animation'] ) ) {
			$card_classes[] = 'elementor-animation-' . $settings['hover_animation'];
		}

		// Start output
		echo '<div class="typology-card-container">';

		// Card wrapper
		$card_tag = ! empty( $link_url ) ? 'a' : 'div';
		$link_attrs = '';
		if ( ! empty( $link_url ) ) {
			$link_attrs = 'href="' . esc_url( $link_url ) . '"';
			
			// Add external link attributes if needed
			if ( 'custom' === $settings['link_type'] ) {
				if ( ! empty( $settings['custom_link']['is_external'] ) ) {
					$link_attrs .= ' target="_blank"';
				}
				if ( ! empty( $settings['custom_link']['nofollow'] ) ) {
					$link_attrs .= ' rel="nofollow"';
				}
			}
		}

		?>
		<<?php echo esc_attr( $card_tag ); ?> class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" style="<?php echo esc_attr( $card_style ); ?>" <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			
			<?php if ( 'yes' === $settings['show_icon'] && ! empty( $data['icon'] ) ) : ?>
				<div class="typology-card-icon">
					<?php $this->render_icon( $data['icon'], $data['icon_bg_color'] ); ?>
				</div>
			<?php endif; ?>

			<div class="typology-card-content">
				<h3 class="typology-card-title"><?php echo esc_html( $data['title'] ); ?></h3>
				<?php if ( 'yes' === $settings['show_subtitle'] && ! empty( $data['subtitle'] ) ) : ?>
					<p class="typology-card-subtitle"><?php echo esc_html( $data['subtitle'] ); ?></p>
				<?php endif; ?>
			</div>

		</<?php echo esc_attr( $card_tag ); ?>>
		<?php

		echo '</div>';
	}

	/**
	 * Render icon based on type (font icon, SVG, or image).
	 *
	 * @since 2.2.0
	 * @param array  $icon     Icon data.
	 * @param string $bg_color Background color for meta icons.
	 */
	private function render_icon( $icon, $bg_color ) {
		if ( empty( $icon['value'] ) ) {
			return;
		}

		// Check if it's a font icon or SVG
		if ( ! empty( $icon['library'] ) ) {
			// Font icon or SVG from Elementor
			\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		} else {
			// Image URL (from meta field)
			echo '<img src="' . esc_url( $icon['value'] ) . '" alt="" decoding="async">';
		}
	}

	/**
	 * Get link URL based on settings.
	 *
	 * @since 2.2.0
	 * @param array $data     Typology data.
	 * @param array $settings Widget settings.
	 * @return string Link URL.
	 */
	private function get_link_url( $data, $settings ) {
		switch ( $settings['link_type'] ) {
			case 'typology':
				return $data['url'];

			case 'custom':
				return ! empty( $settings['custom_link']['url'] ) ? $settings['custom_link']['url'] : '';

			default:
				return '';
		}
	}
}