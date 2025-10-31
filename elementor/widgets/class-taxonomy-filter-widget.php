<?php
/**
 * Taxonomy Filter Widget
 *
 * Elementor widget for filtering search/archive results by taxonomy terms.
 * Configurable for any taxonomy (destinations, durations, difficulties, min ages, typologies).
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 * @since      2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Taxonomy Filter Widget class.
 *
 * @since 2.8.0
 */
class LoveTravelChild_Taxonomy_Filter_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.8.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-taxonomy-filter';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.8.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Taxonomy Filter', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.8.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-filter';
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
		return array( 'taxonomy', 'filter', 'category', 'search', 'archive' );
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
				'default'     => esc_html__( 'Filter', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Enter filter title', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'   => esc_html__( 'Taxonomy', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'nd_travel_cpt_1_tax_0' => esc_html__( 'Destinations', 'lovetravel-child' ),
					'nd_travel_cpt_1_tax_1' => esc_html__( 'Durations', 'lovetravel-child' ),
					'nd_travel_cpt_1_tax_2' => esc_html__( 'Difficulties', 'lovetravel-child' ),
					'nd_travel_cpt_1_tax_3' => esc_html__( 'Min Ages', 'lovetravel-child' ),
					'nd_travel_cpt_2'       => esc_html__( 'Typologies', 'lovetravel-child' ),
				),
				'default' => 'nd_travel_cpt_1_tax_0',
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => esc_html__( 'Show Count', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => esc_html__( 'Show Search Box', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => esc_html__( 'Show search box when 5+ terms exist', 'lovetravel-child' ),
			)
		);

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'Display Type', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'checkboxes' => esc_html__( 'Checkboxes', 'lovetravel-child' ),
					'radio'      => esc_html__( 'Radio Buttons', 'lovetravel-child' ),
				),
				'default' => 'checkboxes',
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
			'checkbox_color',
			array(
				'label'     => esc_html__( 'Checkbox Color', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#EA5B10',
				'selectors' => array(
					'{{WRAPPER}} .lovetravel-filter-checkbox:checked + .lovetravel-filter-label::before' => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
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

		$taxonomy     = $settings['taxonomy'];
		$show_count   = $settings['show_count'] === 'yes';
		$show_search  = $settings['show_search'] === 'yes';
		$display_type = $settings['display_type'];

		// Special handling for Typologies and Destinations (CPTs, not taxonomies)
		$is_cpt_based = in_array( $taxonomy, array( 'nd_travel_cpt_2', 'nd_travel_cpt_3' ), true );
		$terms = array();

		if ( $is_cpt_based ) {
			// Get posts from CPT
			$posts = get_posts(
				array(
					'post_type'      => $taxonomy,
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
				)
			);

			// Convert to term-like objects for consistent rendering
			foreach ( $posts as $post ) {
				$terms[] = (object) array(
					'term_id' => $post->post_name, // Use slug as ID for CPT-based filters
					'name'    => $post->post_title,
					'slug'    => $post->post_name,
					'count'   => 0, // CPT count not easily available without extra queries
				);
			}
		} else {
			// Get taxonomy terms
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
		}

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		// Determine param name and selected values
		if ( $taxonomy === 'nd_travel_cpt_2' ) {
			// Typologies use 'cpt_2' param with slugs
			$param_name     = 'cpt_2';
			$selected_terms = isset( $_GET['cpt_2'] ) ? (array) $_GET['cpt_2'] : array();
		} elseif ( $taxonomy === 'nd_travel_cpt_3' ) {
			// Destinations use 'cpt_1_tax_0' param (legacy compatibility) with slugs
			$param_name     = 'cpt_1_tax_0';
			$selected_terms = isset( $_GET['cpt_1_tax_0'] ) ? (array) $_GET['cpt_1_tax_0'] : array();
		} else {
			// Standard taxonomy params (convert nd_travel_ prefix to param format)
			$param_name      = str_replace( 'nd_travel_', '', $taxonomy );
			$selected_terms  = isset( $_GET[ $param_name ] ) ? (array) $_GET[ $param_name ] : array();
			$selected_terms  = array_map( 'intval', $selected_terms );
		}

		$input_type      = $display_type === 'radio' ? 'radio' : 'checkbox';
		$input_name      = $display_type === 'radio' ? $param_name : $param_name . '[]';

		?>
		<div class="lovetravel-taxonomy-filter" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h3 class="lovetravel-filter-title"><?php echo esc_html( $settings['title'] ); ?></h3>
			<?php endif; ?>

			<div class="lovetravel-taxonomy-filter-wrapper">
				<?php if ( $show_search && count( $terms ) >= 5 ) : ?>
					<div class="lovetravel-filter-search">
						<input 
							type="text" 
							class="lovetravel-filter-search-input" 
							placeholder="<?php esc_attr_e( 'Search...', 'lovetravel-child' ); ?>"
						>
					</div>
				<?php endif; ?>

				<div class="lovetravel-filter-options">
					<?php foreach ( $terms as $term ) : ?>
						<?php
						$is_checked = in_array( $term->term_id, $selected_terms, true );
						?>
						<div class="lovetravel-filter-option">
							<input 
								type="<?php echo esc_attr( $input_type ); ?>" 
								class="lovetravel-filter-checkbox" 
								id="term-<?php echo esc_attr( $term->term_id ); ?>" 
								name="<?php echo esc_attr( $input_name ); ?>"
								value="<?php echo esc_attr( $term->term_id ); ?>"
								data-filter="<?php echo esc_attr( $param_name ); ?>"
								<?php checked( $is_checked, true ); ?>
							>
							<label for="term-<?php echo esc_attr( $term->term_id ); ?>" class="lovetravel-filter-label">
								<?php echo esc_html( $term->name ); ?>
								<?php if ( $show_count ) : ?>
									<span class="lovetravel-filter-count">(<?php echo intval( $term->count ); ?>)</span>
								<?php endif; ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<style>
		.lovetravel-taxonomy-filter {
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
		.lovetravel-filter-search {
			margin-bottom: 15px;
		}
		.lovetravel-filter-search-input {
			width: 100%;
			padding: 8px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		.lovetravel-filter-search-input:focus {
			outline: none;
			border-color: #EA5B10;
		}
		.lovetravel-filter-options {
			max-height: 300px;
			overflow-y: auto;
		}
		.lovetravel-filter-option {
			margin-bottom: 10px;
		}
		.lovetravel-filter-checkbox {
			position: absolute;
			opacity: 0;
		}
		.lovetravel-filter-label {
			display: flex;
			align-items: center;
			cursor: pointer;
			font-size: 14px;
			color: #333;
			padding-left: 28px;
			position: relative;
			user-select: none;
		}
		.lovetravel-filter-label::before {
			content: '';
			position: absolute;
			left: 0;
			top: 50%;
			transform: translateY(-50%);
			width: 18px;
			height: 18px;
			border: 2px solid #ddd;
			border-radius: 3px;
			background: #fff;
			transition: all 0.3s;
		}
		.lovetravel-filter-label::after {
			content: '';
			position: absolute;
			left: 6px;
			top: 50%;
			transform: translateY(-50%) rotate(45deg) scale(0);
			width: 6px;
			height: 10px;
			border: solid #fff;
			border-width: 0 2px 2px 0;
			transition: transform 0.3s;
		}
		.lovetravel-filter-checkbox:checked + .lovetravel-filter-label::before {
			background-color: #EA5B10;
			border-color: #EA5B10;
		}
		.lovetravel-filter-checkbox:checked + .lovetravel-filter-label::after {
			transform: translateY(-50%) rotate(45deg) scale(1);
		}
		.lovetravel-filter-count {
			margin-left: auto;
			color: #999;
			font-size: 12px;
		}
		</style>

		<script>
		(function() {
			const filterOptions = document.querySelectorAll('.lovetravel-filter-checkbox');
			const searchInput = document.querySelector('.lovetravel-filter-search-input');

			// Handle filter changes via AJAX
			filterOptions.forEach(function(input) {
				input.addEventListener('change', function() {
					const filterName = this.getAttribute('data-filter');
					
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
					
					// Get all checked values for this filter
					const checkedInputs = document.querySelectorAll(
						'.lovetravel-filter-checkbox[data-filter="' + filterName + '"]:checked'
					);
					
					// Remove existing parameters
					delete params[filterName];
					url.searchParams.delete(filterName);
					url.searchParams.delete(filterName + '[]');
					
					// Add new parameters
					if (checkedInputs.length > 0) {
						if (this.type === 'radio') {
							params[filterName] = this.value;
							url.searchParams.set(filterName, this.value);
						} else {
							params[filterName] = [];
							checkedInputs.forEach(function(input) {
								params[filterName].push(input.value);
								url.searchParams.append(filterName + '[]', input.value);
							});
						}
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
				});
			});

			// Handle search filtering
			if (searchInput) {
				searchInput.addEventListener('input', function() {
					const searchTerm = this.value.toLowerCase();
					const options = document.querySelectorAll('.lovetravel-filter-option');
					
					options.forEach(function(option) {
						const label = option.querySelector('.lovetravel-filter-label').textContent.toLowerCase();
						option.style.display = label.includes(searchTerm) ? 'block' : 'none';
					});
				});
			}
		})();
		</script>
		<?php
	}
}
