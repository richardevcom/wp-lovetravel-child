<?php
/**
 * Typology Card Metabox Extension
 *
 * Adds "Card Settings" tab to nd_travel_cpt_2 (Typologies) metabox with custom fields
 * for card icon and background image used in Elementor widgets.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/metaboxes
 * @since      2.0.0
 */

/**
 * Typology Card Metabox class.
 *
 * Extends the plugin's typology metabox with card-specific fields by injecting
 * a new tab into the existing jQuery UI tabs interface.
 *
 * @since 2.0.0
 */
class LoveTravelChildTypologyCardMetabox {

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
	 * Inject Card Settings tab into plugin metabox.
	 *
	 * Hooks: admin_footer-post.php, admin_footer-post-new.php
	 *
	 * @since 2.0.0
	 */
	public function injectCardSettingsTab() {
		global $post;
		
		// Only run on typology edit screens
		if ( ! $post || 'nd_travel_cpt_2' !== $post->post_type ) {
			return;
		}

		// Get current values
		$card_icon     = get_post_meta( $post->ID, 'lovetravel_child_meta_box_card_icon', true );
		$card_bg_image = get_post_meta( $post->ID, 'lovetravel_child_meta_box_card_bg_image', true );

		// Enqueue media uploader
		wp_enqueue_media();
		?>

		<script>
		jQuery(document).ready(function($) {
			// Inject Card Settings tab into existing metabox
			$('#nd_travel_id_metabox_cpt > ul').append(
				'<li><a href="#nd_travel_tab_card"><span class="dashicons-before dashicons-admin-appearance nd_travel_line_height_20 nd_travel_margin_right_10 nd_travel_color_444444"></span><?php esc_html_e( 'Card Settings', 'lovetravel-child' ); ?></a></li>'
			);
			
			$('#nd_travel_id_metabox_cpt .nd_travel_id_metabox_cpt_content').append(`
				<div id="nd_travel_tab_card">
					
					<!-- Card Icon -->
					<div class="nd_travel_section nd_travel_border_bottom_1_solid_eee nd_travel_padding_10 nd_travel_box_sizing_border_box">
						<p><strong><?php esc_html_e( 'Card Icon', 'lovetravel-child' ); ?></strong></p>
						<p><input class="nd_travel_width_100_percentage" type="text" name="lovetravel_child_meta_box_card_icon" id="lovetravel_child_meta_box_card_icon" value="<?php echo esc_attr( $card_icon ); ?>" /></p>
						<p>
							<input class="button lovetravel-child-upload-card-icon-btn" type="button" value="<?php esc_attr_e( 'Upload', 'lovetravel-child' ); ?>" />
						</p>
						<p><?php esc_html_e( 'Icon displayed on the left side of the card (recommended: square image, transparent PNG).', 'lovetravel-child' ); ?></p>
					</div>

					<!-- Card Background Image -->
					<div class="nd_travel_section nd_travel_padding_10 nd_travel_box_sizing_border_box">
						<p><strong><?php esc_html_e( 'Card Background Image', 'lovetravel-child' ); ?></strong></p>
						<p><input class="nd_travel_width_100_percentage" type="text" name="lovetravel_child_meta_box_card_bg_image" id="lovetravel_child_meta_box_card_bg_image" value="<?php echo esc_attr( $card_bg_image ); ?>" /></p>
						<p>
							<input class="button lovetravel-child-upload-card-bg-btn" type="button" value="<?php esc_attr_e( 'Upload', 'lovetravel-child' ); ?>" />
						</p>
						<p><?php esc_html_e( 'Background image for the card (will be covered by overlay color if background color is set in widget).', 'lovetravel-child' ); ?></p>
					</div>

				</div>
			`);
			
			// Refresh tabs to include new tab
			$('#nd_travel_id_metabox_cpt').tabs('refresh');

			// Media uploader for card icon
			$('.lovetravel-child-upload-card-icon-btn').on('click', function(e) {
				e.preventDefault();
				
				var frame = wp.media({
					title: '<?php echo esc_js( __( 'Select Card Icon', 'lovetravel-child' ) ); ?>',
					button: {
						text: '<?php echo esc_js( __( 'Use this icon', 'lovetravel-child' ) ); ?>'
					},
					multiple: false
				});
				
				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					$('#lovetravel_child_meta_box_card_icon').val(attachment.url);
				});
				
				frame.open();
			});

			// Media uploader for card background
			$('.lovetravel-child-upload-card-bg-btn').on('click', function(e) {
				e.preventDefault();
				
				var frame = wp.media({
					title: '<?php echo esc_js( __( 'Select Card Background', 'lovetravel-child' ) ); ?>',
					button: {
						text: '<?php echo esc_js( __( 'Use this image', 'lovetravel-child' ) ); ?>'
					},
					multiple: false
				});
				
				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					$('#lovetravel_child_meta_box_card_bg_image').val(attachment.url);
				});
				
				frame.open();
			});
		});
		</script>
		<?php
	}

	/**
	 * Save card settings meta.
	 *
	 * Hooks: save_post
	 *
	 * @since 2.0.0
	 * @param int $post_id Post ID.
	 */
	public function saveMetaBox( $post_id ) {
		// Check post type
		if ( ! isset( $_POST['post_type'] ) || 'nd_travel_cpt_2' !== $_POST['post_type'] ) {
			return;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save card icon
		if ( isset( $_POST['lovetravel_child_meta_box_card_icon'] ) ) {
			$card_icon = esc_url_raw( $_POST['lovetravel_child_meta_box_card_icon'] );
			if ( ! empty( $card_icon ) ) {
				update_post_meta( $post_id, 'lovetravel_child_meta_box_card_icon', $card_icon );
			} else {
				delete_post_meta( $post_id, 'lovetravel_child_meta_box_card_icon' );
			}
		}

		// Save card background image
		if ( isset( $_POST['lovetravel_child_meta_box_card_bg_image'] ) ) {
			$card_bg_image = esc_url_raw( $_POST['lovetravel_child_meta_box_card_bg_image'] );
			if ( ! empty( $card_bg_image ) ) {
				update_post_meta( $post_id, 'lovetravel_child_meta_box_card_bg_image', $card_bg_image );
			} else {
				delete_post_meta( $post_id, 'lovetravel_child_meta_box_card_bg_image' );
			}
		}
	}

}
