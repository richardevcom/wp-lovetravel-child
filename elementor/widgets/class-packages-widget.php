<?php
/**
 * Packages Widget (Standalone)
 *
 * Custom Elementor widget for adventure packages grid with custom layouts.
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
 * Packages Widget Class
 *
 * Standalone Elementor widget with built-in custom layout support.
 * No dependency on nd-travel Packages widget hooks.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/widgets
 */
class LoveTravelChild_Packages_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since  2.2.0
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lovetravel-child-packages';
	}

	/**
	 * Get widget title.
	 *
	 * @since  2.2.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Adventure Packages', 'lovetravel-child' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  2.2.0
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
	 * Matches nd-travel Packages widget controls exactly (1:1).
	 *
	 * @since  2.2.0
	 */
	protected function register_controls() {
		// Main Options Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Main Options', 'lovetravel-child' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'packages_layout',
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
			'packages_width',
			array(
				'label'   => esc_html__( 'Width', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'nd_travel_width_25_percentage',
				'options' => array(
					'nd_travel_width_100_percentage' => esc_html__( '1 Column', 'lovetravel-child' ),
					'nd_travel_width_50_percentage'  => esc_html__( '2 Columns', 'lovetravel-child' ),
					'nd_travel_width_33_percentage'  => esc_html__( '3 Columns', 'lovetravel-child' ),
					'nd_travel_width_25_percentage'  => esc_html__( '4 Columns', 'lovetravel-child' ),
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'large',
				'separator' => 'none',
			)
		);

		$this->add_control(
			'packages_order',
			array(
				'label'   => esc_html__( 'Order', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'DESC', 'lovetravel-child' ),
					'ASC'  => esc_html__( 'ASC', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'packages_orderby',
			array(
				'label'   => esc_html__( 'Order By', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'ID'            => esc_html__( 'ID', 'lovetravel-child' ),
					'author'        => esc_html__( 'Author', 'lovetravel-child' ),
					'title'         => esc_html__( 'Title', 'lovetravel-child' ),
					'name'          => esc_html__( 'Name', 'lovetravel-child' ),
					'type'          => esc_html__( 'Type', 'lovetravel-child' ),
					'date'          => esc_html__( 'Date', 'lovetravel-child' ),
					'modified'      => esc_html__( 'Modified', 'lovetravel-child' ),
					'rand'          => esc_html__( 'Random', 'lovetravel-child' ),
					'comment_count' => esc_html__( 'Comment Count', 'lovetravel-child' ),
				),
			)
		);

		$this->add_control(
			'packages_qnt',
			array(
				'label'   => esc_html__( 'Posts Per Page', 'lovetravel-child' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => -1,
				'min'     => -1,
				'max'     => 20,
				'step'    => 1,
			)
		);

		$this->add_control(
			'packages_id',
			array(
				'label' => esc_html__( 'ID', 'lovetravel-child' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
				'min'   => 1,
				'max'   => 9000,
				'step'  => 1,
			)
		);

		$this->add_control(
			'destination_id',
			array(
				'label' => esc_html__( 'Destination ID', 'lovetravel-child' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
				'min'   => 0,
				'max'   => 9000,
				'step'  => 1,
			)
		);

		$this->add_control(
			'typology_slug',
			array(
				'label' => esc_html__( 'Typology Slug', 'lovetravel-child' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'enable_search_filters',
			array(
				'label'        => esc_html__( 'Enable Search Filters', 'lovetravel-child' ),
				'description'  => esc_html__( 'Allow filtering via URL parameters (price, dates, taxonomies)', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_no_results',
			array(
				'label'        => esc_html__( 'Show No Results Message', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'No', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'no_results_text',
			array(
				'label'       => esc_html__( 'No Results Text', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'No adventures found matching your criteria. Please try adjusting your filters.', 'lovetravel-child' ),
				'condition'   => array(
					'show_no_results' => 'yes',
				),
			)
		);

		$this->add_control(
			'load_more_show',
			array(
				'label'        => esc_html__( 'Load More', 'lovetravel-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'lovetravel-child' ),
				'label_off'    => esc_html__( 'Hide', 'lovetravel-child' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'load_more_text',
			array(
				'label'       => esc_html__( 'Button Text', 'lovetravel-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Load More', 'lovetravel-child' ),
				'placeholder' => esc_html__( 'Load More', 'lovetravel-child' ),
				'condition'   => array(
					'load_more_show' => 'yes',
				),
			)
		);

		$this->add_control(
			'load_more_posts_per_page',
			array(
				'label'     => esc_html__( 'Posts Per Load', 'lovetravel-child' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'condition' => array(
					'load_more_show' => 'yes',
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

		// Check if in Elementor editor
		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		// Enqueue required scripts and styles (only if nd-travel plugin is active)
		if ( function_exists( 'nd_travel_scripts' ) || post_type_exists( 'nd_travel_cpt_1' ) ) {
			wp_enqueue_style(
				'nd_travel_style',
				plugins_url( 'nd-travel/assets/css/style.css' ),
				array(),
				null
			);
			wp_enqueue_script( 'masonry' );
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script(
				'nd_travel_elementor_packages_js',
				plugins_url( 'nd-travel/addons/elementor/packages/js/packages.js' ),
				array( 'jquery', 'masonry', 'imagesloaded' ),
				null,
				true
			);
		}

		// Extract settings
		$nd_travel_postgrid_order   = ! empty( $settings['packages_order'] ) ? $settings['packages_order'] : 'DESC';
		$nd_travel_postgrid_orderby = ! empty( $settings['packages_orderby'] ) ? $settings['packages_orderby'] : 'date';
		$packages_qnt               = isset( $settings['packages_qnt'] ) ? $settings['packages_qnt'] : -1;
		$packages_width             = ! empty( $settings['packages_width'] ) ? $settings['packages_width'] : 'nd_travel_width_25_percentage';
		$packages_layout            = ! empty( $settings['packages_layout'] ) ? $settings['packages_layout'] : 'layout-1';
		$packages_id                = ! empty( $settings['packages_id'] ) ? $settings['packages_id'] : '';
		$nd_travel_destination_id   = ! empty( $settings['destination_id'] ) ? $settings['destination_id'] : '';
		$nd_travel_typology_slug    = ! empty( $settings['typology_slug'] ) ? $settings['typology_slug'] : '';
		$packagesgrid_image_size    = ! empty( $settings['thumbnail_size'] ) ? $settings['thumbnail_size'] : 'large';

		// Build query args
		$args = array(
			'post_type'      => 'nd_travel_cpt_1',
			'posts_per_page' => $packages_qnt,
			'order'          => $nd_travel_postgrid_order,
			'orderby'        => $nd_travel_postgrid_orderby,
		);

		// Handle search query
		if ( is_search() && ! empty( get_search_query() ) ) {
			$args['s'] = get_search_query();
		}

		// Add specific package ID if set
		if ( ! empty( $packages_id ) ) {
			$args['p'] = $packages_id;
		}

		// Filter by destination
		if ( ! empty( $nd_travel_destination_id ) ) {
			$nd_travel_archive_form_destinations_array = array( $nd_travel_destination_id );

			// Handle current destination (value = 1)
			if ( 1 === absint( $nd_travel_destination_id ) ) {
				$nd_travel_destination_id                     = get_the_ID();
				$nd_travel_archive_form_destinations_array[0] = get_the_ID();
			}

			// Include child destinations if function exists
			if ( function_exists( 'nd_travel_get_destinations_with_parent' ) ) {
				$nd_travel_children_destinations_array = nd_travel_get_destinations_with_parent( $nd_travel_destination_id );
				if ( ! empty( $nd_travel_children_destinations_array ) ) {
					$nd_travel_destinations_query_i = 1;
					foreach ( $nd_travel_children_destinations_array as $nd_travel_children_destination_id ) {
						$nd_travel_archive_form_destinations_array[ $nd_travel_destinations_query_i ] = $nd_travel_children_destination_id;
						++$nd_travel_destinations_query_i;
					}
				}
			}

			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'nd_travel_meta_box_destinations',
					'type'  => 'numeric',
					'value' => $nd_travel_archive_form_destinations_array,
				),
			);
		} elseif ( ! empty( $nd_travel_typology_slug ) ) {
			// Filter by typology
			$nd_travel_get_current_typology_slug = $nd_travel_typology_slug;

			// Handle current typology (value = 1)
			if ( 1 === absint( $nd_travel_typology_slug ) ) {
				$nd_travel_get_current_typology_id   = get_the_ID();
				$nd_travel_get_current_typology_slug = get_post_field( 'post_name', $nd_travel_get_current_typology_id );
			}

			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'nd_travel_meta_box_typologies',
					'value'   => $nd_travel_get_current_typology_slug,
					'compare' => 'LIKE',
				),
			);
		}

		// Initialize meta_query and tax_query arrays if not set
		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
		if ( ! isset( $args['tax_query'] ) ) {
			$args['tax_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		// SEARCH FILTERS: Process URL parameters from filter widgets (if enabled)
		$enable_search_filters = isset( $settings['enable_search_filters'] ) && 'yes' === $settings['enable_search_filters'];

		if ( $enable_search_filters ) {
			// Price Range Filter
			if ( isset( $_GET['price_min'] ) || isset( $_GET['price_max'] ) ) {
			$price_min = isset( $_GET['price_min'] ) ? intval( $_GET['price_min'] ) : 0;
			$price_max = isset( $_GET['price_max'] ) ? intval( $_GET['price_max'] ) : 999999;

			$args['meta_query'][] = array(
				'key'     => 'nd_travel_meta_box_price',
				'value'   => array( $price_min, $price_max ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
		}

		// Date Range Filter
		if ( isset( $_GET['date_from'] ) || isset( $_GET['date_to'] ) ) {
			$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '';
			$date_to   = isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '';

			// Convert YYYY-MM-DD to YYYYMMDD for nd-travel format
			$date_from_formatted = '';
			$date_to_formatted   = '';
			if ( ! empty( $date_from ) ) {
				$date_from_formatted = str_replace( '-', '', $date_from );
			}
			if ( ! empty( $date_to ) ) {
				$date_to_formatted = str_replace( '-', '', $date_to );
			}

			// Use overlap logic: package_start <= requested_end AND package_end >= requested_start
			if ( ! empty( $date_from_formatted ) || ! empty( $date_to_formatted ) ) {
				$sub_queries = array();
				if ( ! empty( $date_to_formatted ) ) {
					$sub_queries[] = array(
						'key'     => 'nd_travel_meta_box_availability_from',
						'value'   => $date_to_formatted,
						'type'    => 'NUMERIC',
						'compare' => '<=',
					);
				}
				if ( ! empty( $date_from_formatted ) ) {
					$sub_queries[] = array(
						'key'     => 'nd_travel_meta_box_availability_to',
						'value'   => $date_from_formatted,
						'type'    => 'NUMERIC',
						'compare' => '>=',
					);
				}

				if ( ! empty( $sub_queries ) ) {
					$sub_queries['relation'] = 'AND';
					$args['meta_query'][] = $sub_queries; // package overlaps requested range
				}
			}
		}

		// Taxonomy / meta-based Filters
		// Support legacy taxonomy params (term IDs) and modern meta-stored slugs for destinations/typologies.
		// cpt_1_tax_0 => destinations (can be term IDs or slugs)
		// cpt_1_tax_1/2/3 => real taxonomies (term IDs)
		// cpt_2 => typologies (expects array of slugs)

		// Destinations: handle as term IDs OR slugs stored in meta key nd_travel_meta_box_destinations
		if ( isset( $_GET['cpt_1_tax_0'] ) && ! empty( $_GET['cpt_1_tax_0'] ) ) {
			$values = (array) $_GET['cpt_1_tax_0'];
			// Detect numeric IDs
			$all_numeric = array_reduce( $values, function( $carry, $item ) {
				return $carry && is_numeric( $item );
			}, true );

			if ( $all_numeric ) {
				$term_ids = array_map( 'intval', $values );
				$args['tax_query'][] = array(
					'taxonomy' => 'nd_travel_cpt_1_tax_0',
					'field'    => 'term_id',
					'terms'    => $term_ids,
					'operator' => 'IN',
				);
			} else {
				// Treat as slugs stored in meta (comma separated). Build OR meta queries matching each slug.
				$meta_sub = array();
				foreach ( $values as $slug ) {
					$meta_sub[] = array(
						'key'     => 'nd_travel_meta_box_destinations',
						'value'   => sanitize_text_field( $slug ),
						'compare' => 'LIKE',
					);
				}
				if ( ! empty( $meta_sub ) ) {
					$meta_sub['relation'] = 'OR';
					$args['meta_query'][] = $meta_sub;
				}
			}
		}

		// Typologies: expect param 'cpt_2' (array of slugs stored in meta nd_travel_meta_box_typologies)
		if ( isset( $_GET['cpt_2'] ) && ! empty( $_GET['cpt_2'] ) ) {
			$typology_slugs = (array) $_GET['cpt_2'];
			$meta_sub        = array();
			foreach ( $typology_slugs as $slug ) {
				$meta_sub[] = array(
					'key'     => 'nd_travel_meta_box_typologies',
					'value'   => sanitize_text_field( $slug ),
					'compare' => 'LIKE',
				);
			}
			if ( ! empty( $meta_sub ) ) {
				$meta_sub['relation'] = 'OR';
				$args['meta_query'][]   = $meta_sub;
			}
		}

		// Other taxonomy params that remain as term IDs
		$taxonomy_filters = array(
			'cpt_1_tax_1' => 'nd_travel_cpt_1_tax_1', // Durations
			'cpt_1_tax_2' => 'nd_travel_cpt_1_tax_2', // Difficulties
			'cpt_1_tax_3' => 'nd_travel_cpt_1_tax_3', // Min Ages
		);

		foreach ( $taxonomy_filters as $param => $taxonomy ) {
			if ( isset( $_GET[ $param ] ) && ! empty( $_GET[ $param ] ) ) {
				$term_ids = (array) $_GET[ $param ];
				$term_ids = array_map( 'intval', $term_ids );

				if ( ! empty( $term_ids ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_ids,
						'operator' => 'IN',
					);
				}
			}
		}

		// Set tax_query relation if multiple taxonomies
		if ( count( $args['tax_query'] ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}

		// Set meta_query relation if multiple meta queries
		if ( count( $args['meta_query'] ) > 1 ) {
			$args['meta_query']['relation'] = 'AND';
		}
		// End search filters
		}

		// Run query
		$the_query = new WP_Query( $args );

		// Start output
		echo '<div class="nd_travel_section nd_travel_masonry_content">';

		// Check if query has posts
		if ( ! $the_query->have_posts() ) {
			// No results - show message if enabled
			$show_no_results = isset( $settings['show_no_results'] ) && 'yes' === $settings['show_no_results'];
			if ( $show_no_results ) {
				$no_results_text = isset( $settings['no_results_text'] ) ? $settings['no_results_text'] : esc_html__( 'No adventures found matching your criteria. Please try adjusting your filters.', 'lovetravel-child' );
				echo '<div class="lovetravel-no-results" style="padding: 40px 20px; text-align: center; width: 100%;">';
				echo '<p style="font-size: 16px; color: #666; margin: 0;">' . esc_html( $no_results_text ) . '</p>';
				echo '</div>';
			}
		}

		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			// Package info
			$nd_travel_id        = get_the_ID();
			$nd_travel_title     = get_the_title();
			$nd_travel_excerpt   = get_the_excerpt();
			$nd_travel_permalink = get_permalink( $nd_travel_id );

			// Color
			$nd_travel_meta_box_color = get_post_meta( get_the_ID(), 'nd_travel_meta_box_color', true );
			if ( '' === $nd_travel_meta_box_color ) {
				$nd_travel_meta_box_color = '#EA5B10';
			}

			// Meta fields
			$nd_travel_meta_box_tab_gallery_content    = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_gallery_content', true );
			$nd_travel_meta_box_tab_map_content        = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_map_content', true );
			$nd_travel_meta_box_featured_image_replace = get_post_meta( get_the_ID(), 'nd_travel_meta_box_featured_image_replace', true );

			// Load layout template (pass $is_edit_mode for conditional rendering)
			$nd_travel_layout_path = get_stylesheet_directory() . '/elementor/templates/packages/' . $packages_layout . '.php';
			if ( file_exists( $nd_travel_layout_path ) ) {
				include $nd_travel_layout_path;
			} else {
				echo '<p>' . esc_html__( 'Layout template not found', 'lovetravel-child' ) . '</p>';
			}
		}

		echo '</div>';

		// Render Load More button if enabled
		if ( 'yes' === $settings['load_more_show'] ) {
			$load_more_text          = ! empty( $settings['load_more_text'] ) ? $settings['load_more_text'] : esc_html__( 'Load More', 'lovetravel-child' );
			$load_more_posts_per_page = ! empty( $settings['load_more_posts_per_page'] ) ? $settings['load_more_posts_per_page'] : 4;

			// Check if there are more posts to load
			$total_posts = $the_query->found_posts;
			$has_more    = $packages_qnt < $total_posts; // Show button only if more posts available

			if ( $has_more ) {
				?>
				<div class="lovetravel-load-more-wrapper" style="text-align: center; margin-top: 30px;">
					<button 
						class="lovetravel-load-more-btn" 
						data-widget-id="<?php echo esc_attr( $this->get_id() ); ?>"
						data-posts-per-page="<?php echo esc_attr( $load_more_posts_per_page ); ?>"
						data-order="<?php echo esc_attr( $nd_travel_postgrid_order ); ?>"
						data-orderby="<?php echo esc_attr( $nd_travel_postgrid_orderby ); ?>"
						data-width="<?php echo esc_attr( $packages_width ); ?>"
						data-layout="<?php echo esc_attr( $packages_layout ); ?>"
						data-packages-id="<?php echo esc_attr( $packages_id ); ?>"
						data-destination-id="<?php echo esc_attr( $nd_travel_destination_id ); ?>"
						data-typology-slug="<?php echo esc_attr( $nd_travel_typology_slug ); ?>"
						data-image-size="<?php echo esc_attr( $packagesgrid_image_size ); ?>"
						data-loading-text="<?php echo esc_attr__( 'Loading...', 'lovetravel-child' ); ?>"
						style="padding: 12px 30px; background: #EA5B10; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 500; transition: background 0.3s ease;"
					>
						<?php echo esc_html( $load_more_text ); ?>
					</button>
				</div>
				<?php
			}
		}

		wp_reset_postdata();
	}

	/**
	 * AJAX handler for Load More functionality.
	 *
	 * Outputs JSON response with HTML for additional packages.
	 *
	 * @since 2.2.0
	 */
	public function ajax_load_more_packages() {
		// Verify nonce
		check_ajax_referer( 'lovetravel_load_more_nonce', 'nonce' );

		// Get parameters from AJAX request
		$offset              = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		$posts_per_page      = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 4;
		$packages_order      = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : 'DESC';
		$packages_orderby    = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date';
		$packages_width      = isset( $_POST['width'] ) ? sanitize_text_field( wp_unslash( $_POST['width'] ) ) : 'nd_travel_width_25_percentage';
		$packages_layout     = isset( $_POST['layout'] ) ? sanitize_text_field( wp_unslash( $_POST['layout'] ) ) : 'layout-1';
		$packages_id         = isset( $_POST['packages_id'] ) ? sanitize_text_field( wp_unslash( $_POST['packages_id'] ) ) : '';
		$destination_id      = isset( $_POST['destination_id'] ) ? sanitize_text_field( wp_unslash( $_POST['destination_id'] ) ) : '';
		$typology_slug       = isset( $_POST['typology_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['typology_slug'] ) ) : '';
		$packagesgrid_image_size = isset( $_POST['image_size'] ) ? sanitize_text_field( wp_unslash( $_POST['image_size'] ) ) : 'large';
		$is_editor           = isset( $_POST['is_editor'] ) && '1' === $_POST['is_editor'];

		// Build query args
		$args = array(
			'post_type'      => 'nd_travel_cpt_1',
			'posts_per_page' => $posts_per_page,
			'offset'         => $offset,
			'order'          => $packages_order,
			'orderby'        => $packages_orderby,
		);

		// Add specific package ID if set
		if ( ! empty( $packages_id ) ) {
			$args['p'] = $packages_id;
		}

		// Filter by destination
		if ( ! empty( $destination_id ) ) {
			$nd_travel_archive_form_destinations_array = array( $destination_id );

			// Handle current destination (value = 1)
			if ( 1 === absint( $destination_id ) ) {
				$destination_id                               = get_the_ID();
				$nd_travel_archive_form_destinations_array[0] = get_the_ID();
			}

			// Include child destinations if function exists
			if ( function_exists( 'nd_travel_get_destinations_with_parent' ) ) {
				$nd_travel_children_destinations_array = nd_travel_get_destinations_with_parent( $destination_id );
				if ( ! empty( $nd_travel_children_destinations_array ) ) {
					$nd_travel_destinations_query_i = 1;
					foreach ( $nd_travel_children_destinations_array as $nd_travel_children_destination_id ) {
						$nd_travel_archive_form_destinations_array[ $nd_travel_destinations_query_i ] = $nd_travel_children_destination_id;
						++$nd_travel_destinations_query_i;
					}
				}
			}

			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'nd_travel_meta_box_destinations',
					'type'  => 'numeric',
					'value' => $nd_travel_archive_form_destinations_array,
				),
			);
		} elseif ( ! empty( $typology_slug ) ) {
			// Filter by typology
			$nd_travel_get_current_typology_slug = $typology_slug;

			// Handle current typology (value = 1)
			if ( 1 === absint( $typology_slug ) ) {
				$nd_travel_get_current_typology_id   = get_the_ID();
				$nd_travel_get_current_typology_slug = get_post_field( 'post_name', $nd_travel_get_current_typology_id );
			}

			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'nd_travel_meta_box_typologies',
					'value'   => $nd_travel_get_current_typology_slug,
					'compare' => 'LIKE',
				),
			);
		}

		// Run query
		$the_query = new WP_Query( $args );

		// Start output buffering
		ob_start();

		// Check if query has posts - show message if not
		if ( ! $the_query->have_posts() ) {
			echo '<div class="lovetravel-no-results" style="padding: 40px 20px; text-align: center; width: 100%;">';
			echo '<p style="font-size: 16px; color: #666; margin: 0;">' . esc_html__( 'No adventures found matching your criteria. Please try adjusting your filters.', 'lovetravel-child' ) . '</p>';
			echo '</div>';
		}

		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			// Package info
			$nd_travel_id        = get_the_ID();
			$nd_travel_title     = get_the_title();
			$nd_travel_excerpt   = get_the_excerpt();
			$nd_travel_permalink = get_permalink( $nd_travel_id );

			// Color
			$nd_travel_meta_box_color = get_post_meta( get_the_ID(), 'nd_travel_meta_box_color', true );
			if ( '' === $nd_travel_meta_box_color ) {
				$nd_travel_meta_box_color = '#EA5B10';
			}

			// Meta fields
			$nd_travel_meta_box_tab_gallery_content    = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_gallery_content', true );
			$nd_travel_meta_box_tab_map_content        = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_map_content', true );
			$nd_travel_meta_box_featured_image_replace = get_post_meta( get_the_ID(), 'nd_travel_meta_box_featured_image_replace', true );

			// Load layout template (use $is_editor from AJAX request)
			$is_edit_mode          = $is_editor; // Use editor context from AJAX
			$nd_travel_layout_path = get_stylesheet_directory() . '/elementor/templates/packages/' . $packages_layout . '.php';
			if ( file_exists( $nd_travel_layout_path ) ) {
				include $nd_travel_layout_path;
			}
		}

		$html = ob_get_clean();

		wp_reset_postdata();

		// Check if there are more posts
		$total_posts = $the_query->found_posts;
		$has_more    = ( $offset + $posts_per_page ) < $total_posts;

		// Send JSON response
		wp_send_json_success(
			array(
				'html'     => $html,
				'has_more' => $has_more,
			)
		);
	}
}
