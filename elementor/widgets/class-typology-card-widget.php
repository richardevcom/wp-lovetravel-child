<?php
/**
 * Single Typology Card Widget
 *
 * Elementor widget for displaying a single typology card with icon and title.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Typology Card Widget class.
 *
 * @since 2.0.0
 */
class LoveTravelChildTypologyCardWidget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.0.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel_child_typology_card';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.0.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Typology Card', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.0.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 2.0.0
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'lovetravel-child' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 2.0.0
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'typology', 'card', 'lovetravel' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since 2.0.0
	 */
	protected function register_controls() {
		$this->registerContentControls();
		$this->registerStyleControls();
	}

	/**
	 * Register content tab controls.
	 *
	 * @since 2.0.0
	 */
	private function registerContentControls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Data Source
		$this->add_control(
			'data_source',
			array(
				'label'   => __( 'Data Source', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'typology',
				'options' => array(
					'typology' => __( 'Typology', 'lovetravel-child' ),
					'custom'   => __( 'Custom', 'lovetravel-child' ),
				),
			)
		);

		// Typology Selector
		$this->add_control(
			'typology_id',
			array(
				'label'       => __( 'Select Typology', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->getTypologyOptions(),
				'condition'   => array(
					'data_source' => 'typology',
				),
			)
		);

		// Dynamic Card Icon (Typology Mode) - syncs with post meta
		$this->add_control(
			'typology_card_icon_override',
			array(
				'label'       => __( 'Override Card Icon', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => __( 'Yes', 'lovetravel-child' ),
				'label_off'   => __( 'No', 'lovetravel-child' ),
				'default'     => '',
				'description' => __( 'Override the card icon saved in WordPress. Changes will NOT save to the post.', 'lovetravel-child' ),
				'condition'   => array(
					'data_source' => 'typology',
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'typology_card_icon_temp',
			array(
				'label'      => __( 'Temporary Card Icon', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => '',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'data_source',
							'operator' => '===',
							'value'    => 'typology',
						),
						array(
							'name'     => 'typology_card_icon_override',
							'operator' => '===',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'typology_card_icon_notice',
			array(
				'type'       => \Elementor\Controls_Manager::RAW_HTML,
				'raw'        => __( '<strong>Note:</strong> To permanently change the card icon, edit it in the WordPress post editor under "Card Settings" tab.', 'lovetravel-child' ),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'data_source',
							'operator' => '===',
							'value'    => 'typology',
						),
					),
				),
			)
		);

		// Dynamic Card Background (Typology Mode)
		$this->add_control(
			'typology_card_bg_override',
			array(
				'label'       => __( 'Override Card Background', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => __( 'Yes', 'lovetravel-child' ),
				'label_off'   => __( 'No', 'lovetravel-child' ),
				'default'     => '',
				'description' => __( 'Override the card background saved in WordPress. Changes will NOT save to the post.', 'lovetravel-child' ),
				'condition'   => array(
					'data_source' => 'typology',
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'typology_card_bg_temp',
			array(
				'label'      => __( 'Temporary Card Background', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => '',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'data_source',
							'operator' => '===',
							'value'    => 'typology',
						),
						array(
							'name'     => 'typology_card_bg_override',
							'operator' => '===',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'typology_card_bg_notice',
			array(
				'type'       => \Elementor\Controls_Manager::RAW_HTML,
				'raw'        => __( '<strong>Note:</strong> To permanently change the card background, edit it in the WordPress post editor under "Card Settings" tab.', 'lovetravel-child' ),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'data_source',
							'operator' => '===',
							'value'    => 'typology',
						),
					),
				),
			)
		);

		// Custom Title
		$this->add_control(
			'custom_title',
			array(
				'label'       => __( 'Title', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Typology Title', 'lovetravel-child' ),
				'label_block' => true,
				'condition'   => array(
					'data_source' => 'custom',
				),
			)
		);

		// Custom Icon
		$this->add_control(
			'custom_icon',
			array(
				'label'     => __( 'Icon', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'data_source' => 'custom',
				),
			)
		);

		// Custom Background Type
		$this->add_control(
			'custom_bg_type',
			array(
				'label'     => __( 'Background Type', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'color',
				'options'   => array(
					'color' => __( 'Color', 'lovetravel-child' ),
					'image' => __( 'Image', 'lovetravel-child' ),
				),
				'condition' => array(
					'data_source' => 'custom',
				),
			)
		);

		// Custom Background Image
		$this->add_control(
			'custom_bg_image',
			array(
				'label'      => __( 'Background Image', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => '',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'data_source',
							'operator' => '===',
							'value'    => 'custom',
						),
						array(
							'name'     => 'custom_bg_type',
							'operator' => '===',
							'value'    => 'image',
						),
					),
				),
			)
		);

		// Custom Background Color
		$this->add_control(
			'custom_bg_color',
			array(
				'label'      => __( 'Background Color', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::COLOR,
				'default'    => '#f5f5f5',
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'data_source',
							'operator' => '===',
							'value'    => 'custom',
						),
						array(
							'name'     => 'custom_bg_type',
							'operator' => '===',
							'value'    => 'color',
						),
					),
				),
			)
		);

		$this->end_controls_section();

		// Link Section
		$this->start_controls_section(
			'link_section',
			array(
				'label' => __( 'Link', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Link Type
		$this->add_control(
			'link_type',
			array(
				'label'   => __( 'Link Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'     => __( 'None', 'lovetravel-child' ),
					'typology' => __( 'Typology Page', 'lovetravel-child' ),
					'manual'   => __( 'Manual URL', 'lovetravel-child' ),
				),
			)
		);

		// Link Typology Selector
		$this->add_control(
			'link_typology_id',
			array(
				'label'       => __( 'Link to Typology', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->getTypologyOptions(),
				'condition'   => array(
					'link_type' => 'typology',
				),
			)
		);

		// Manual Link
		$this->add_control(
			'manual_link',
			array(
				'label'         => __( 'Link', 'lovetravel-child' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'lovetravel-child' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'     => array(
					'link_type' => 'manual',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style tab controls.
	 *
	 * @since 2.0.0
	 */
	private function registerStyleControls() {
		// Card Style
		$this->start_controls_section(
			'card_style',
			array(
				'label' => __( 'Card', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .lovetravel-child-typology-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_border_radius',
			array(
				'label'      => __( 'Border Radius', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => '12',
					'right'    => '12',
					'bottom'   => '12',
					'left'     => '12',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .lovetravel-child-typology-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_box_shadow',
				'label'    => __( 'Box Shadow', 'lovetravel-child' ),
				'selector' => '{{WRAPPER}} .lovetravel-child-typology-card',
			)
		);

		$this->add_control(
			'card_bg_overlay',
			array(
				'label'     => __( 'Background Overlay Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-child-typology-card::before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_bg_overlay_opacity',
			array(
				'label'     => __( 'Overlay Opacity', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 0.5,
				),
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-child-typology-card::before' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();

		// Icon Style
		$this->start_controls_section(
			'icon_style',
			array(
				'label' => __( 'Icon', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_width',
			array(
				'label'      => __( 'Width', 'lovetravel-child' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 30,
						'max' => 200,
					),
					'%'  => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 60,
				),
				'selectors'  => array(
					'{{WRAPPER}} .lovetravel-child-typology-card__icon img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Spacing', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'size' => 20,
				),
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-child-typology-card__icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Title Style
		$this->start_controls_section(
			'title_style',
			array(
				'label' => __( 'Title', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'lovetravel-child' ),
				'selector' => '{{WRAPPER}} .lovetravel-child-typology-card__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-child-typology-card__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get typology options for select control.
	 *
	 * @since 2.0.0
	 * @return array Typology options.
	 */
	private function getTypologyOptions() {
		$options = array( '' => __( 'Select Typology', 'lovetravel-child' ) );

		$args = array(
			'post_type'      => 'nd_travel_cpt_2',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		);

		$typologies = get_posts( $args );

		if ( ! empty( $typologies ) ) {
			foreach ( $typologies as $typology ) {
				$options[ $typology->ID ] = $typology->post_title;
			}
		}

		return $options;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 2.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get card data based on source
		if ( 'typology' === $settings['data_source'] && ! empty( $settings['typology_id'] ) ) {
			$card_data = $this->getTypologyData( $settings['typology_id'], $settings );
		} else {
			$card_data = $this->getCustomData( $settings );
		}

		if ( empty( $card_data ) ) {
			return;
		}

		// Get link data
		$link_data = $this->getLinkData( $settings );

		// Render card
		$this->renderCard( $card_data, $link_data );
	}

	/**
	 * Get typology data.
	 *
	 * @since 2.0.0
	 * @param int   $typology_id Typology post ID.
	 * @param array $settings    Widget settings (for overrides).
	 * @return array|null Card data or null.
	 */
	private function getTypologyData( $typology_id, $settings = array() ) {
		$typology = get_post( $typology_id );

		if ( ! $typology || 'nd_travel_cpt_2' !== $typology->post_type ) {
			return null;
		}

		// Get meta fields (child theme fields first, fallback to plugin fields)
		$icon = get_post_meta( $typology_id, 'lovetravel_child_meta_box_card_icon', true );
		if ( empty( $icon ) ) {
			$icon = get_post_meta( $typology_id, 'nd_travel_meta_box_cpt_2_icon', true );
		}

		$bg_image = get_post_meta( $typology_id, 'lovetravel_child_meta_box_card_bg_image', true );
		if ( empty( $bg_image ) ) {
			$bg_image = get_post_meta( $typology_id, 'nd_travel_meta_box_image_cpt_2', true );
		}

		// Check for Elementor overrides
		if ( ! empty( $settings['typology_card_icon_override'] ) && 'yes' === $settings['typology_card_icon_override'] ) {
			if ( ! empty( $settings['typology_card_icon_temp']['url'] ) ) {
				$icon = $settings['typology_card_icon_temp']['url'];
			}
		}

		if ( ! empty( $settings['typology_card_bg_override'] ) && 'yes' === $settings['typology_card_bg_override'] ) {
			if ( ! empty( $settings['typology_card_bg_temp']['url'] ) ) {
				$bg_image = $settings['typology_card_bg_temp']['url'];
			}
		}

		return array(
			'title'    => $typology->post_title,
			'icon'     => $icon,
			'bg_image' => $bg_image,
			'bg_color' => get_post_meta( $typology_id, 'nd_travel_meta_box_cpt_2_color', true ),
		);
	}

	/**
	 * Get custom data.
	 *
	 * @since 2.0.0
	 * @param array $settings Widget settings.
	 * @return array Card data.
	 */
	private function getCustomData( $settings ) {
		$bg_image = '';
		$bg_color = '';

		if ( 'image' === $settings['custom_bg_type'] && ! empty( $settings['custom_bg_image']['url'] ) ) {
			$bg_image = $settings['custom_bg_image']['url'];
		} elseif ( 'color' === $settings['custom_bg_type'] ) {
			$bg_color = $settings['custom_bg_color'];
		}

		return array(
			'title'    => $settings['custom_title'],
			'icon'     => ! empty( $settings['custom_icon']['url'] ) ? $settings['custom_icon']['url'] : '',
			'bg_image' => $bg_image,
			'bg_color' => $bg_color,
		);
	}

	/**
	 * Get link data.
	 *
	 * @since 2.0.0
	 * @param array $settings Widget settings.
	 * @return array|null Link data or null.
	 */
	private function getLinkData( $settings ) {
		if ( 'none' === $settings['link_type'] ) {
			return null;
		}

		if ( 'typology' === $settings['link_type'] && ! empty( $settings['link_typology_id'] ) ) {
			return array(
				'url'         => get_permalink( $settings['link_typology_id'] ),
				'is_external' => false,
				'nofollow'    => false,
			);
		}

		if ( 'manual' === $settings['link_type'] && ! empty( $settings['manual_link']['url'] ) ) {
			return array(
				'url'         => $settings['manual_link']['url'],
				'is_external' => $settings['manual_link']['is_external'],
				'nofollow'    => $settings['manual_link']['nofollow'],
			);
		}

		return null;
	}

	/**
	 * Render card HTML.
	 *
	 * @since 2.0.0
	 * @param array      $data Card data.
	 * @param array|null $link Link data.
	 */
	private function renderCard( $data, $link ) {
		$bg_style = '';

		if ( ! empty( $data['bg_image'] ) ) {
			$bg_style = 'background-image: url(' . esc_url( $data['bg_image'] ) . '); background-size: cover; background-position: center;';
		} elseif ( ! empty( $data['bg_color'] ) ) {
			$bg_style = 'background-color: ' . esc_attr( $data['bg_color'] ) . ';';
		}

		// Build link attributes
		$link_attrs = '';
		if ( ! empty( $link ) ) {
			$link_attrs  = 'href="' . esc_url( $link['url'] ) . '"';
			$link_attrs .= ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
			$link_attrs .= ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : '';
		}

		$tag = ! empty( $link ) ? 'a' : 'div';
		?>

		<<?php echo esc_attr( $tag ); ?> <?php echo $link_attrs; ?> class="lovetravel-child-typology-card" style="<?php echo esc_attr( $bg_style ); ?>">
			<div class="lovetravel-child-typology-card__content">
				<?php if ( ! empty( $data['icon'] ) ) : ?>
					<div class="lovetravel-child-typology-card__icon">
						<img src="<?php echo esc_url( $data['icon'] ); ?>" alt="<?php echo esc_attr( $data['title'] ); ?>">
					</div>
				<?php endif; ?>
				
				<div class="lovetravel-child-typology-card__title-wrapper">
					<h3 class="lovetravel-child-typology-card__title"><?php echo esc_html( $data['title'] ); ?></h3>
				</div>
			</div>
		</<?php echo esc_attr( $tag ); ?>>

		<style>
			.lovetravel-child-typology-card {
				position: relative;
				display: block;
				overflow: hidden;
				text-decoration: none;
			}
			
			.lovetravel-child-typology-card::before {
				content: '';
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: 1;
				pointer-events: none;
			}
			
			.lovetravel-child-typology-card__content {
				position: relative;
				z-index: 2;
				display: flex;
				align-items: center;
				gap: 20px;
			}
			
			.lovetravel-child-typology-card__icon {
				flex: 0 0 33.333%;
				display: flex;
				align-items: center;
				justify-content: center;
			}
			
			.lovetravel-child-typology-card__icon img {
				display: block;
				height: auto;
			}
			
			.lovetravel-child-typology-card__title-wrapper {
				flex: 1;
				display: flex;
				align-items: center;
			}
			
			.lovetravel-child-typology-card__title {
				margin: 0;
				padding: 0;
			}
			
			a.lovetravel-child-typology-card {
				transition: transform 0.3s ease, box-shadow 0.3s ease;
			}
			
			a.lovetravel-child-typology-card:hover {
				transform: translateY(-2px);
			}
		</style>
		<?php
	}

}
