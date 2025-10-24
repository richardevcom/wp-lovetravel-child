<?php
/**
 * Elementor Packages Widget Extension
 *
 * Extends the nd-travel Packages widget to use custom layout from child theme.
 * When enabled in child theme settings, replaces the default plugin layout
 * with our custom layout template.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

/**
 * Extends nd-travel Packages Elementor widget.
 *
 * Hooks into the existing packages widget to:
 * - Replace default layout with child theme custom layout when enabled
 * - Maintain compatibility with plugin updates
 * - Controlled by global setting: lovetravel_child_enable_custom_packages_layout
 *
 * @since      2.0.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 */
class LoveTravelChildElementorPackagesWidgetExtension {

	/**
	 * The ID of this theme.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $theme_name    The ID of this theme.
	 */
	private $theme_name;

	/**
	 * The version of this theme.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this theme.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string $theme_name       The name of this theme.
	 * @param    string $version    The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {
		$this->theme_name = $theme_name;
		$this->version    = $version;
	}

	/**
	 * One-time migration to replace any saved "layout-2" values in content/meta
	 * with "layout-1" to avoid the plugin attempting to include a missing file.
	 *
	 * This runs on admin_init once and sets an option to avoid repeating.
	 *
	 * @since 2.0.0
	 */
	public function migrateSavedLayouts() {
		if ( get_option( 'lovetravel_child_packages_layout_2_migrated', 0 ) ) {
			return;
		}

		// Only run in admin and for users that can manage options.
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $wpdb;

		$needle = '"packages_layout":"layout-2"';
		$replacement = '"packages_layout":"layout-1"';

		// Update post_content in posts table (covers templates stored in content)
		$posts_to_update = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE %s", '%' . $wpdb->esc_like( $needle ) . '%' ) );
		if ( ! empty( $posts_to_update ) ) {
			foreach ( $posts_to_update as $post_id ) {
				$old = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id ) );
				$new = str_replace( $needle, $replacement, $old );
				$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $post_id ) );
			}
		}

		// Update postmeta values (Elementor often stores data in postmeta)
		$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_value LIKE %s", '%' . $wpdb->esc_like( $needle ) . '%' ) );
		if ( ! empty( $meta_ids ) ) {
			foreach ( $meta_ids as $meta_id ) {
				$old = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_id = %d", $meta_id ) );
				$new = str_replace( $needle, $replacement, $old );
				$wpdb->update( $wpdb->postmeta, array( 'meta_value' => $new ), array( 'meta_id' => $meta_id ) );
			}
		}

		// Mark migration complete so we don't run again
		update_option( 'lovetravel_child_packages_layout_2_migrated', 1 );
	}

	/**
	 * Determine if widget should render on frontend.
	 *
	 * Returns false when custom layout is enabled globally, preventing default rendering.
	 *
	 * Hooks: elementor/frontend/widget/should_render (filter)
	 *
	 * @since    2.0.0
	 * @param    bool   $should_render  Whether to render the widget.
	 * @param    object $widget         Widget instance.
	 * @return   bool                   False to prevent rendering, true to allow.
	 */
	public function shouldRenderWidget( $should_render, $widget ) {
		// Only process packages widget
		if ( 'packages' !== $widget->get_name() ) {
			return $should_render;
		}

		// Check global setting
		$use_custom_layout = get_option( 'lovetravel_child_enable_custom_packages_layout', 0 );

		// If custom layout enabled globally, render custom layout
		if ( $use_custom_layout ) {
			$settings = $widget->get_settings_for_display();
			$this->renderCustomLayout( $widget, $settings );
			return false; // Prevent default rendering
		}

		return $should_render;
	}

	/**
	 * Intercept widget rendering for custom layout in editor.
	 *
	 * Checks if custom layout is enabled globally and renders custom template.
	 *
	 * Hooks: elementor/widget/render_content (filter)
	 *
	 * @since    2.0.0
	 * @param    string $content   Widget content from plugin.
	 * @param    object $widget    Widget instance.
	 * @return   string            Custom content or original content.
	 */
	public function interceptRender( $content, $widget ) {
		// Only process packages widget
		if ( 'packages' !== $widget->get_name() ) {
			return $content;
		}

		// Check global setting
		$use_custom_layout = get_option( 'lovetravel_child_enable_custom_packages_layout', 0 );

		// Only intercept if custom layout is enabled globally
		if ( ! $use_custom_layout ) {
			return $content;
		}

		// Render our custom layout instead
		$settings = $widget->get_settings_for_display();
		ob_start();
		$this->renderCustomLayout( $widget, $settings );
		return ob_get_clean();
	}

	/**
	 * Render custom Layout 2 template.
	 *
	 * @since    2.0.0
	 * @param    object $widget     Widget instance.
	 * @param    array  $settings   Widget settings.
	 */
	private function renderCustomLayout( $widget, $settings ) {
		// Enqueue required scripts
		wp_enqueue_script( 'masonry' );
		wp_enqueue_script( 'jquery-ui-dialog' );

		// Get widget settings
		$nd_travel_postgrid_order   = $settings['packages_order'];
		$nd_travel_postgrid_orderby = $settings['packages_orderby'];
		$packages_qnt               = $settings['packages_qnt'];
		$packages_width             = $settings['packages_width'];
		$packages_layout            = $settings['packages_layout'];
		$packages_id                = isset( $settings['packages_id'] ) ? $settings['packages_id'] : '';
		$nd_travel_destination_id   = isset( $settings['destination_id'] ) ? $settings['destination_id'] : '';
		$nd_travel_typology_slug    = isset( $settings['typology_slug'] ) ? $settings['typology_slug'] : '';
		$packagesgrid_image_size    = $settings['thumbnail_size'];

		// Default values
		if ( '' === $packages_width ) {
			$packages_width = 'nd_travel_width_100_percentage';
		}
		if ( '' === $packages_qnt ) {
			$packages_qnt = -1;
		}
		if ( '' === $nd_travel_postgrid_order ) {
			$nd_travel_postgrid_order = 'DESC';
		}
		if ( '' === $nd_travel_postgrid_orderby ) {
			$nd_travel_postgrid_orderby = 'date';
		}
		if ( '' === $packagesgrid_image_size ) {
			$packagesgrid_image_size = 'large';
		}

		// Build query args (same logic as plugin)
		if ( '' !== $nd_travel_destination_id ) {
			// Destination-based query
			$nd_travel_archive_form_destinations_array    = array();
			$nd_travel_archive_form_destinations_array[0] = $nd_travel_destination_id;

			if ( 1 === $nd_travel_archive_form_destinations_array[0] ) {
				$nd_travel_destination_id                     = get_the_ID();
				$nd_travel_archive_form_destinations_array[0] = get_the_ID();

				if ( function_exists( 'nd_travel_get_destinations_with_parent' ) && 0 !== count( nd_travel_get_destinations_with_parent( $nd_travel_destination_id ) ) ) {
					$nd_travel_destinations_query_i        = 1;
					$nd_travel_children_destinations_array = nd_travel_get_destinations_with_parent( $nd_travel_destination_id );

					foreach ( $nd_travel_children_destinations_array as $nd_travel_children_destination_id ) {
						$nd_travel_archive_form_destinations_array[ $nd_travel_destinations_query_i ] = $nd_travel_children_destination_id;
						++$nd_travel_destinations_query_i;
					}
				}
			} else {
				if ( function_exists( 'nd_travel_get_destinations_with_parent' ) && 0 !== count( nd_travel_get_destinations_with_parent( $nd_travel_destination_id ) ) ) {
					$nd_travel_destinations_query_i        = 1;
					$nd_travel_children_destinations_array = nd_travel_get_destinations_with_parent( $nd_travel_destination_id );

					foreach ( $nd_travel_children_destinations_array as $nd_travel_children_destination_id ) {
						$nd_travel_archive_form_destinations_array[ $nd_travel_destinations_query_i ] = $nd_travel_children_destination_id;
						++$nd_travel_destinations_query_i;
					}
				}
			}

			$args = array(
				'post_type'      => 'nd_travel_cpt_1',
				'posts_per_page' => $packages_qnt,
				'order'          => $nd_travel_postgrid_order,
				'orderby'        => $nd_travel_postgrid_orderby,
				'p'              => $packages_id,
				'meta_query'     => array(
					array(
						'key'   => 'nd_travel_meta_box_destinations',
						'type'  => 'numeric',
						'value' => $nd_travel_archive_form_destinations_array,
					),
				),
			);
		} elseif ( '' !== $nd_travel_typology_slug ) {
			// Typology-based query
			if ( 1 === $nd_travel_typology_slug ) {
				$nd_travel_get_current_typology_id   = get_the_ID();
				$nd_travel_get_current_typology_slug = get_post_field( 'post_name', $nd_travel_get_current_typology_id );
			} else {
				$nd_travel_get_current_typology_slug = $nd_travel_typology_slug;
			}

			$args = array(
				'post_type'      => 'nd_travel_cpt_1',
				'posts_per_page' => $packages_qnt,
				'order'          => $nd_travel_postgrid_order,
				'orderby'        => $nd_travel_postgrid_orderby,
				'p'              => $packages_id,
				'meta_query'     => array(
					array(
						'key'     => 'nd_travel_meta_box_typologies',
						'value'   => $nd_travel_get_current_typology_slug,
						'compare' => 'LIKE',
					),
				),
			);
		} else {
			// Default query
			$args = array(
				'post_type'      => 'nd_travel_cpt_1',
				'posts_per_page' => $packages_qnt,
				'order'          => $nd_travel_postgrid_order,
				'orderby'        => $nd_travel_postgrid_orderby,
				'p'              => $packages_id,
			);
		}

		$the_query = new WP_Query( $args );

		// Start output
		echo '<div class="nd_travel_section nd_travel_masonry_content">';

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

			$nd_travel_meta_box_tab_gallery_content    = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_gallery_content', true );
			$nd_travel_meta_box_tab_map_content        = get_post_meta( get_the_ID(), 'nd_travel_meta_box_tab_map_content', true );
			$nd_travel_meta_box_featured_image_replace = get_post_meta( get_the_ID(), 'nd_travel_meta_box_featured_image_replace', true );

			// Load custom layout template
			$nd_travel_layout_path = get_stylesheet_directory() . '/elementor-widgets/packages/layouts/layout-1.php';
			if ( file_exists( $nd_travel_layout_path ) ) {
				include $nd_travel_layout_path;
			}
		}

		echo '</div>';

		wp_reset_postdata();
	}
}
