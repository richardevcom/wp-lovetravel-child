<?php
/**
 * Team Member Grid Widget
 *
 * Custom Elementor widget for displaying multiple team members in a responsive grid.
 * Shows 3 members per row on desktop, 1 per row on mobile.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Team Member Grid Widget Class
 *
 * Displays multiple team members in a responsive grid layout.
 * Uses the same styling as the individual Team Member Card widget.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 */
class LoveTravelChild_Team_Member_Grid_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since  2.2.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-child-team-member-grid';
	}

	/**
	 * Get widget title.
	 *
	 * @since  2.2.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Team Member Grid', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  2.2.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
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
	 * Get widget keywords.
	 *
	 * @since  2.2.0
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'team', 'member', 'grid', 'staff', 'employees', 'people' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since  2.2.0
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register content controls.
	 *
	 * @since  2.2.0
	 */
	private function register_content_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Team Members', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Members Selection
		$this->add_control(
			'selected_members',
			array(
				'label'       => esc_html__( 'Select Members', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $this->get_members_options(),
				'default'     => array(),
				'multiple'    => true,
				'description' => esc_html__( 'Select team members to display. Leave empty to show all members.', 'lovetravel-child' ),
			)
		);

		// Number of Members
		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'Number of Members', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 50,
				'condition' => array(
					'selected_members' => array(),
				),
			)
		);

		// Order By
		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'title'      => esc_html__( 'Name', 'lovetravel-child' ),
					'date'       => esc_html__( 'Date Created', 'lovetravel-child' ),
					'modified'   => esc_html__( 'Last Modified', 'lovetravel-child' ),
					'menu_order' => esc_html__( 'Menu Order', 'lovetravel-child' ),
					'rand'       => esc_html__( 'Random', 'lovetravel-child' ),
				),
				'condition' => array(
					'selected_members' => array(),
				),
			)
		);

		// Order
		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'lovetravel-child' ),
					'DESC' => esc_html__( 'Descending', 'lovetravel-child' ),
				),
				'condition' => array(
					'selected_members' => array(),
					'orderby!' => 'rand',
				),
			)
		);

		// Show Social Links
		$this->add_control(
			'show_social_links',
			array(
				'label'   => esc_html__( 'Show Social Links', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		// Show About Text
		$this->add_control(
			'show_about',
			array(
				'label'   => esc_html__( 'Show About Text', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		// About Text Excerpt Length
		$this->add_control(
			'about_excerpt_length',
			array(
				'label'     => esc_html__( 'Excerpt Length (words)', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 15,
				'min'       => 5,
				'max'       => 50,
				'condition' => array(
					'show_about' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style controls.
	 *
	 * @since  2.2.0
	 */
	private function register_style_controls() {
		// Grid Layout Section
		$this->start_controls_section(
			'grid_layout_section',
			array(
				'label' => esc_html__( 'Grid Layout', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'lovetravel-child' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .team-member-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => esc_html__( 'Columns Gap', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => esc_html__( 'Rows Gap', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Individual Card Styles (inherit from Team Member Card widget)
		$this->add_card_style_controls();
	}

	/**
	 * Add card style controls (same as individual card widget).
	 *
	 * @since  2.2.0
	 */
	private function add_card_style_controls() {
		// Card Style Section
		$this->start_controls_section(
			'card_style_section',
			array(
				'label' => esc_html__( 'Card Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '30',
					'right'  => '25',
					'bottom' => '30',
					'left'   => '25',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'   => esc_html__( 'Background Color', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .team-member-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .team-member-card',
			)
		);

		$this->add_responsive_control(
			'card_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '12',
					'right'  => '12',
					'bottom' => '12',
					'left'   => '12',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .team-member-card',
				'fields_options' => array(
					'box_shadow_type' => array(
						'default' => 'yes',
					),
					'box_shadow' => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 8,
							'blur'       => 25,
							'spread'     => 0,
							'color'      => 'rgba(0, 0, 0, 0.1)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get members options for select control.
	 *
	 * @since  2.2.0
	 * @return array Members options.
	 */
	private function get_members_options() {
		$options = array();

		$members_query = lovetravelChild_get_members_query();
		
		if ( $members_query->have_posts() ) {
			while ( $members_query->have_posts() ) {
				$members_query->the_post();
				$options[ get_the_ID() ] = get_the_title();
			}
			wp_reset_postdata();
		}

		return $options;
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * @since  2.2.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Prepare query arguments
		$query_args = array(
			'post_type'      => 'lovetravel_member',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $settings['selected_members'] ) ? -1 : $settings['posts_per_page'],
		);

		// If specific members are selected
		if ( ! empty( $settings['selected_members'] ) ) {
			$query_args['post__in'] = $settings['selected_members'];
			$query_args['orderby'] = 'post__in'; // Maintain selected order
		} else {
			// Use settings for order
			$query_args['orderby'] = $settings['orderby'];
			if ( $settings['orderby'] !== 'rand' ) {
				$query_args['order'] = $settings['order'];
			}
		}

		// Execute query
		$members_query = new WP_Query( $query_args );
		
		if ( ! $members_query->have_posts() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="team-member-grid-placeholder">';
				echo '<p>' . esc_html__( 'No team members found. Please add some team members first.', 'lovetravel-child' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Start output
		echo '<div class="team-member-grid">';
		
		while ( $members_query->have_posts() ) {
			$members_query->the_post();
			
			// Get member data
			$member_data = lovetravelChild_get_member_data( get_the_ID() );
			
			if ( ! $member_data ) {
				continue;
			}

			// Render individual member card
			echo '<div class="team-member-card">';
			
			// Avatar
			if ( $member_data['avatar_url'] ) {
				echo '<div class="team-member-avatar">';
				echo '<img src="' . esc_url( $member_data['avatar_url'] ) . '" alt="' . esc_attr( $member_data['name'] ) . '">';
				echo '</div>';
			}

			// Social links overlay
			if ( $settings['show_social_links'] === 'yes' && ! empty( $member_data['social_networks'] ) ) {
				echo '<div class="team-member-social-overlay">';
				foreach ( $member_data['social_networks'] as $network ) {
					if ( ! empty( $network['type'] ) && ! empty( $network['url'] ) ) {
						$icon_class = function_exists( 'lovetravelChild_get_social_network_icon' ) 
							? lovetravelChild_get_social_network_icon( $network['type'] ) 
							: 'fas fa-link';
						$label = ucfirst( $network['type'] );
						
						echo '<a href="' . esc_url( $network['url'] ) . '" target="_blank" rel="noopener" aria-label="' . 
							 esc_attr( sprintf( __( '%s profile', 'lovetravel-child' ), $label ) ) . '">';
						echo '<i class="' . esc_attr( $icon_class ) . '"></i>';
						echo '</a>';
					}
				}
				echo '</div>';
			}

			// Content area
			echo '<div class="team-member-content">';

			// Name
			if ( $member_data['name'] ) {
				echo '<h3 class="team-member-name">' . esc_html( $member_data['name'] ) . '</h3>';
			}

			// Occupation with briefcase icon
			if ( $member_data['occupation'] ) {
				echo '<div class="team-member-occupation"><i class="fas fa-briefcase"></i>' . esc_html( $member_data['occupation'] ) . '</div>';
			}

			// About text with excerpt
			if ( $settings['show_about'] === 'yes' && ! empty( $member_data['about'] ) ) {
				$about_text = $member_data['about'];
				$excerpt_length = $settings['about_excerpt_length'] ? $settings['about_excerpt_length'] : 15;
				$excerpt = wp_trim_words( $about_text, $excerpt_length, '...' );
				$needs_expand = str_word_count( strip_tags( $about_text ) ) > $excerpt_length;
				
				echo '<div class="team-member-about">';
				echo '<div class="about-excerpt">' . wp_kses_post( wpautop( $excerpt ) );
				if ( $needs_expand ) {
					echo ' <a href="#" class="read-more-link">Read more <i class="fas fa-chevron-down"></i></a>';
				}
				echo '</div>';
				if ( $needs_expand ) {
					echo '<div class="about-full">' . wp_kses_post( wpautop( $about_text ) ) . ' <a href="#" class="read-less-link">Read less <i class="fas fa-chevron-up"></i></a></div>';
				}
				echo '</div>';
			}

			echo '</div>'; // Close content

			echo '</div>'; // .team-member-card
		}
		
		wp_reset_postdata();
		
		echo '</div>'; // .team-member-grid
	}

}