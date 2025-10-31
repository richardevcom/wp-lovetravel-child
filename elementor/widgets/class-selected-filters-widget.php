<?php
/**
 * Selected Filters Widget
 *
 * Elementor widget displaying active filter badges with remove buttons.
 * Shows currently applied filters from URL parameters.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selected Filters Widget class.
 *
 * @since 2.8.0
 */
class LoveTravelChild_Selected_Filters_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.8.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-selected-filters';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.8.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Selected Filters', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.8.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tags';
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
		return array( 'filter', 'tags', 'badges', 'selected', 'active' );
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
				'default'     => esc_html__( 'Active Filters', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Enter title', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'clear_all_text',
			array(
				'label'   => esc_html__( 'Clear All Text', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Clear All', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'hide_when_empty',
			array(
				'label'        => esc_html__( 'Hide When Empty', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'badge_background',
			array(
				'label'     => esc_html__( 'Badge Background', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#EA5B10',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-filter-badge' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'badge_text_color',
			array(
				'label'     => esc_html__( 'Badge Text Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-filter-badge' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get active filters from URL.
	 *
	 * @since 2.8.0
	 * @return array Active filters.
	 */
	private function get_active_filters() {
		$filters = array();

		// Price filter
		if ( isset( $_GET['price_min'] ) || isset( $_GET['price_max'] ) ) {
			$min = isset( $_GET['price_min'] ) ? intval( $_GET['price_min'] ) : null;
			$max = isset( $_GET['price_max'] ) ? intval( $_GET['price_max'] ) : null;
			
			$label = '';
			if ( $min && $max ) {
				$label = sprintf( '€%s - €%s', number_format( $min, 0, ',', '.' ), number_format( $max, 0, ',', '.' ) );
			} elseif ( $min ) {
				$label = sprintf( 'Min: €%s', number_format( $min, 0, ',', '.' ) );
			} elseif ( $max ) {
				$label = sprintf( 'Max: €%s', number_format( $max, 0, ',', '.' ) );
			}

			$filters[] = array(
				'type'  => 'price',
				'label' => $label,
				'param' => array( 'price_min', 'price_max' ),
			);
		}

		// Date filter
		if ( isset( $_GET['date_from'] ) || isset( $_GET['date_to'] ) ) {
			$from = isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '';
			$to   = isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '';

			$label = '';
			if ( $from && $to ) {
				$label = sprintf( '%s - %s', date( 'd.m.Y', strtotime( $from ) ), date( 'd.m.Y', strtotime( $to ) ) );
			} elseif ( $from ) {
				$label = sprintf( 'From: %s', date( 'd.m.Y', strtotime( $from ) ) );
			} elseif ( $to ) {
				$label = sprintf( 'To: %s', date( 'd.m.Y', strtotime( $to ) ) );
			}

			$filters[] = array(
				'type'  => 'date',
				'label' => $label,
				'param' => array( 'date_from', 'date_to' ),
			);
		}

		// Taxonomy filters
		$taxonomy_map = array(
			'cpt_1_tax_0' => array( 'nd_travel_cpt_1_tax_0', __( 'Destination', 'lovetravel-child' ) ),
			'cpt_1_tax_1' => array( 'nd_travel_cpt_1_tax_1', __( 'Duration', 'lovetravel-child' ) ),
			'cpt_1_tax_2' => array( 'nd_travel_cpt_1_tax_2', __( 'Difficulty', 'lovetravel-child' ) ),
			'cpt_1_tax_3' => array( 'nd_travel_cpt_1_tax_3', __( 'Min Age', 'lovetravel-child' ) ),
		);

		foreach ( $taxonomy_map as $param => $data ) {
			if ( isset( $_GET[ $param ] ) && ! empty( $_GET[ $param ] ) ) {
				$values = (array) $_GET[ $param ];
				$taxonomy = $data[0];

				foreach ( $values as $value ) {
					// Check if numeric (term ID) or string (slug for CPT-based)
					if ( is_numeric( $value ) ) {
						$term = get_term( intval( $value ), $taxonomy );
						if ( $term && ! is_wp_error( $term ) ) {
							$filters[] = array(
								'type'    => 'taxonomy',
								'label'   => $term->name,
								'param'   => $param,
								'value'   => $value,
								'tax_name' => $data[1],
							);
						}
					} else {
						// Slug-based (e.g., Destinations from nd_travel_cpt_3)
						$post = get_page_by_path( $value, OBJECT, 'nd_travel_cpt_3' );
						if ( $post ) {
							$filters[] = array(
								'type'    => 'taxonomy',
								'label'   => $post->post_title,
								'param'   => $param,
								'value'   => $value,
								'tax_name' => $data[1],
							);
						}
					}
				}
			}
		}

		// Typologies (CPT-based, param: cpt_2)
		if ( isset( $_GET['cpt_2'] ) && ! empty( $_GET['cpt_2'] ) ) {
			$typology_slugs = (array) $_GET['cpt_2'];

			foreach ( $typology_slugs as $slug ) {
				$post = get_page_by_path( $slug, OBJECT, 'nd_travel_cpt_2' );
				if ( $post ) {
					$filters[] = array(
						'type'    => 'cpt',
						'label'   => $post->post_title,
						'param'   => 'cpt_2',
						'value'   => $slug,
						'tax_name' => __( 'Typology', 'lovetravel-child' ),
					);
				}
			}
		}

		return $filters;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 2.8.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$filters  = $this->get_active_filters();

		$hide_when_empty = $settings['hide_when_empty'] === 'yes';

		if ( empty( $filters ) && $hide_when_empty ) {
			return;
		}

		?>
		<div class="lovetravel-selected-filters">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h3 class="lovetravel-filters-title"><?php echo esc_html( $settings['title'] ); ?></h3>
			<?php endif; ?>

			<div class="lovetravel-filters-list">
				<?php if ( empty( $filters ) ) : ?>
					<p class="lovetravel-no-filters"><?php esc_html_e( 'No filters applied', 'lovetravel-child' ); ?></p>
				<?php else : ?>
					<?php foreach ( $filters as $filter ) : ?>
						<span class="lovetravel-filter-badge" data-filter-type="<?php echo esc_attr( $filter['type'] ); ?>" data-filter-param="<?php echo esc_attr( is_array( $filter['param'] ) ? implode( ',', $filter['param'] ) : $filter['param'] ); ?>" data-filter-value="<?php echo isset( $filter['value'] ) ? esc_attr( $filter['value'] ) : ''; ?>">
							<?php echo esc_html( $filter['label'] ); ?>
							<button type="button" class="lovetravel-filter-remove" aria-label="<?php esc_attr_e( 'Remove filter', 'lovetravel-child' ); ?>">×</button>
						</span>
					<?php endforeach; ?>

					<button type="button" class="lovetravel-clear-all-filters">
						<?php echo esc_html( $settings['clear_all_text'] ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>

		<style>
		.lovetravel-selected-filters {
			padding: 20px;
			background: #fff;
			border-radius: 4px;
			margin-bottom: 20px;
		}
		.lovetravel-filters-title {
			margin: 0 0 15px 0;
			font-size: 16px;
			font-weight: 600;
		}
		.lovetravel-filters-list {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			align-items: center;
		}
		.lovetravel-filter-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 6px 12px;
			background: #EA5B10;
			color: #fff;
			border-radius: 20px;
			font-size: 14px;
			font-weight: 500;
		}
		.lovetravel-filter-remove {
			background: none;
			border: none;
			color: #fff;
			font-size: 20px;
			line-height: 1;
			cursor: pointer;
			padding: 0;
			margin-left: 4px;
			opacity: 0.8;
			transition: opacity 0.3s;
		}
		.lovetravel-filter-remove:hover {
			opacity: 1;
		}
		.lovetravel-clear-all-filters {
			padding: 6px 16px;
			background: #f5f5f5;
			border: 1px solid #ddd;
			border-radius: 20px;
			font-size: 14px;
			font-weight: 500;
			color: #666;
			cursor: pointer;
			transition: all 0.3s;
		}
		.lovetravel-clear-all-filters:hover {
			background: #EA5B10;
			color: #fff;
			border-color: #EA5B10;
		}
		.lovetravel-no-filters {
			color: #999;
			font-size: 14px;
			margin: 0;
		}
		</style>

		<script>
		(function() {
			// Helper: collect current URL params into a plain object
			function buildParamsFromUrl() {
				const url = new URL(window.location);
				const params = {};
				url.searchParams.forEach((value, key) => {
					// Support repeated params like foo[]
					if (key.endsWith('[]')) {
						const cleanKey = key.replace(/\[\]$/, '');
						if (!params[cleanKey]) params[cleanKey] = [];
						params[cleanKey].push(value);
					} else {
						// If key already exists, convert to array
						if (typeof params[key] === 'undefined') {
							params[key] = value;
						} else if (Array.isArray(params[key])) {
							params[key].push(value);
						} else {
							params[key] = [ params[key], value ];
						}
					}
				});
				return params;
			}

			// Serialize params for AJAX call (no URL encoding)
			function normalizeParams(params) {
				const out = {};
				Object.keys(params).forEach(k => {
					out[k] = params[k];
				});
				return out;
			}

			// Remove individual filter using AJAX update
			document.querySelectorAll('.lovetravel-filter-remove').forEach(function(btn) {
				btn.addEventListener('click', function() {
					const badge = this.closest('.lovetravel-filter-badge');
					const filterType = badge.dataset.filterType;
					const filterParam = badge.dataset.filterParam;
					const filterValue = badge.dataset.filterValue;

					const params = buildParamsFromUrl();

					if (filterType === 'price' || filterType === 'date') {
						filterParam.split(',').forEach(param => delete params[param]);
					} else if (filterType === 'taxonomy' || filterType === 'cpt') {
						if (!Array.isArray(params[filterParam])) {
							params[filterParam] = [ params[filterParam] ].filter(Boolean);
						}
						params[filterParam] = (params[filterParam] || []).filter(v => v !== filterValue);
						if (params[filterParam].length === 0) delete params[filterParam];
					}

					// Update URL for history
					const url = new URL(window.location);
					Object.keys(params).forEach(key => {
						url.searchParams.delete(key);
						url.searchParams.delete(key + '[]');
					});
					Object.keys(params).forEach(key => {
						if (Array.isArray(params[key])) {
							params[key].forEach(v => url.searchParams.append(key + '[]', v));
						} else {
							url.searchParams.set(key, params[key]);
						}
					});

					// Prepare AJAX params and call component
					const ajaxParams = normalizeParams(params);
					ajaxParams.nonce = (window.lovetravelLoadMore && window.lovetravelLoadMore.nonce) ? window.lovetravelLoadMore.nonce : '';
					ajaxParams.widgetSelector = '.elementor-widget-lovetravel-child-packages';

					if (window.LoveTravelChild && window.LoveTravelChild.getComponent) {
						const comp = window.LoveTravelChild.getComponent('packagesLoadMore');
						if (comp && typeof comp.fetchFilteredPackages === 'function') {
							comp.fetchFilteredPackages(ajaxParams);
							window.history.pushState({}, '', url.toString());
							return;
						}
					}

					// Fallback to full page reload
					window.location.href = url.toString();
				});
			});

			// Clear all filters via AJAX (keep 's' search param if present)
			const clearAllBtn = document.querySelector('.lovetravel-clear-all-filters');
			if (clearAllBtn) {
				clearAllBtn.addEventListener('click', function() {
					const params = {};
					const url = new URL(window.location);
					const searchQuery = url.searchParams.get('s');
					if (searchQuery) params.s = searchQuery;
					const ajaxParams = normalizeParams(params);
					ajaxParams.nonce = (window.lovetravelLoadMore && window.lovetravelLoadMore.nonce) ? window.lovetravelLoadMore.nonce : '';
					ajaxParams.widgetSelector = '.elementor-widget-lovetravel-child-packages';

					if (window.LoveTravelChild && window.LoveTravelChild.getComponent) {
						const comp = window.LoveTravelChild.getComponent('packagesLoadMore');
						if (comp && typeof comp.fetchFilteredPackages === 'function') {
							comp.fetchFilteredPackages(ajaxParams);
							return;
						}
					}

					// fallback
					const newUrl = new URL(url.origin + url.pathname);
					if (searchQuery) newUrl.searchParams.set('s', searchQuery);
					window.location.href = newUrl.toString();
				});
			}
		})();
		</script>
		<?php
	}
}
