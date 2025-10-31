<?php
/**
 * Team Member Card Widget
 *
 * Custom Elementor widget for displaying team member cards with avatar,
 * name, occupation, and social links. Optimized for 3 cards per row.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Team Member Card Widget Class
 *
 * Displays a single team member card with avatar, name, occupation, and social links.
 * Designed for 3-column layouts on desktop, single column on mobile.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 */
class LoveTravelChild_Team_Member_Card_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since  2.2.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-child-team-member-card';
	}

	/**
	 * Get widget title.
	 *
	 * @since  2.2.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Team Member Card', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  2.2.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-person';
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
		return array( 'team', 'member', 'profile', 'card', 'person', 'staff', 'employee' );
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
				'label' => esc_html__( 'Content', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Member Selection
		$this->add_control(
			'member_id',
			array(
				'label'   => esc_html__( 'Select Member', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_members_options(),
				'default' => '',
			)
		);

		// Override Name (optional)
		$this->add_control(
			'override_name',
			array(
				'label'       => esc_html__( 'Override Name', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Leave empty to use member name', 'lovetravel-child' ),
			)
		);

		// Override Occupation (optional)
		$this->add_control(
			'override_occupation',
			array(
				'label'       => esc_html__( 'Override Occupation', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Leave empty to use member occupation', 'lovetravel-child' ),
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
				'default'   => 20,
				'min'       => 5,
				'max'       => 100,
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

		// Avatar Style Section
		$this->start_controls_section(
			'avatar_style_section',
			array(
				'label' => esc_html__( 'Avatar Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// Avatar border radius (removed size control for full-width)
		$this->add_responsive_control(
			'avatar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16, // Matches card border radius for top corners
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-avatar' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;', // Only top corners
				),
			)
		);

		$this->end_controls_section();

		// Name Style Section
		$this->start_controls_section(
			'name_style_section',
			array(
				'label' => esc_html__( 'Name Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .team-member-name',
				'fields_options' => array(
					'typography'      => array( 'default' => 'yes' ),
					'font_family'     => array( 'default' => 'Inter' ),
					'font_size'       => array( 'default' => array( 'size' => 20, 'unit' => 'px' ) ),
					'font_weight'     => array( 'default' => '700' ),
					'line_height'     => array( 'default' => array( 'size' => 1.3, 'unit' => 'em' ) ),
				),
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'   => esc_html__( 'Color', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#2c3e50',
				'selectors' => array(
					'{{WRAPPER}} .team-member-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'name_margin_bottom',
			array(
				'label'      => esc_html__( 'Spacing Below', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Occupation Style Section
		$this->start_controls_section(
			'occupation_style_section',
			array(
				'label' => esc_html__( 'Occupation Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'occupation_typography',
				'selector' => '{{WRAPPER}} .team-member-occupation',
				'fields_options' => array(
					'typography'      => array( 'default' => 'yes' ),
					'font_family'     => array( 'default' => 'Inter' ),
					'font_size'       => array( 'default' => array( 'size' => 14, 'unit' => 'px' ) ),
					'font_weight'     => array( 'default' => '500' ),
					'line_height'     => array( 'default' => array( 'size' => 1.4, 'unit' => 'em' ) ),
				),
			)
		);

		$this->add_control(
			'occupation_color',
			array(
				'label'   => esc_html__( 'Color', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#7f8c8d',
				'selectors' => array(
					'{{WRAPPER}} .team-member-occupation' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'occupation_margin_bottom',
			array(
				'label'      => esc_html__( 'Spacing Below', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-occupation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Social Links Style Section
		$this->start_controls_section(
			'social_style_section',
			array(
				'label'     => esc_html__( 'Social Links Style', 'lovetravel-child' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_social_links' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'social_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 14,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-social a i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_icon_spacing',
			array(
				'label'      => esc_html__( 'Spacing Between Icons', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .team-member-social a:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'social_icon_color',
			array(
				'label'   => esc_html__( 'Icon Color', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#95a5a6',
				'selectors' => array(
					'{{WRAPPER}} .team-member-social a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_icon_hover_color',
			array(
				'label'   => esc_html__( 'Icon Hover Color', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#3498db',
				'selectors' => array(
					'{{WRAPPER}} .team-member-social a:hover' => 'color: {{VALUE}};',
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
		$options = array( '' => esc_html__( 'Select a Member', 'lovetravel-child' ) );

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
		
		// Get member ID
		$member_id = $settings['member_id'];
		
		if ( empty( $member_id ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="team-member-card-placeholder">';
				echo '<p>' . esc_html__( 'Please select a team member to display.', 'lovetravel-child' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Get member data
		$member_data = lovetravelChild_get_member_data( $member_id );
		
		if ( ! $member_data ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="team-member-card-placeholder">';
				echo '<p>' . esc_html__( 'Selected member not found.', 'lovetravel-child' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Override values if provided
		$display_name = ! empty( $settings['override_name'] ) ? $settings['override_name'] : $member_data['name'];
		$display_occupation = ! empty( $settings['override_occupation'] ) ? $settings['override_occupation'] : $member_data['occupation'];

		// Start output
		echo '<div class="team-member-card">';
		
		// Avatar
		if ( $member_data['avatar_url'] ) {
			echo '<div class="team-member-avatar">';
			echo '<img src="' . esc_url( $member_data['avatar_url'] ) . '" alt="' . esc_attr( $display_name ) . '" />';
			echo '</div>';
		}

		// Social links overlay (positioned between avatar and content)
		if ( $settings['show_social_links'] === 'yes' && ! empty( $member_data['social_networks'] ) ) {
			echo '<div class="team-member-social-overlay">';
			foreach ( $member_data['social_networks'] as $network ) {
				if ( ! empty( $network['url'] ) && ! empty( $network['type'] ) ) {
					$icon_class = function_exists( 'lovetravelChild_get_social_network_icon' ) 
						? lovetravelChild_get_social_network_icon( $network['type'] ) 
						: 'fas fa-link';
					echo '<a href="' . esc_url( $network['url'] ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( ucfirst( $network['type'] ) ) . '">';
					echo '<i class="' . esc_attr( $icon_class ) . '"></i>';
					echo '</a>';
				}
			}
			echo '</div>';
		}

		// Content area
		echo '<div class="team-member-content">';
		
		// Name
		if ( $display_name ) {
			echo '<h3 class="team-member-name">' . esc_html( $display_name ) . '</h3>';
		}

		// Occupation with briefcase icon
		if ( $display_occupation ) {
			echo '<div class="team-member-occupation"><i class="fas fa-briefcase"></i>' . esc_html( $display_occupation ) . '</div>';
		}

		// About text with excerpt and expand functionality
		if ( $settings['show_about'] === 'yes' && ! empty( $member_data['about'] ) ) {
			$about_text = $member_data['about'];
			$excerpt_length = $settings['about_excerpt_length'] ? $settings['about_excerpt_length'] : 20;
			$excerpt = wp_trim_words( $about_text, $excerpt_length, '...' );
			$needs_expand = str_word_count( strip_tags( $about_text ) ) > $excerpt_length;
			
			echo '<div class="team-member-about">';
			echo '<div class="about-excerpt">' . wp_kses_post( wpautop( $excerpt ) );
			if ( $needs_expand ) {
				echo ' <a href="#" class="read-more-link">Read more <i class="fas fa-chevron-down"></i></a>';
			}
			echo '</div>';
			if ( $needs_expand ) {
				// Use CSS class instead of inline style
				echo '<div class="about-full">' . wp_kses_post( wpautop( $about_text ) ) . ' <a href="#" class="read-less-link">Read less <i class="fas fa-chevron-up"></i></a></div>';
			}
			echo '</div>';
		}

		echo '</div>'; // Close content

		echo '</div>'; // .team-member-card
	}

}