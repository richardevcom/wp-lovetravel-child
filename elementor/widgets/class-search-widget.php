<?php
/**
 * Search Widget (Standalone)
 *
 * Custom Elementor widget for adventure search form with Month taxonomy integration.
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
 * Search Widget Class
 *
 * Standalone Elementor widget with built-in Month taxonomy support.
 * No dependency on nd-travel Search widget hooks.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 */
class LoveTravelChild_Search_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since  2.2.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-child-search';
	}

	/**
	 * Get widget title.
	 *
	 * @since  2.2.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Adventure Search', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  2.2.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-search';
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
	protected function _register_controls() {
		// Main Options Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Main Options', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_layout',
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
			'search_columns',
			array(
				'label'   => esc_html__( 'Columns', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'nd_elements_width_25_percentage',
				'options' => array(
					'nd_elements_width_100_percentage' => esc_html__( '1 Column', 'lovetravel-child' ),
					'nd_elements_width_50_percentage'  => esc_html__( '2 Columns', 'lovetravel-child' ),
					'nd_elements_width_33_percentage'  => esc_html__( '3 Columns', 'lovetravel-child' ),
					'nd_elements_width_25_percentage'  => esc_html__( '4 Columns', 'lovetravel-child' ),
					'nd_elements_width_20_percentage'  => esc_html__( '5 Columns', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'search_submit_text',
			array(
				'label'       => esc_html__( 'Submit Button Text', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'SEARCH', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Enter submit button text', 'lovetravel-child' ),
			)
		);

		$this->end_controls_section();

		// Keyword Section
		$this->start_controls_section(
			'content_section_keyword',
			array(
				'label' => esc_html__( 'Keyword', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_keyword_show',
			array(
				'label'        => esc_html__( 'Keyword', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_keyword_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Keyword', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_keyword_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_keyword_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_keyword_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// Destinations Section
		$this->start_controls_section(
			'content_section_destinations',
			array(
				'label' => esc_html__( 'Destinations', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_destination_show',
			array(
				'label'        => esc_html__( 'Destinations', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_destination_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Destinations', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_destination_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_destination_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_destination_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// Typologies Section
		$this->start_controls_section(
			'content_section_typologies',
			array(
				'label' => esc_html__( 'Typologies', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_typologies_show',
			array(
				'label'        => esc_html__( 'Typologies', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_typologies_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Typologies', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_typologies_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_typologies_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_typologies_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// Durations Section
		$this->start_controls_section(
			'content_section_durations',
			array(
				'label' => esc_html__( 'Durations', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_durations_show',
			array(
				'label'        => esc_html__( 'Durations', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_durations_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Durations', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_durations_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_durations_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_durations_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// Difficulties Section
		$this->start_controls_section(
			'content_section_difficulties',
			array(
				'label' => esc_html__( 'Difficulties', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_difficulties_show',
			array(
				'label'        => esc_html__( 'Difficulties', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_difficulties_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Difficulties', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_difficulties_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_difficulties_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_difficulties_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// Min Ages Section
		$this->start_controls_section(
			'content_section_minages',
			array(
				'label' => esc_html__( 'Min Ages', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_minages_show',
			array(
				'label'        => esc_html__( 'Min Ages', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_minages_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Min Ages', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_minages_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_minages_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_minages_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// Months Section (CHILD THEME ADDITION)
		$this->start_controls_section(
			'content_section_months',
			array(
				'label' => esc_html__( 'Months', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search_months_show',
			array(
				'label'        => esc_html__( 'Months', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_months_title',
			array(
				'label'       => esc_html__( 'Label', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Months', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Type your title here', 'lovetravel-child' ),
				'condition'   => array(
					'search_months_show' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'search_months_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_months_show' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		// STYLE TAB - Content Section
		$this->start_controls_section(
			'style_content',
			array(
				'label' => esc_html__( 'Content', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'style_content_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_form' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'style_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'style_content_border',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_form',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'style_content_box_shadow',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_form',
			)
		);

		$this->add_control(
			'style_content_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_form' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// STYLE TAB - Label Section
		$this->start_controls_section(
			'style_label',
			array(
				'label' => esc_html__( 'Label', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'style_label_text_typography',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_label',
			)
		);

		$this->add_control(
			'style_label_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		// STYLE TAB - Fields Section
		$this->start_controls_section(
			'style_field',
			array(
				'label' => esc_html__( 'Fields', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'style_field_text_typography',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_field',
			)
		);

		$this->add_control(
			'style_field_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_field' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'style_field_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_field' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'style_field_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'style_field_border',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_field',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'style_field_box_shadow',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_field',
			)
		);

		$this->add_control(
			'style_field_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_field' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// STYLE TAB - Submit Section
		$this->start_controls_section(
			'style_submit',
			array(
				'label' => esc_html__( 'Submit', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'style_submit_text_typography',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_submit',
			)
		);

		$this->add_control(
			'style_submit_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_submit' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'style_submit_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_submit' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'style_submit_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'style_submit_border',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_submit',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'style_submit_box_shadow',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_submit',
			)
		);

		$this->add_control(
			'style_submit_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_submit' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// STYLE TAB - Columns Section
		$this->start_controls_section(
			'style_column',
			array(
				'label' => esc_html__( 'Columns', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'style_column_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_column' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'style_column_border',
				'selector' => '{{WRAPPER}} .nd_travel_search_components_column',
			)
		);

		$this->end_controls_section();

		// STYLE TAB - Icons Section
		$this->start_controls_section(
			'style_icon',
			array(
				'label' => esc_html__( 'Icons', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'style_icon_text_color',
			array(
				'label'     => esc_html__( 'Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nd_travel_search_components_icon' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'style_icon_width',
			array(
				'label'      => esc_html__( 'Width', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_icon' => 'width: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'style_icon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .nd_travel_search_components_icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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

		// Extract settings
		$columns     = ! empty( $settings['search_columns'] ) ? $settings['search_columns'] : 'nd_elements_width_25_percentage';
		$submit_text = ! empty( $settings['search_submit_text'] ) ? $settings['search_submit_text'] : esc_html__( 'Search', 'lovetravel-child' );

		// Get search page URL from nd-travel plugin settings
		$archive_url = function_exists( 'nd_travel_search_page' ) ? nd_travel_search_page() : home_url( '/' );

		// Start form
		?>
		<div class="nd_travel_section nd_travel_search_components_form">
			<form method="get" action="<?php echo esc_url( $archive_url ); ?>">
				
				<?php
				// Keyword (text input)
				if ( 'yes' === $settings['search_keyword_show'] ) {
					$this->render_keyword_field(
						$settings['search_keyword_title'],
						$settings['search_keyword_icon'],
						$columns
					);
				}

				// Destinations (special rendering - CPT not taxonomy)
				if ( 'yes' === $settings['search_destination_show'] ) {
					$this->render_destinations_field(
						$settings['search_destination_title'],
						$settings['search_destination_icon'],
						$columns
					);
				}

				// Typologies (special rendering - CPT not taxonomy)
				if ( 'yes' === $settings['search_typologies_show'] ) {
					$this->render_typologies_field(
						$settings['search_typologies_title'],
						$settings['search_typologies_icon'],
						$columns
					);
				}

				// Durations
				if ( 'yes' === $settings['search_durations_show'] ) {
					$this->render_taxonomy_field(
						'durations',
						'nd_travel_cpt_1_tax_1',
						$settings['search_durations_title'],
						$settings['search_durations_icon'],
						$columns
					);
				}

				// Difficulties
				if ( 'yes' === $settings['search_difficulties_show'] ) {
					$this->render_taxonomy_field(
						'difficulties',
						'nd_travel_cpt_1_tax_2',
						$settings['search_difficulties_title'],
						$settings['search_difficulties_icon'],
						$columns
					);
				}

				// Min Ages
				if ( 'yes' === $settings['search_minages_show'] ) {
					$this->render_taxonomy_field(
						'minages',
						'nd_travel_cpt_1_tax_3',
						$settings['search_minages_title'],
						$settings['search_minages_icon'],
						$columns
					);
				}

				// Months (CHILD THEME ADDITION)
				if ( 'yes' === $settings['search_months_show'] ) {
					$this->render_taxonomy_field(
						'months',
						'nd_travel_cpt_1_tax_4',
						$settings['search_months_title'],
						$settings['search_months_icon'],
						$columns
					);
				}
				?>

				<!-- Submit Button -->
				<div id="nd_travel_search_components_submit" class="nd_travel_float_left nd_travel_position_relative nd_travel_width_100_percentage_responsive nd_travel_box_sizing_border_box nd_travel_search_components_column <?php echo esc_attr( $columns ); ?>">
					<input class="nd_travel_section nd_travel_search_components_submit" type="submit" value="<?php echo esc_attr( $settings['search_submit_text'] ); ?>">
				</div>

			</form>
		</div>
		<?php
	}

	/**
	 * Render keyword text input field.
	 *
	 * @since  2.2.0
	 * @param  string $label      Field label.
	 * @param  mixed  $icon       Icon array or string.
	 * @param  string $columns    Column width class.
	 */
	private function render_keyword_field( $label, $icon, $columns ) {
		// Build label and icon HTML
		$label_html = $this->build_label_html( $label );
		$icon_html  = $this->build_icon_html( $icon );

		// Output field HTML
		?>
		<div id="nd_travel_search_components_keyword" class="nd_travel_float_left nd_travel_position_relative nd_travel_width_100_percentage_responsive nd_travel_box_sizing_border_box nd_travel_search_components_column <?php echo esc_attr( $columns ); ?>">
			<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<input class="nd_travel_section nd_travel_search_components_field" type="text" name="s" placeholder="<?php echo esc_attr( $label ); ?>">
		</div>
		<?php
	}

	/**
	 * Render destinations select field (nd_travel_cpt_3 posts).
	 *
	 * @since  2.2.0
	 * @param  string $label      Field label.
	 * @param  mixed  $icon       Icon array or string.
	 * @param  string $columns    Column width class.
	 */
	private function render_destinations_field( $label, $icon, $columns ) {
		// Build label and icon HTML (reuse helper)
		$label_html = $this->build_label_html( $label );
		$icon_html  = $this->build_icon_html( $icon );

		// Query destination posts (nd_travel_cpt_3)
		$destinations_query = new \WP_Query(
			array(
				'post_type'      => 'nd_travel_cpt_3',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		// Build options HTML
		$options_html = '<option value="0">' . esc_html__( 'All Destinations', 'lovetravel-child' ) . '</option>';

		if ( $destinations_query->have_posts() ) {
			while ( $destinations_query->have_posts() ) {
				$destinations_query->the_post();
				$destination_id = get_the_ID();
				$parent_id      = get_post_meta( $destination_id, 'nd_travel_meta_box_parent_destination', true );

				// Only show parent destinations (not children)
				if ( empty( $parent_id ) || 0 == $parent_id ) {
					$options_html .= '<option value="' . esc_attr( $destination_id ) . '">' . esc_html( get_the_title() ) . '</option>';

					// Check if this destination has children
					if ( function_exists( 'nd_travel_get_destinations_with_parent' ) ) {
						$children_ids = nd_travel_get_destinations_with_parent( $destination_id );

						if ( ! empty( $children_ids ) && is_array( $children_ids ) ) {
							foreach ( $children_ids as $child_id ) {
								$child_parent_id = get_post_meta( $child_id, 'nd_travel_meta_box_parent_destination', true );
								if ( $child_parent_id == $destination_id ) {
									$options_html .= '<option value="' . esc_attr( $child_id ) . '">&nbsp;&nbsp;- ' . esc_html( get_the_title( $child_id ) ) . '</option>';
								}
							}
						}
					}
				}
			}
		}
		wp_reset_postdata();

		// Output field HTML
		?>
		<div id="nd_travel_search_components_destinations" class="nd_travel_float_left nd_travel_position_relative nd_travel_width_100_percentage_responsive nd_travel_box_sizing_border_box nd_travel_search_components_column <?php echo esc_attr( $columns ); ?>">
			<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<select class="nd_travel_section nd_travel_search_components_field" name="nd_travel_archive_form_destinations">
				<?php echo $options_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Render typologies select field (nd_travel_cpt_2 posts).
	 *
	 * @since  2.2.0
	 * @param  string $label      Field label.
	 * @param  mixed  $icon       Icon array or string.
	 * @param  string $columns    Column width class.
	 */
	private function render_typologies_field( $label, $icon, $columns ) {
		// Build label and icon HTML (reuse helper)
		$label_html = $this->build_label_html( $label );
		$icon_html  = $this->build_icon_html( $icon );

		// Query typology posts (nd_travel_cpt_2)
		$typologies_query = new \WP_Query(
			array(
				'post_type'      => 'nd_travel_cpt_2',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		// Build options HTML
		$options_html = '<option value="">' . esc_html__( 'All Typologies', 'lovetravel-child' ) . '</option>';

		if ( $typologies_query->have_posts() ) {
			while ( $typologies_query->have_posts() ) {
				$typologies_query->the_post();
				$typology_id = get_the_ID();
				$options_html .= '<option value="' . esc_attr( $typology_id ) . '">' . esc_html( get_the_title() ) . '</option>';
			}
		}
		wp_reset_postdata();

		// Output field HTML
		?>
		<div id="nd_travel_search_components_typologies" class="nd_travel_float_left nd_travel_position_relative nd_travel_width_100_percentage_responsive nd_travel_box_sizing_border_box nd_travel_search_components_column <?php echo esc_attr( $columns ); ?>">
			<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<select class="nd_travel_section nd_travel_search_components_field" name="nd_travel_typology_slug">
				<?php echo $options_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Render taxonomy select field (for standard taxonomies).
	 *
	 * @since  2.2.0
	 * @param  string $field_id   Field identifier.
	 * @param  string $taxonomy   Taxonomy slug.
	 * @param  string $label      Field label.
	 * @param  mixed  $icon       Icon array or string.
	 * @param  string $columns    Column width class.
	 */
	private function render_taxonomy_field( $field_id, $taxonomy, $label, $icon, $columns ) {
		// Build label and icon HTML (reuse helpers)
		$label_html = $this->build_label_html( $label );
		$icon_html  = $this->build_icon_html( $icon );

		// Get taxonomy terms
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		$all_label = esc_html__( 'All', 'lovetravel-child' ) . ' ' . esc_html( $label );
		$options_html = '<option value="">' . esc_html( $all_label ) . '</option>';

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$options_html .= '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
			}
		}

		// Output field HTML
		?>
		<div id="nd_travel_search_components_<?php echo esc_attr( $field_id ); ?>" class="nd_travel_float_left nd_travel_position_relative nd_travel_width_100_percentage_responsive nd_travel_box_sizing_border_box nd_travel_search_components_column <?php echo esc_attr( $columns ); ?>">
			<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<select class="nd_travel_section nd_travel_search_components_field" name="<?php echo esc_attr( $taxonomy ); ?>">
				<?php echo $options_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Build label HTML.
	 *
	 * @since  2.2.0
	 * @param  string $label Label text.
	 * @return string Label HTML or empty string.
	 */
	private function build_label_html( $label ) {
		if ( ! empty( $label ) ) {
			return '<label class="nd_travel_search_components_label">' . esc_html( $label ) . '</label>';
		}
		return '';
	}

	/**
	 * Build icon HTML.
	 *
	 * @since  2.2.0
	 * @param  mixed $icon Icon array or string.
	 * @return string Icon HTML or empty string.
	 */
	private function build_icon_html( $icon ) {
		if ( ! empty( $icon ) && is_array( $icon ) && isset( $icon['value'] ) ) {
			if ( is_array( $icon['value'] ) && isset( $icon['value']['url'] ) ) {
				// SVG/Image icon
				return '<img class="nd_travel_position_absolute nd_travel_top_0 nd_travel_left_0 nd_travel_search_components_icon" src="' . esc_url( $icon['value']['url'] ) . '" alt="">';
			} else {
				// Font icon
				return '<i class="nd_travel_position_absolute nd_travel_top_0 nd_travel_left_0 nd_travel_search_components_icon ' . esc_attr( $icon['value'] ) . '"></i>';
			}
		}
		return '';
	}
}
