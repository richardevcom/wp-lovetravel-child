<?php
/**
 * Elementor Search Widget Extension
 *
 * Extends the nd-travel Search widget to add Month taxonomy support.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

/**
 * Elementor Search Widget Extension class.
 *
 * Injects Month taxonomy controls into the Search widget and modifies rendering.
 *
 * @since 2.0.0
 */
class LoveTravelChildElementorSearchWidgetExtension {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// Controls will be registered via Loader
	}

	/**
	 * Inject Month taxonomy controls into Search widget.
	 *
	 * Adds new content section with show/hide toggle, label, and icon controls
	 * for the Month taxonomy, following same pattern as durations/difficulties/minages.
	 *
	 * @since  2.0.0
	 * @param  \Elementor\Controls_Stack $element The element instance.
	 * @param  array                     $args    Section arguments.
	 * @return void
	 */
	public function addMonthControls( $element, $args ) {
		// Add Month taxonomy section after Min Ages section
		$element->start_controls_section(
			'content_section_months',
			array(
				'label' => esc_html__( 'Months', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Show/Hide toggle
		$element->add_control(
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

		// Label control
		$element->add_control(
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

		// Icon control
		$element->add_control(
			'search_months_icon',
			array(
				'label'     => esc_html__( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'search_months_show' => array( 'yes' ),
				),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Modify Search widget render output to include Month taxonomy.
	 *
	 * Intercepts widget render to add Month taxonomy variables and extend
	 * the taxonomy loop in layout-1.php.
	 *
	 * @since  2.0.0
	 * @param  string $content Widget HTML output.
	 * @param  object $widget  Widget instance.
	 * @return string          Modified HTML output.
	 */
	public function modifySearchWidgetRender( $content, $widget ) {
		// Only target the Search widget
		if ( 'Search' !== $widget->get_name() ) {
			return $content;
		}

		// Get widget settings
		$settings = $widget->get_settings_for_display();

		// Extract Month taxonomy settings (if they exist)
		$month_show  = isset( $settings['search_months_show'] ) ? $settings['search_months_show'] : '';
		$month_title = isset( $settings['search_months_title'] ) ? $settings['search_months_title'] : esc_html__( 'Months', 'lovetravel-child' );
		$month_icon  = isset( $settings['search_months_icon'] ) ? $settings['search_months_icon'] : '';

		// If months disabled, return original content
		if ( 'yes' !== $month_show ) {
			return $content;
		}

		// Build Month taxonomy HTML following plugin's pattern
		$month_html = $this->buildMonthTaxonomyHtml( $month_title, $month_icon, $settings );

		// Inject Month HTML before the submit button
		// Search for the submit button div and insert Month HTML before it
		$submit_pattern = '/<div id="nd_travel_search_components_submit"/';
		if ( preg_match( $submit_pattern, $content ) ) {
			$content = preg_replace(
				$submit_pattern,
				$month_html . '<div id="nd_travel_search_components_submit"',
				$content,
				1
			);
		}

		return $content;
	}

	/**
	 * Build Month taxonomy HTML select dropdown.
	 *
	 * Generates HTML markup for Month taxonomy select field following
	 * the same structure as other taxonomies in the search widget.
	 *
	 * @since  2.0.0
	 * @param  string $label    Field label text.
	 * @param  mixed  $icon     Icon array or string.
	 * @param  array  $settings Widget settings for column width.
	 * @return string           HTML markup for Month select field.
	 */
	private function buildMonthTaxonomyHtml( $label, $icon, $settings ) {
		$columns = isset( $settings['search_columns'] ) ? $settings['search_columns'] : 'nd_elements_width_25_percentage';

		// Build label
		$label_html = '';
		if ( ! empty( $label ) ) {
			$label_html = '<label class="nd_travel_search_components_label">' . esc_html( $label ) . '</label>';
		}

		// Build icon (following plugin's pattern)
		$icon_html = '';
		if ( ! empty( $icon ) ) {
			if ( is_array( $icon ) && isset( $icon['value'] ) ) {
				if ( is_array( $icon['value'] ) && isset( $icon['value']['url'] ) ) {
					// SVG/Image icon
					$icon_html = '<img class="nd_travel_position_absolute nd_travel_top_0 nd_travel_left_0 nd_travel_search_components_icon" src="' . esc_url( $icon['value']['url'] ) . '">';
				} else {
					// Font icon
					$icon_html = '<i class=" nd_travel_position_absolute nd_travel_top_0 nd_travel_left_0 nd_travel_search_components_icon ' . esc_attr( $icon['value'] ) . '"></i>';
				}
			}
		}

		// Get Month taxonomy terms
		$terms = get_terms(
			array(
				'taxonomy'   => 'nd_travel_cpt_1_tax_4',
				'hide_empty' => false,
			)
		);

		// Build options HTML
		$options_html = '<option value="">' . esc_html__( 'All Months', 'lovetravel-child' ) . '</option>';
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$options_html .= '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
			}
		}

		// Build complete Month select HTML
		$html = '
<div id="nd_travel_search_components_months" class="nd_travel_float_left nd_travel_position_relative nd_travel_width_100_percentage_responsive nd_travel_box_sizing_border_box nd_travel_search_components_column ' . esc_attr( $columns ) . ' ">

  ' . $icon_html . '

  ' . $label_html . '
  
  <select class="nd_travel_section nd_travel_search_components_field" name="nd_travel_cpt_1_tax_4">
    ' . $options_html . '
  </select>

</div>';

		return $html;
	}
}
