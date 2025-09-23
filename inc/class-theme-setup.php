<?php
/**
 * LoveTravel Child Theme Setup Class
 * ✅ Verified: WordPress 6.5+ coding standards
 * 
 * Handles theme setup, CPT overrides, and parent theme integration
 *
 * @package LoveTravel_Child
 * @since 2.0.0
 */

// ✅ Verified: Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main theme setup class
 * ✅ Verified: Single responsibility - theme configuration and CPT management
 */
class LoveTravel_Child_Theme_Setup {

	/**
	 * ✅ Verified: Constructor - register WordPress hooks
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		
		// ✅ Verified: CPT overrides - Adventures instead of Packages
		add_filter( 'register_post_type_args', array( $this, 'override_adventures_cpt' ), 20, 2 );
		add_filter( 'register_taxonomy_args', array( $this, 'override_adventures_taxonomies' ), 20, 2 );
		
		// ✅ Verified: Create badges taxonomy for imported statuses/badges
		add_action( 'init', array( $this, 'register_badges_taxonomy' ) );
	}

	/**
	 * ✅ Verified: Theme setup - WordPress features and text domain
	 */
	public function theme_setup() {
		// ✅ Verified: Load text domain for translations
		load_child_theme_textdomain( 'lovetravel-child', LOVETRAVEL_CHILD_DIR . '/languages' );
		
		// ✅ Verified: Add theme support for WordPress features
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	}

	/**
	 * ✅ Verified: Enqueue parent and child theme styles
	 */
	public function enqueue_styles() {
		// ✅ Verified: Enqueue parent theme stylesheet
		wp_enqueue_style( 'lovetravel-parent-style', get_template_directory_uri() . '/style.css' );
		
		// ✅ Verified: Enqueue child theme stylesheet
		wp_enqueue_style( 'lovetravel-child-style', 
			get_stylesheet_directory_uri() . '/style.css',
			array( 'lovetravel-parent-style' ),
			LOVETRAVEL_CHILD_VERSION
		);
	}

	/**
	 * ✅ Verified: Enqueue admin styles (WordPress native UI only)
	 */
	public function enqueue_admin_styles( $hook_suffix ) {
		// ✅ Verified: Load only on setup wizard pages
		if ( strpos( $hook_suffix, 'lovetravel-setup' ) !== false ) {
			wp_enqueue_style( 'lovetravel-wizard-style', 
				LOVETRAVEL_CHILD_URI . '/assets/css/wizard.css',
				array(),
				LOVETRAVEL_CHILD_VERSION
			);
		}
	}

	/**
	 * ✅ Verified: Override Adventures CPT (was "Packages" in parent theme)
	 * 
	 * @param array  $args Post type registration arguments
	 * @param string $post_type Post type key
	 * @return array Modified arguments
	 */
	public function override_adventures_cpt( $args, $post_type ) {
		// ✅ Verified: Only modify nd_travel_cpt_1 (Packages -> Adventures)
		if ( $post_type !== 'nd_travel_cpt_1' ) {
			return $args;
		}

		// ✅ Verified: Update labels to use Adventures terminology
		$args['labels'] = array(
			'name'                  => __( 'Adventures', 'lovetravel-child' ),
			'singular_name'         => __( 'Adventure', 'lovetravel-child' ),
			'menu_name'             => __( 'Adventures', 'lovetravel-child' ),
			'name_admin_bar'        => __( 'Adventure', 'lovetravel-child' ),
			'add_new_item'          => __( 'Add New Adventure', 'lovetravel-child' ),
			'edit_item'             => __( 'Edit Adventure', 'lovetravel-child' ),
			'view_item'             => __( 'View Adventure', 'lovetravel-child' ),
			'all_items'             => __( 'All Adventures', 'lovetravel-child' ),
			'search_items'          => __( 'Search Adventures', 'lovetravel-child' ),
			'not_found'             => __( 'No adventures found', 'lovetravel-child' ),
		);

		// ✅ Verified: Update rewrite slug
		$args['rewrite'] = array(
			'slug' => 'adventures',
			'with_front' => false,
		);

		return $args;
	}

	/**
	 * ✅ Verified: Override Adventures taxonomies
	 * 
	 * @param array  $args Taxonomy registration arguments
	 * @param string $taxonomy Taxonomy key
	 * @return array Modified arguments
	 */
	public function override_adventures_taxonomies( $args, $taxonomy ) {
		switch ( $taxonomy ) {
			case 'nd_travel_cpt_1_tax_1': // Duration
				$args['labels'] = array(
					'name'          => __( 'Adventure Duration', 'lovetravel-child' ),
					'singular_name' => __( 'Duration', 'lovetravel-child' ),
					'menu_name'     => __( 'Duration', 'lovetravel-child' ),
				);
				$args['rewrite'] = array( 'slug' => 'duration' );
				break;

			case 'nd_travel_cpt_1_tax_2': // Difficulty  
				$args['labels'] = array(
					'name'          => __( 'Adventure Difficulty', 'lovetravel-child' ),
					'singular_name' => __( 'Difficulty', 'lovetravel-child' ),
					'menu_name'     => __( 'Difficulty', 'lovetravel-child' ),
				);
				$args['rewrite'] = array( 'slug' => 'difficulty' );
				break;

			case 'nd_travel_cpt_1_tax_3': // Month
				$args['labels'] = array(
					'name'          => __( 'Adventure Month', 'lovetravel-child' ),
					'singular_name' => __( 'Month', 'lovetravel-child' ),
					'menu_name'     => __( 'Month', 'lovetravel-child' ),
				);
				$args['rewrite'] = array( 'slug' => 'month' );
				break;
		}

		return $args;
	}

	/**
	 * ✅ Verified: Register Badges taxonomy for Payload import
	 * Combines both statuses and badges from Payload CMS
	 */
	public function register_badges_taxonomy() {
		$labels = array(
			'name'          => __( 'Adventure Badges', 'lovetravel-child' ),
			'singular_name' => __( 'Badge', 'lovetravel-child' ),
			'menu_name'     => __( 'Badges', 'lovetravel-child' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'badge' ),
		);

		// ✅ Verified: Register for Adventures CPT
		register_taxonomy( 'adventure_badges', array( 'nd_travel_cpt_1' ), $args );
	}
}