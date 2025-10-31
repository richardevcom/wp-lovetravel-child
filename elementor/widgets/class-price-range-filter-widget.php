<?php
/**
 * Price Range Filter Widget
 *
 * Elementor widget for filtering search/archive results by price range.
 * Integrates with nd_travel_meta_box_price meta field.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Price Range Filter Widget class.
 *
 * @since 2.8.0
 */
class LoveTravelChild_Price_Range_Filter_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.8.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-price-range-filter';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.8.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Price Range Filter', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.8.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 2.8.0
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'lovetravel-child' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 2.8.0
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'price', 'filter', 'range', 'search', 'archive' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since 2.8.0
	 */
	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Settings', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Price Range', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Enter filter title', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'currency_symbol',
			array(
				'label'   => esc_html__( 'Currency Symbol', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '€',
			)
		);

		$this->add_control(
			'min_price',
			array(
				'label'       => esc_html__( 'Minimum Price', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => esc_html__( 'Leave empty to auto-detect from database', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'max_price',
			array(
				'label'       => esc_html__( 'Maximum Price', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 5000,
				'description' => esc_html__( 'Leave empty to auto-detect from database', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'step',
			array(
				'label'   => esc_html__( 'Step Size', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 1,
			)
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Style', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-filter-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'lovetravel-child' ),
				'selector' => '{{WRAPPER}} .lovetravel-filter-title',
			)
		);

		$this->add_control(
			'slider_color',
			array(
				'label'     => esc_html__( 'Slider Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#EA5B10',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-price-slider::-webkit-slider-thumb' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .lovetravel-price-slider::-moz-range-thumb'     => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get min/max price from database.
	 * 
	 * Considers both regular price and promotion price.
	 * Uses promotion price if it's set and lower than regular price.
	 *
	 * @since 2.8.0
	 * @return array Array with min and max prices.
	 */
	private function get_price_range() {
		global $wpdb;

		// Get all prices (regular and promotion) in one query
		$prices = $wpdb->get_results(
			"SELECT p1.post_id, 
				CAST(p1.meta_value AS UNSIGNED) as regular_price,
				CAST(p2.meta_value AS UNSIGNED) as promotion_price
			FROM {$wpdb->postmeta} p1
			LEFT JOIN {$wpdb->postmeta} p2 
				ON p1.post_id = p2.post_id 
				AND p2.meta_key = 'nd_travel_meta_box_promotion_price'
			WHERE p1.meta_key = 'nd_travel_meta_box_price' 
			AND p1.meta_value != ''",
			ARRAY_A
		);

		if ( empty( $prices ) ) {
			return array(
				'min' => 0,
				'max' => 5000,
			);
		}

		$min = PHP_INT_MAX;
		$max = 0;

		// For each package, use the lower price (promotion if set and lower)
		foreach ( $prices as $price_data ) {
			$regular_price   = intval( $price_data['regular_price'] );
			$promotion_price = intval( $price_data['promotion_price'] );

			// Use promotion price if it exists and is lower than regular price
			$effective_price = ( $promotion_price > 0 && $promotion_price < $regular_price ) 
				? $promotion_price 
				: $regular_price;

			if ( $effective_price > 0 ) {
				$min = min( $min, $effective_price );
				$max = max( $max, $effective_price );
			}
		}

		return array(
			'min' => $min !== PHP_INT_MAX ? $min : 0,
			'max' => $max > 0 ? $max : 5000,
		);
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 2.8.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get price range from DB or settings
		$db_range  = $this->get_price_range();
		$min_price = ! empty( $settings['min_price'] ) ? $settings['min_price'] : $db_range['min'];
		$max_price = ! empty( $settings['max_price'] ) ? $settings['max_price'] : $db_range['max'];
		$step      = $settings['step'];
		$currency  = $settings['currency_symbol'];

		// Get current filter values from URL
		$current_min = isset( $_GET['price_min'] ) ? intval( $_GET['price_min'] ) : $min_price;
		$current_max = isset( $_GET['price_max'] ) ? intval( $_GET['price_max'] ) : $max_price;

		?>
		<div class="lovetravel-price-range-filter">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h3 class="lovetravel-filter-title"><?php echo esc_html( $settings['title'] ); ?></h3>
			<?php endif; ?>

			<div class="lovetravel-price-range-wrapper">
				<div class="lovetravel-price-display">
					<span class="lovetravel-price-min">
						<?php echo esc_html( $currency ); ?><span id="price-min-display"><?php echo number_format( $current_min, 0, ',', '.' ); ?></span>
					</span>
					<span class="lovetravel-price-separator"> - </span>
					<span class="lovetravel-price-max">
						<?php echo esc_html( $currency ); ?><span id="price-max-display"><?php echo number_format( $current_max, 0, ',', '.' ); ?></span>
					</span>
				</div>

				<div class="lovetravel-price-sliders">
					<input 
						type="range" 
						class="lovetravel-price-slider" 
						id="price-min-slider" 
						name="price_min"
						min="<?php echo esc_attr( $min_price ); ?>" 
						max="<?php echo esc_attr( $max_price ); ?>" 
						step="<?php echo esc_attr( $step ); ?>" 
						value="<?php echo esc_attr( $current_min ); ?>"
						data-filter="price"
					>
					<input 
						type="range" 
						class="lovetravel-price-slider" 
						id="price-max-slider" 
						name="price_max"
						min="<?php echo esc_attr( $min_price ); ?>" 
						max="<?php echo esc_attr( $max_price ); ?>" 
						step="<?php echo esc_attr( $step ); ?>" 
						value="<?php echo esc_attr( $current_max ); ?>"
						data-filter="price"
					>
				</div>
			</div>
		</div>

		<style>
		.lovetravel-price-range-filter {
			padding: 20px;
			background: #fff;
			border-radius: 4px;
			margin-bottom: 20px;
		}
		.lovetravel-filter-title {
			margin: 0 0 15px 0;
			font-size: 16px;
			font-weight: 600;
		}
		.lovetravel-price-display {
			text-align: center;
			margin-bottom: 15px;
			font-size: 18px;
			font-weight: 600;
			color: #EA5B10;
		}
		.lovetravel-price-separator {
			margin: 0 10px;
			color: #666;
		}
		.lovetravel-price-sliders {
			position: relative;
			height: 40px;
		}
		.lovetravel-price-slider {
			position: absolute;
			width: 100%;
			height: 6px;
			background: transparent;
			pointer-events: none;
			-webkit-appearance: none;
		}
		.lovetravel-price-slider::-webkit-slider-runnable-track {
			width: 100%;
			height: 6px;
			background: #ddd;
			border-radius: 3px;
		}
		.lovetravel-price-slider::-webkit-slider-thumb {
			-webkit-appearance: none;
			pointer-events: all;
			width: 18px;
			height: 18px;
			background-color: #EA5B10;
			border-radius: 50%;
			cursor: pointer;
			margin-top: -6px;
		}
		.lovetravel-price-slider::-moz-range-track {
			width: 100%;
			height: 6px;
			background: #ddd;
			border-radius: 3px;
		}
		.lovetravel-price-slider::-moz-range-thumb {
			pointer-events: all;
			width: 18px;
			height: 18px;
			background-color: #EA5B10;
			border-radius: 50%;
			border: none;
			cursor: pointer;
		}
		</style>

		<script>
		(function() {
			const minSlider = document.getElementById('price-min-slider');
			const maxSlider = document.getElementById('price-max-slider');
			const minDisplay = document.getElementById('price-min-display');
			const maxDisplay = document.getElementById('price-max-display');

			if (!minSlider || !maxSlider) return;

			function updateDisplay() {
				let minVal = parseInt(minSlider.value);
				let maxVal = parseInt(maxSlider.value);

				// Ensure min doesn't exceed max
				if (minVal > maxVal - <?php echo intval( $step ); ?>) {
					minVal = maxVal - <?php echo intval( $step ); ?>;
					minSlider.value = minVal;
				}

				// Ensure max doesn't go below min
				if (maxVal < minVal + <?php echo intval( $step ); ?>) {
					maxVal = minVal + <?php echo intval( $step ); ?>;
					maxSlider.value = maxVal;
				}

				minDisplay.textContent = minVal.toLocaleString('de-DE');
				maxDisplay.textContent = maxVal.toLocaleString('de-DE');
			}

			function triggerFilter() {
				const url = new URL(window.location);
				url.searchParams.set('price_min', minSlider.value);
				url.searchParams.set('price_max', maxSlider.value);
				window.location.href = url.toString();
			}

			minSlider.addEventListener('input', updateDisplay);
			maxSlider.addEventListener('input', updateDisplay);
			minSlider.addEventListener('change', triggerFilter);
			maxSlider.addEventListener('change', triggerFilter);
		})();
		</script>
		<?php
	}
}
