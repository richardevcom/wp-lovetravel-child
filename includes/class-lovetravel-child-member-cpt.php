<?php
/**
 * Member Custom Post Type
 *
 * Registers "Member" custom post type with admin label "Team".
 * Used for team members displayed in widgets and templates.
 *
 * @link       https://github.com/richardevcom
 * @since      2.2.0
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Member Custom Post Type class.
 *
 * Handles registration of Member CPT and related meta fields.
 * Members have: title, occupation, content, featured image, social networks.
 *
 * @since      2.2.0
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @author     richardevcom <hello@richardev.com>
 */
class LoveTravelChild_Member_CPT {

	/**
	 * The ID of this theme.
	 *
	 * @since    2.2.0
	 * @access   private
	 * @var      string    $theme_name    The ID of this theme.
	 */
	private $theme_name;

	/**
	 * The version of this theme.
	 *
	 * @since    2.2.0
	 * @access   private
	 * @var      string    $version    The current version of this theme.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.2.0
	 * @param    string    $theme_name    The name of this theme.
	 * @param    string    $version       The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {
		$this->theme_name = $theme_name;
		$this->version    = $version;
	}

	/**
	 * Register the Member custom post type.
	 *
	 * @since    2.2.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Team', 'Post type general name', 'lovetravel-child' ),
			'singular_name'         => _x( 'Member', 'Post type singular name', 'lovetravel-child' ),
			'menu_name'             => _x( 'Team', 'Admin Menu text', 'lovetravel-child' ),
			'name_admin_bar'        => _x( 'Member', 'Add New on Toolbar', 'lovetravel-child' ),
			'add_new'               => __( 'Add New', 'lovetravel-child' ),
			'add_new_item'          => __( 'Add New Member', 'lovetravel-child' ),
			'new_item'              => __( 'New Member', 'lovetravel-child' ),
			'edit_item'             => __( 'Edit Member', 'lovetravel-child' ),
			'view_item'             => __( 'View Member', 'lovetravel-child' ),
			'all_items'             => __( 'All Members', 'lovetravel-child' ),
			'search_items'          => __( 'Search Members', 'lovetravel-child' ),
			'parent_item_colon'     => __( 'Parent Members:', 'lovetravel-child' ),
			'not_found'             => __( 'No members found.', 'lovetravel-child' ),
			'not_found_in_trash'    => __( 'No members found in Trash.', 'lovetravel-child' ),
			'featured_image'        => _x( 'Member Avatar', 'Overrides the "Featured Image" phrase', 'lovetravel-child' ),
			'set_featured_image'    => _x( 'Set member avatar', 'Overrides the "Set featured image" phrase', 'lovetravel-child' ),
			'remove_featured_image' => _x( 'Remove member avatar', 'Overrides the "Remove featured image" phrase', 'lovetravel-child' ),
			'use_featured_image'    => _x( 'Use as member avatar', 'Overrides the "Use as featured image" phrase', 'lovetravel-child' ),
			'archives'              => _x( 'Member archives', 'The post type archive label', 'lovetravel-child' ),
			'insert_into_item'      => _x( 'Insert into member', 'Overrides the "Insert into post" phrase', 'lovetravel-child' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this member', 'Overrides the "Uploaded to this post" phrase', 'lovetravel-child' ),
			'filter_items_list'     => _x( 'Filter members list', 'Screen reader text for the filter links', 'lovetravel-child' ),
			'items_list_navigation' => _x( 'Members list navigation', 'Screen reader text for the pagination', 'lovetravel-child' ),
			'items_list'            => _x( 'Members list', 'Screen reader text for the items list', 'lovetravel-child' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-groups',
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			'show_in_rest'       => true, // Enable REST API access, Gutenberg disabled via filter
		);

		register_post_type( 'lovetravel_member', $args );
	}

	/**
	 * Register meta boxes for Member fields.
	 *
	 * @since    2.2.0
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'member_details',
			__( 'Member Details', 'lovetravel-child' ),
			array( $this, 'render_member_details_meta_box' ),
			'lovetravel_member',
			'normal',
			'high'
		);

		add_meta_box(
			'member_social_networks',
			__( 'Social Networks', 'lovetravel-child' ),
			array( $this, 'render_social_networks_meta_box' ),
			'lovetravel_member',
			'side',
			'default'
		);
	}

	/**
	 * Render the Member Details meta box.
	 *
	 * @since    2.2.0
	 * @param    WP_Post    $post    The post object.
	 */
	public function render_member_details_meta_box( $post ) {
		// Add nonce for security
		wp_nonce_field( 'member_details_nonce', 'member_details_nonce_field' );

		// Get current value
		$occupation = get_post_meta( $post->ID, '_member_occupation', true );

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="member_occupation"><?php _e( 'Occupation', 'lovetravel-child' ); ?></label>
				</th>
				<td>
					<input type="text" 
						   id="member_occupation" 
						   name="member_occupation" 
						   value="<?php echo esc_attr( $occupation ); ?>" 
						   class="regular-text" />
					<p class="description"><?php _e( 'Job title or role of the team member.', 'lovetravel-child' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render the Social Networks meta box.
	 *
	 * @since    2.2.0
	 * @param    WP_Post    $post    The post object.
	 */
	public function render_social_networks_meta_box( $post ) {
		// Add nonce for security
		wp_nonce_field( 'member_social_nonce', 'member_social_nonce_field' );

		// Get current values
		$social_networks = get_post_meta( $post->ID, '_member_social_networks', true );
		if ( ! is_array( $social_networks ) ) {
			$social_networks = array();
		}

		// Available social network types
		$social_types = array(
			'facebook'   => __( 'Facebook', 'lovetravel-child' ),
			'twitter'    => __( 'Twitter', 'lovetravel-child' ),
			'instagram'  => __( 'Instagram', 'lovetravel-child' ),
			'linkedin'   => __( 'LinkedIn', 'lovetravel-child' ),
			'youtube'    => __( 'YouTube', 'lovetravel-child' ),
			'tiktok'     => __( 'TikTok', 'lovetravel-child' ),
			'pinterest'  => __( 'Pinterest', 'lovetravel-child' ),
			'snapchat'   => __( 'Snapchat', 'lovetravel-child' ),
			'whatsapp'   => __( 'WhatsApp', 'lovetravel-child' ),
			'telegram'   => __( 'Telegram', 'lovetravel-child' ),
			'website'    => __( 'Website', 'lovetravel-child' ),
			'email'      => __( 'Email', 'lovetravel-child' ),
		);

		?>
		<div id="member-social-networks">
			<div id="social-networks-container">
				<?php
				if ( ! empty( $social_networks ) ) {
					foreach ( $social_networks as $index => $network ) {
						$this->render_social_network_row( $index, $network, $social_types );
					}
				}
				?>
			</div>
			
			<p>
				<button type="button" id="add-social-network" class="button button-secondary">
					<?php _e( 'Add Social Network', 'lovetravel-child' ); ?>
				</button>
			</p>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			var socialIndex = <?php echo count( $social_networks ); ?>;
			var socialTypes = <?php echo json_encode( $social_types ); ?>;

			// Add new social network row
			$('#add-social-network').on('click', function() {
				var html = '<div class="social-network-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 3px;">';
				html += '<select name="member_social_networks[' + socialIndex + '][type]" style="width: 100%; margin-bottom: 5px;">';
				html += '<option value=""><?php _e( 'Select Type', 'lovetravel-child' ); ?></option>';
				
				for (var key in socialTypes) {
					html += '<option value="' + key + '">' + socialTypes[key] + '</option>';
				}
				
				html += '</select>';
				html += '<input type="url" name="member_social_networks[' + socialIndex + '][url]" placeholder="<?php _e( 'Enter URL', 'lovetravel-child' ); ?>" style="width: 100%; margin-bottom: 5px;" />';
				html += '<button type="button" class="button button-small remove-social-network" style="width: 100%;"><?php _e( 'Remove', 'lovetravel-child' ); ?></button>';
				html += '</div>';

				$('#social-networks-container').append(html);
				socialIndex++;
			});

			// Remove social network row
			$(document).on('click', '.remove-social-network', function() {
				$(this).closest('.social-network-row').remove();
			});
		});
		</script>
		<?php
	}

