<?php
/**
 * Typology Cards Widget
 *
 * Elementor widget for displaying multiple typology cards in a grid.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Typology Cards Widget class.
 *
 * @since 2.0.0
 */
class LoveTravelChildTypologyCardsWidget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.0.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel_child_typology_cards';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.0.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Typology Cards', 'lovetravel-child' );
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
		return array( 'typology', 'cards', 'grid', 'lovetravel' );
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
		// Query Section
		$this->start_controls_section(
			'query_section',
			array(
				'label' => __( 'Query', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Query Type
		$this->add_control(
			'query_type',
			array(
				'label'   => __( 'Query Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'     => __( 'All Typologies', 'lovetravel-child' ),
					'include' => __( 'Include by IDs', 'lovetravel-child' ),
					'exclude' => __( 'Exclude by IDs', 'lovetravel-child' ),
					'custom'  => __( 'Custom Items', 'lovetravel-child' ),
				),
			)
		);

		// Include IDs
		$this->add_control(
			'include_ids',
			array(
				'label'       => __( 'Include Typologies', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->getTypologyOptions(),
				'condition'   => array(
					'query_type' => 'include',
				),
			)
		);

		// Exclude IDs
		$this->add_control(
			'exclude_ids',
			array(
				'label'       => __( 'Exclude Typologies', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->getTypologyOptions(),
				'condition'   => array(
					'query_type' => 'exclude',
				),
			)
		);

		// Posts Per Page
		$this->add_control(
			'posts_per_page',
			array(
				'label'     => __( 'Posts Per Page', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 50,
				'condition' => array(
					'query_type' => array( 'all', 'include', 'exclude' ),
				),
			)
		);

		// Custom Items (Repeater)
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Typology Title', 'lovetravel-child' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'bg_type',
			array(
				'label'   => __( 'Background Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'color',
				'options' => array(
					'color' => __( 'Color', 'lovetravel-child' ),
					'image' => __( 'Image', 'lovetravel-child' ),
				),
			)
		);

		$repeater->add_control(
			'bg_image',
			array(
				'label'     => __( 'Background Image', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'condition' => array(
					'bg_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#f5f5f5',
				'condition' => array(
					'bg_type' => 'color',
				),
			)
		);

		$repeater->add_control(
			'link_type',
			array(
				'label'   => __( 'Link Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'lovetravel-child' ),
					'manual' => __( 'Manual URL', 'lovetravel-child' ),
				),
			)
		);

		$repeater->add_control(
			'link',
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

		$this->add_control(
			'custom_items',
			array(
				'label'       => __( 'Custom Items', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'title'    => __( 'Typology 1', 'lovetravel-child' ),
						'bg_type'  => 'color',
						'bg_color' => '#f5f5f5',
					),
					array(
						'title'    => __( 'Typology 2', 'lovetravel-child' ),
						'bg_type'  => 'color',
						'bg_color' => '#e8f5e9',
					),
					array(
						'title'    => __( 'Typology 3', 'lovetravel-child' ),
						'bg_type'  => 'color',
						'bg_color' => '#e3f2fd',
					),
				),
				'title_field' => '{{{ title }}}',
				'condition'   => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->end_controls_section();

		// Layout Section
		$this->start_controls_section(
			'layout_section',
			array(
				'label' => __( 'Layout', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'lovetravel-child' ),
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
				'selectors'      => array(
					'{{WRAPPER}} .lovetravel-child-typology-cards-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'     => __( 'Column Gap', 'lovetravel-child' ),
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
					'{{WRAPPER}} .lovetravel-child-typology-cards-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'     => __( 'Row Gap', 'lovetravel-child' ),
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
					'{{WRAPPER}} .lovetravel-child-typology-cards-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style tab controls (inherit from single card widget).
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
		$options = array();

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

		// Get cards data based on query type
		if ( 'custom' === $settings['query_type'] ) {
			$cards_data = $this->getCustomCardsData( $settings );
		} else {
			$cards_data = $this->getTypologyCardsData( $settings );
		}

		if ( empty( $cards_data ) ) {
			return;
		}

		// Render grid
		$this->renderCardsGrid( $cards_data );
	}

	/**
	 * Get typology cards data from query.
	 *
	 * @since 2.0.0
	 * @param array $settings Widget settings.
	 * @return array Cards data.
	 */
	private function getTypologyCardsData( $settings ) {
		$args = array(
			'post_type'      => 'nd_travel_cpt_2',
			'posts_per_page' => ! empty( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 6,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		);

		if ( 'include' === $settings['query_type'] && ! empty( $settings['include_ids'] ) ) {
			$args['post__in'] = $settings['include_ids'];
		}

		if ( 'exclude' === $settings['query_type'] && ! empty( $settings['exclude_ids'] ) ) {
			$args['post__not_in'] = $settings['exclude_ids'];
		}

		$typologies = get_posts( $args );

		$cards_data = array();

		foreach ( $typologies as $typology ) {
			// Get meta fields (child theme fields first, fallback to plugin fields)
			$icon = get_post_meta( $typology->ID, 'lovetravel_child_meta_box_card_icon', true );
			if ( empty( $icon ) ) {
				$icon = get_post_meta( $typology->ID, 'nd_travel_meta_box_cpt_2_icon', true );
			}

			$bg_image = get_post_meta( $typology->ID, 'lovetravel_child_meta_box_card_bg_image', true );
			if ( empty( $bg_image ) ) {
				$bg_image = get_post_meta( $typology->ID, 'nd_travel_meta_box_image_cpt_2', true );
			}

			$cards_data[] = array(
				'title'    => $typology->post_title,
				'icon'     => $icon,
				'bg_image' => $bg_image,
				'bg_color' => get_post_meta( $typology->ID, 'nd_travel_meta_box_cpt_2_color', true ),
				'link'     => array(
					'url'         => get_permalink( $typology->ID ),
					'is_external' => false,
					'nofollow'    => false,
				),
			);
		}

		return $cards_data;
	}

	/**
	 * Get custom cards data from repeater.
	 *
	 * @since 2.0.0
	 * @param array $settings Widget settings.
	 * @return array Cards data.
	 */
	private function getCustomCardsData( $settings ) {
		$cards_data = array();

		if ( empty( $settings['custom_items'] ) ) {
			return $cards_data;
		}

		foreach ( $settings['custom_items'] as $item ) {
			$bg_image = '';
			$bg_color = '';

			if ( 'image' === $item['bg_type'] && ! empty( $item['bg_image']['url'] ) ) {
				$bg_image = $item['bg_image']['url'];
			} elseif ( 'color' === $item['bg_type'] ) {
				$bg_color = $item['bg_color'];
			}

			$link = null;
			if ( 'manual' === $item['link_type'] && ! empty( $item['link']['url'] ) ) {
				$link = array(
					'url'         => $item['link']['url'],
					'is_external' => $item['link']['is_external'],
					'nofollow'    => $item['link']['nofollow'],
				);
			}

			$cards_data[] = array(
				'title'    => $item['title'],
				'icon'     => ! empty( $item['icon']['url'] ) ? $item['icon']['url'] : '',
				'bg_image' => $bg_image,
				'bg_color' => $bg_color,
				'link'     => $link,
			);
		}

		return $cards_data;
	}

	/**
	 * Render cards grid HTML.
	 *
	 * @since 2.0.0
	 * @param array $cards_data Cards data.
	 */
	private function renderCardsGrid( $cards_data ) {
		?>
		<div class="lovetravel-child-typology-cards-grid">
			<?php foreach ( $cards_data as $card ) : ?>
				<?php $this->renderCard( $card ); ?>
			<?php endforeach; ?>
		</div>

		<style>
			.lovetravel-child-typology-cards-grid {
				display: grid;
			}
		</style>
		<?php
	}

	/**
	 * Render single card HTML (same as single widget).
	 *
	 * @since 2.0.0
	 * @param array $card Card data.
	 */
	private function renderCard( $card ) {
		$bg_style = '';

		if ( ! empty( $card['bg_image'] ) ) {
			$bg_style = 'background-image: url(' . esc_url( $card['bg_image'] ) . '); background-size: cover; background-position: center;';
		} elseif ( ! empty( $card['bg_color'] ) ) {
			$bg_style = 'background-color: ' . esc_attr( $card['bg_color'] ) . ';';
		}

		// Build link attributes
		$link_attrs = '';
		if ( ! empty( $card['link'] ) ) {
			$link_attrs  = 'href="' . esc_url( $card['link']['url'] ) . '"';
			$link_attrs .= ! empty( $card['link']['is_external'] ) ? ' target="_blank"' : '';
			$link_attrs .= ! empty( $card['link']['nofollow'] ) ? ' rel="nofollow"' : '';
		}

		$tag = ! empty( $card['link'] ) ? 'a' : 'div';
		?>

		<<?php echo esc_attr( $tag ); ?> <?php echo $link_attrs; ?> class="lovetravel-child-typology-card" style="<?php echo esc_attr( $bg_style ); ?>">
			<div class="lovetravel-child-typology-card__content">
				<?php if ( ! empty( $card['icon'] ) ) : ?>
					<div class="lovetravel-child-typology-card__icon">
						<img src="<?php echo esc_url( $card['icon'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>">
					</div>
				<?php endif; ?>
				
				<div class="lovetravel-child-typology-card__title-wrapper">
					<h3 class="lovetravel-child-typology-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
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
