<?php
/**
 * Taxonomy Manager
 *
 * Manages custom taxonomy registration, modification, and term population.
 * Provides a centralized, reusable system for taxonomy operations.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Taxonomy Manager Class
 *
 * Handles registration of new taxonomies and modification of existing ones.
 * Provides methods for adding terms programmatically.
 */
class LoveTravelChildTaxonomyManager {

	/**
	 * Array of taxonomies to register.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    array $taxonomies Taxonomies configuration.
	 */
	private $taxonomies = array();

	/**
	 * Initialize the taxonomy manager.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->setupTaxonomies();
	}

	/**
	 * Setup taxonomy configurations.
	 *
	 * Define all custom taxonomies here.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function setupTaxonomies() {
		// Month taxonomy (new)
		$this->taxonomies['nd_travel_cpt_1_tax_4'] = array(
			'post_type' => 'nd_travel_cpt_1',
			'labels'    => array(
				'name'              => __( 'Months', 'lovetravel-child' ),
				'singular_name'     => __( 'Month', 'lovetravel-child' ),
				'menu_name'         => __( 'Months', 'lovetravel-child' ),
				'all_items'         => __( 'All Months', 'lovetravel-child' ),
				'edit_item'         => __( 'Edit Month', 'lovetravel-child' ),
				'view_item'         => __( 'View Month', 'lovetravel-child' ),
				'update_item'       => __( 'Update Month', 'lovetravel-child' ),
				'add_new_item'      => __( 'Add New Month', 'lovetravel-child' ),
				'new_item_name'     => __( 'New Month Name', 'lovetravel-child' ),
				'search_items'      => __( 'Search Months', 'lovetravel-child' ),
				'popular_items'     => __( 'Popular Months', 'lovetravel-child' ),
				'not_found'         => __( 'No months found', 'lovetravel-child' ),
				'back_to_items'     => __( '← Back to Months', 'lovetravel-child' ),
			),
			'args'      => array(
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'month-packages' ),
			),
			'terms'     => array(
				'January'   => 'january',
				'February'  => 'february',
				'March'     => 'march',
				'April'     => 'april',
				'May'       => 'may',
				'June'      => 'june',
				'July'      => 'july',
				'August'    => 'august',
				'September' => 'september',
				'October'   => 'october',
				'November'  => 'november',
				'December'  => 'december',
			),
		);
	}

	/**
	 * Register all configured taxonomies.
	 *
	 * Hooks into 'init' action at priority 9 (before nd-travel at 10).
	 *
	 * @since 2.0.0
	 */
	public function registerTaxonomies() {
		foreach ( $this->taxonomies as $taxonomy => $config ) {
			// Register taxonomy
			$args = isset( $config['args'] ) && is_array( $config['args'] ) ? $config['args'] : array();
			// Ensure labels are passed under 'labels' key
			$args['labels'] = isset( $config['labels'] ) && is_array( $config['labels'] ) ? $config['labels'] : array();

			register_taxonomy( $taxonomy, $config['post_type'], $args );

			// Register taxonomy for object type
			register_taxonomy_for_object_type( $taxonomy, $config['post_type'] );

			// Populate terms if defined
			if ( ! empty( $config['terms'] ) ) {
				$this->populateTerms( $taxonomy, $config['terms'] );
			}
		}

		// Flush rewrite rules after registering taxonomies
		// Only flush once per request to avoid performance impact
		if ( ! did_action( 'lovetravel_child_taxonomies_flushed' ) ) {
			flush_rewrite_rules( false );
			do_action( 'lovetravel_child_taxonomies_flushed' );
		}
	}

	/**
	 * Populate taxonomy with predefined terms.
	 *
	 * Only creates terms if they don't exist.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  string $taxonomy Taxonomy slug.
	 * @param  array  $terms    Terms to create (name => slug).
	 */
	private function populateTerms( $taxonomy, $terms ) {
		foreach ( $terms as $name => $slug ) {
			// Check if term exists
			if ( ! term_exists( $slug, $taxonomy ) ) {
				wp_insert_term(
					$name,
					$taxonomy,
					array(
						'slug' => $slug,
					)
				);
			}
		}
	}

	/**
	 * Modify existing nd-travel taxonomy labels.
	 *
	 * Filters taxonomy registration arguments to override plugin labels.
	 *
	 * @since 2.0.0
	 * @param array  $args     Taxonomy registration arguments.
	 * @param string $taxonomy Taxonomy slug.
	 * @return array Modified arguments.
	 */
	public function modifyTaxonomyLabels( $args, $taxonomy ) {
		// Ensure labels array exists
		if ( ! isset( $args['labels'] ) ) {
			$args['labels'] = array();
		}

		// Override labels for nd-travel taxonomies
		switch ( $taxonomy ) {
			case 'nd_travel_cpt_1_tax_1': // Durations
				$args['labels'] = array_merge(
					(array) $args['labels'],
					array(
						'name'          => __( 'Durations', 'lovetravel-child' ),
						'singular_name' => __( 'Duration', 'lovetravel-child' ),
						'menu_name'     => __( 'Durations', 'lovetravel-child' ),
					)
				);
				$args['rewrite'] = array( 'slug' => 'durations' );
				break;

			case 'nd_travel_cpt_1_tax_2': // Difficulty
				$args['labels'] = array_merge(
					(array) $args['labels'],
					array(
						'name'          => __( 'Difficulties', 'lovetravel-child' ),
						'singular_name' => __( 'Difficulty', 'lovetravel-child' ),
						'menu_name'     => __( 'Difficulties', 'lovetravel-child' ),
					)
				);
				$args['rewrite'] = array( 'slug' => 'difficulty' );
				break;

			case 'nd_travel_cpt_1_tax_3': // Min Age
				$args['labels'] = array_merge(
					(array) $args['labels'],
					array(
						'name'          => __( 'Min. Ages', 'lovetravel-child' ),
						'singular_name' => __( 'Min. Age', 'lovetravel-child' ),
						'menu_name'     => __( 'Min. Ages', 'lovetravel-child' ),
					)
				);
				$args['rewrite'] = array( 'slug' => 'min-age' );
				break;
		}

		return $args;
	}

	/**
	 * Flush rewrite rules after taxonomy changes.
	 *
	 * Triggered on theme activation/switch.
	 *
	 * @since 2.0.0
	 */
	public function flushRewriteRules() {
		flush_rewrite_rules( false );
	}
}