	/**
	 * Render a single social network row.
	 *
	 * @since    2.2.0
	 * @param    int      $index         The row index.
	 * @param    array    $network       The network data.
	 * @param    array    $social_types  Available social types.
	 */
	private function render_social_network_row( $index, $network, $social_types ) {
		$type = isset( $network['type'] ) ? $network['type'] : '';
		$url  = isset( $network['url'] ) ? $network['url'] : '';
		?>
		<div class="social-network-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 3px;">
			<select name="member_social_networks[<?php echo $index; ?>][type]" style="width: 100%; margin-bottom: 5px;">
				<option value=""><?php _e( 'Select Type', 'lovetravel-child' ); ?></option>
				<?php foreach ( $social_types as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<input type="url" 
				   name="member_social_networks[<?php echo $index; ?>][url]" 
				   value="<?php echo esc_attr( $url ); ?>" 
				   placeholder="<?php _e( 'Enter URL', 'lovetravel-child' ); ?>" 
				   style="width: 100%; margin-bottom: 5px;" />
			<button type="button" class="button button-small remove-social-network" style="width: 100%;">
				<?php _e( 'Remove', 'lovetravel-child' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Save Member meta fields.
	 *
	 * @since    2.2.0
	 * @param    int    $post_id    The post ID.
	 */
	public function save_member_meta( $post_id ) {
		// Verify nonces
		if ( ! isset( $_POST['member_details_nonce_field'] ) || 
			 ! wp_verify_nonce( $_POST['member_details_nonce_field'], 'member_details_nonce' ) ) {
			return;
		}

		if ( ! isset( $_POST['member_social_nonce_field'] ) || 
			 ! wp_verify_nonce( $_POST['member_social_nonce_field'], 'member_social_nonce' ) ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Avoid auto-save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Save occupation
		if ( isset( $_POST['member_occupation'] ) ) {
			update_post_meta( $post_id, '_member_occupation', sanitize_text_field( $_POST['member_occupation'] ) );
		}

		// Save social networks
		if ( isset( $_POST['member_social_networks'] ) && is_array( $_POST['member_social_networks'] ) ) {
			$social_networks = array();
			
			foreach ( $_POST['member_social_networks'] as $network ) {
				if ( ! empty( $network['type'] ) && ! empty( $network['url'] ) ) {
					$social_networks[] = array(
						'type' => sanitize_text_field( $network['type'] ),
						'url'  => esc_url_raw( $network['url'] ),
					);
				}
			}
			
			update_post_meta( $post_id, '_member_social_networks', $social_networks );
		} else {
			delete_post_meta( $post_id, '_member_social_networks' );
		}
	}

	/**
	 * Customize admin columns for Members.
	 *
	 * @since    2.2.0
	 * @param    array    $columns    Default columns.
	 * @return   array               Modified columns.
	 */
	public function customize_admin_columns( $columns ) {
		// Remove date, add our custom columns
		unset( $columns['date'] );
		
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['title'] = $columns['title'];
		$new_columns['member_avatar'] = __( 'Avatar', 'lovetravel-child' );
		$new_columns['member_occupation'] = __( 'Occupation', 'lovetravel-child' );
		$new_columns['member_social_count'] = __( 'Social Networks', 'lovetravel-child' );
		$new_columns['date'] = __( 'Date', 'lovetravel-child' );

		return $new_columns;
	}

	/**
	 * Display custom column content.
	 *
	 * @since    2.2.0
	 * @param    string    $column     Column name.
	 * @param    int       $post_id    Post ID.
	 */
	public function display_admin_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'member_avatar':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 50, 50 ), array( 'style' => 'border-radius: 50%;' ) );
				} else {
					echo '<span style="color: #999;">' . __( 'No avatar', 'lovetravel-child' ) . '</span>';
				}
				break;

			case 'member_occupation':
				$occupation = get_post_meta( $post_id, '_member_occupation', true );
				echo $occupation ? esc_html( $occupation ) : '<span style="color: #999;">' . __( 'No occupation', 'lovetravel-child' ) . '</span>';
				break;

			case 'member_social_count':
				$social_networks = get_post_meta( $post_id, '_member_social_networks', true );
				$count = is_array( $social_networks ) ? count( $social_networks ) : 0;
				echo sprintf( _n( '%d network', '%d networks', $count, 'lovetravel-child' ), $count );
				break;
		}
	}

	/**
	 * Register post meta fields with REST API support.
	 *
	 * Enables access to member data via REST API for potential future use.
	 *
	 * @since    2.2.0
	 */
	public function register_meta_fields() {
		register_meta( 'post', '_member_occupation', array(
			'object_subtype' => 'lovetravel_member',
			'type'           => 'string',
			'single'         => true,
			'show_in_rest'   => true,
			'auth_callback'  => function() {
				return current_user_can( 'edit_posts' );
			}
		) );

		register_meta( 'post', '_member_social_networks', array(
			'object_subtype' => 'lovetravel_member',
			'type'           => 'array',
			'single'         => true,
			'show_in_rest'   => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'type' => array( 'type' => 'string' ),
							'url'  => array( 'type' => 'string' ),
						),
					),
				),
			),
			'auth_callback'  => function() {
				return current_user_can( 'edit_posts' );
			}
		) );
	}

	/**
	 * Disable Gutenberg editor for Member posts.
	 *
	 * Forces classic editor while maintaining REST API access.
	 *
	 * @since    2.2.0
	 * @param    bool    $can_edit    Whether the post type can be edited in Gutenberg.
	 * @param    string  $post_type   Post type name.
	 * @return   bool                 Modified edit capability.
	 */
	public function disable_gutenberg_editor( $can_edit, $post_type ) {
		if ( $post_type === 'lovetravel_member' ) {
			return false;
		}
		return $can_edit;
	}

}