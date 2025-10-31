<?php
/**
 * Date Range Filter Widget
 *
 * Elementor widget for filtering search/archive results by date range.
 * Integrates with nd_travel_meta_box_availability_from and nd_travel_meta_box_availability_to.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Date Range Filter Widget class.
 *
 * @since 2.8.0
 */
class LoveTravelChild_Date_Range_Filter_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.8.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-date-range-filter';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.8.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Date Range Filter', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.8.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-date';
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
		return array( 'date', 'filter', 'range', 'calendar', 'search' );
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
				'default'     => esc_html__( 'Travel Dates', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Enter filter title', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'from_label',
			array(
				'label'   => esc_html__( 'From Label', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'From', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'to_label',
			array(
				'label'   => esc_html__( 'To Label', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'To', 'lovetravel-child' ),
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
			'input_border_color',
			array(
				'label'     => esc_html__( 'Input Border Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ddd',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-date-input' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'input_focus_color',
			array(
				'label'     => esc_html__( 'Focus Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#EA5B10',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-date-input:focus' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 2.8.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get current filter values from URL
		$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '';
		$date_to   = isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '';

		// Convert YYYY-MM-DD to dd.mm.yyyy for display
		$date_from_display = '';
		$date_to_display   = '';
		if ( $date_from ) {
			$date_from_display = gmdate( 'd.m.Y', strtotime( $date_from ) );
		}
		if ( $date_to ) {
			$date_to_display = gmdate( 'd.m.Y', strtotime( $date_to ) );
		}

		// Min date is today
		$min_date = gmdate( 'd.m.Y' );

		?>
		<div class="lovetravel-date-range-filter">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h3 class="lovetravel-filter-title"><?php echo esc_html( $settings['title'] ); ?></h3>
			<?php endif; ?>

			<div class="lovetravel-date-range-wrapper">
				<div class="lovetravel-date-field">
					<label for="date-from-input" class="lovetravel-date-label">
						<?php echo esc_html( $settings['from_label'] ); ?>
					</label>
					<input 
						type="text" 
						class="lovetravel-date-input" 
						id="date-from-input" 
						name="date_from"
						placeholder="dd.mm.yyyy"
						value="<?php echo esc_attr( $date_from_display ); ?>"
						data-filter="date"
						data-min-date="<?php echo esc_attr( $min_date ); ?>"
					>
				</div>

				<div class="lovetravel-date-field">
					<label for="date-to-input" class="lovetravel-date-label">
						<?php echo esc_html( $settings['to_label'] ); ?>
					</label>
					<input 
						type="text" 
						class="lovetravel-date-input" 
						id="date-to-input" 
						name="date_to"
						placeholder="dd.mm.yyyy"
						value="<?php echo esc_attr( $date_to_display ); ?>"
						data-filter="date"
						data-min-date="<?php echo esc_attr( $min_date ); ?>"
					>
				</div>

				<?php if ( $date_from || $date_to ) : ?>
					<button type="button" class="lovetravel-date-clear-btn">
						<?php esc_html_e( 'Clear Dates', 'lovetravel-child' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>

		<style>
		.lovetravel-date-range-filter {
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
		.lovetravel-date-field {
			margin-bottom: 15px;
		}
		.lovetravel-date-label {
			display: block;
			margin-bottom: 5px;
			font-size: 14px;
			font-weight: 500;
			color: #333;
		}
		.lovetravel-date-input {
			width: 100%;
			padding: 10px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
			transition: border-color 0.3s;
		}
		.lovetravel-date-input:focus {
			outline: none;
			border-color: #EA5B10;
		}
		.lovetravel-date-clear-btn {
			width: 100%;
			padding: 10px;
			background: #f5f5f5;
			border: 1px solid #ddd;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
			color: #666;
			transition: all 0.3s;
		}
		.lovetravel-date-clear-btn:hover {
			background: #EA5B10;
			color: #fff;
			border-color: #EA5B10;
		}
		</style>

		<script>
		(function() {
			const dateFrom = document.getElementById('date-from-input');
			const dateTo = document.getElementById('date-to-input');
			const clearBtn = document.querySelector('.lovetravel-date-clear-btn');

			// Parse dd.mm.yyyy to YYYY-MM-DD
			function parseDateInput(value) {
				if (!value) return '';
				const parts = value.split('.');
				if (parts.length === 3) {
					const day = parts[0].padStart(2, '0');
					const month = parts[1].padStart(2, '0');
					const year = parts[2];
					if (year.length === 4 && !isNaN(day) && !isNaN(month)) {
						return year + '-' + month + '-' + day;
					}
				}
				return '';
			}

			// Validate and format dd.mm.yyyy input
			function formatDateInput(input) {
				let value = input.value.replace(/[^0-9.]/g, '');
				const parts = value.split('.');
				
				if (parts[0] && parts[0].length > 2) {
					parts[0] = parts[0].substring(0, 2);
				}
				if (parts[1] && parts[1].length > 2) {
					parts[1] = parts[1].substring(0, 2);
				}
				if (parts[2] && parts[2].length > 4) {
					parts[2] = parts[2].substring(0, 4);
				}
				
				input.value = parts.join('.');
			}

			function triggerAjaxFilter() {
				const dateFromValue = parseDateInput(dateFrom.value);
				const dateToValue = parseDateInput(dateTo.value);

				// Build params from current URL
				const url = new URL(window.location);
				const params = {};
				url.searchParams.forEach((value, key) => {
					if (!key.endsWith('[]')) {
						params[key] = value;
					} else {
						const cleanKey = key.replace(/\[\]$/, '');
						if (!params[cleanKey]) params[cleanKey] = [];
						params[cleanKey].push(value);
					}
				});

				// Update date params
				if (dateFromValue) {
					params.date_from = dateFromValue;
					url.searchParams.set('date_from', dateFromValue);
				} else {
					delete params.date_from;
					url.searchParams.delete('date_from');
				}

				if (dateToValue) {
					params.date_to = dateToValue;
					url.searchParams.set('date_to', dateToValue);
				} else {
					delete params.date_to;
					url.searchParams.delete('date_to');
				}

				// Call AJAX via component
				params.nonce = (window.lovetravelLoadMore && window.lovetravelLoadMore.nonce) || '';
				params.widgetSelector = '.elementor-widget-lovetravel-child-packages';

				if (window.LoveTravelChild && window.LoveTravelChild.getComponent) {
					const comp = window.LoveTravelChild.getComponent('packagesLoadMore');
					if (comp && typeof comp.fetchFilteredPackages === 'function') {
						comp.fetchFilteredPackages(params);
						// Update URL without reload
						window.history.pushState({}, '', url.toString());
						return;
					}
				}

				// Fallback to page reload
				window.location.href = url.toString();
			}

			function clearDates() {
				const url = new URL(window.location);
				const params = {};
				url.searchParams.forEach((value, key) => {
					if (key !== 'date_from' && key !== 'date_to') {
						if (!key.endsWith('[]')) {
							params[key] = value;
						} else {
							const cleanKey = key.replace(/\[\]$/, '');
							if (!params[cleanKey]) params[cleanKey] = [];
							params[cleanKey].push(value);
						}
					}
				});

				url.searchParams.delete('date_from');
				url.searchParams.delete('date_to');

				params.nonce = (window.lovetravelLoadMore && window.lovetravelLoadMore.nonce) || '';
				params.widgetSelector = '.elementor-widget-lovetravel-child-packages';

				if (window.LoveTravelChild && window.LoveTravelChild.getComponent) {
					const comp = window.LoveTravelChild.getComponent('packagesLoadMore');
					if (comp && typeof comp.fetchFilteredPackages === 'function') {
						comp.fetchFilteredPackages(params);
						window.history.pushState({}, '', url.toString());
						return;
					}
				}

				window.location.href = url.toString();
			}

			if (dateFrom) {
				dateFrom.addEventListener('input', function() {
					formatDateInput(this);
				});
				dateFrom.addEventListener('blur', function() {
					if (this.value && parseDateInput(this.value)) {
						triggerAjaxFilter();
					}
				});
			}

			if (dateTo) {
				dateTo.addEventListener('input', function() {
					formatDateInput(this);
				});
				dateTo.addEventListener('blur', function() {
					if (this.value && parseDateInput(this.value)) {
						triggerAjaxFilter();
					}
				});
			}

			if (clearBtn) {
				clearBtn.addEventListener('click', clearDates);
			}
		})();
		</script>
		<?php
	}
}
