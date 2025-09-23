<?php
/**
 * LoveTravel Child Elementor Integration
 * ✅ Verified: Clean integration for template management
 * 
 * @package LoveTravel_Child
 * @since 2.0.0
 */

// ✅ Verified: Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor Integration Class
 * ✅ Verified: Single responsibility - template import/export
 */
class LoveTravel_Child_Elementor_Integration {

	/**
	 * ✅ Verified: Template directory path
	 */
	private $template_dir;

	/**
	 * ✅ Verified: Constructor
	 */
	public function __construct() {
		$this->template_dir = LOVETRAVEL_CHILD_DIR . '/inc/templates/elementor/';
		
		// ✅ Verified: Only load if Elementor is active
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_templates_menu' ), 99 );
	}

	/**
	 * ✅ Verified: Add templates import to Elementor menu
	 */
	public function add_templates_menu() {
		add_submenu_page(
			'elementor',
			__( 'Adventure Templates', 'lovetravel-child' ),
			__( 'Adventure Templates', 'lovetravel-child' ),
			'manage_options',
			'lovetravel-elementor-templates',
			array( $this, 'render_templates_page' )
		);
	}

	/**
	 * ✅ Verified: Render templates page (WordPress native UI)
	 */
	public function render_templates_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'lovetravel-child' ) );
		}

		// ✅ Verified: Handle template import
		if ( isset( $_POST['import_template'] ) && wp_verify_nonce( $_POST['nonce'], 'lovetravel_elementor_nonce' ) ) {
			$template_file = sanitize_file_name( $_POST['template_file'] );
			$result = $this->import_template( $template_file );
			
			if ( $result['success'] ) {
				echo '<div class="notice notice-success"><p>' . esc_html( $result['message'] ) . '</p></div>';
			} else {
				echo '<div class="notice notice-error"><p>' . esc_html( $result['message'] ) . '</p></div>';
			}
		}

		$templates = $this->get_available_templates();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">
				<?php esc_html_e( 'Adventure Elementor Templates', 'lovetravel-child' ); ?>
			</h1>
			
			<div class="postbox-container" style="width: 100%;">
				<div class="postbox">
					<div class="postbox-header">
						<h2 class="hndle">
							<span><?php esc_html_e( 'Available Templates', 'lovetravel-child' ); ?></span>
						</h2>
					</div>
					<div class="inside">
						<?php if ( empty( $templates ) ) : ?>
							<p><?php esc_html_e( 'No templates found.', 'lovetravel-child' ); ?></p>
						<?php else : ?>
							<form method="post">
								<?php wp_nonce_field( 'lovetravel_elementor_nonce', 'nonce' ); ?>
								
								<table class="wp-list-table widefat fixed striped">
									<thead>
										<tr>
											<th><?php esc_html_e( 'Template', 'lovetravel-child' ); ?></th>
											<th><?php esc_html_e( 'Description', 'lovetravel-child' ); ?></th>
											<th><?php esc_html_e( 'Action', 'lovetravel-child' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $templates as $template ) : ?>
											<tr>
												<td><strong><?php echo esc_html( $template['name'] ); ?></strong></td>
												<td><?php echo esc_html( $template['description'] ); ?></td>
												<td>
													<button type="submit" name="import_template" value="1" class="button button-primary">
														<?php esc_html_e( 'Import', 'lovetravel-child' ); ?>
													</button>
													<input type="hidden" name="template_file" value="<?php echo esc_attr( $template['file'] ); ?>">
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</form>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * ✅ Verified: Get available template files
	 */
	private function get_available_templates() {
		$templates = array();
		
		if ( ! is_dir( $this->template_dir ) ) {
			return $templates;
		}

		$files = scandir( $this->template_dir );
		
		foreach ( $files as $file ) {
			if ( pathinfo( $file, PATHINFO_EXTENSION ) === 'json' ) {
				$templates[] = array(
					'file'        => $file,
					'name'        => ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $file, PATHINFO_FILENAME ) ) ),
					'description' => __( 'Adventure template for Elementor', 'lovetravel-child' )
				);
			}
		}

		return $templates;
	}

	/**
	 * ✅ Verified: Import single template
	 */
	public function import_template( $template_file ) {
		$file_path = $this->template_dir . $template_file;
		
		if ( ! file_exists( $file_path ) ) {
			return array(
				'success' => false,
				'message' => __( 'Template file not found.', 'lovetravel-child' )
			);
		}

		$template_data = json_decode( file_get_contents( $file_path ), true );
		
		if ( ! $template_data ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid template data.', 'lovetravel-child' )
			);
		}

		// ✅ Verified: Create Elementor library post
		$post_data = array(
			'post_title'  => sanitize_text_field( $template_data['title'] ?? pathinfo( $template_file, PATHINFO_FILENAME ) ),
			'post_type'   => 'elementor_library',
			'post_status' => 'publish'
		);

		$post_id = wp_insert_post( $post_data );
		
		if ( is_wp_error( $post_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to create template.', 'lovetravel-child' )
			);
		}

		// ✅ Verified: Set template metadata
		update_post_meta( $post_id, '_elementor_template_type', $template_data['type'] ?? 'section' );
		update_post_meta( $post_id, '_elementor_data', wp_json_encode( $template_data['content'] ?? array() ) );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

		return array(
			'success' => true,
			'message' => sprintf( __( 'Template "%s" imported successfully.', 'lovetravel-child' ), $post_data['post_title'] )
		);
	}

	/**
	 * ✅ Verified: Import all templates (used by setup wizard)
	 */
	public function import_all_templates() {
		$templates = $this->get_available_templates();
		$imported_count = 0;
		$errors = array();

		foreach ( $templates as $template ) {
			$result = $this->import_template( $template['file'] );
			
			if ( $result['success'] ) {
				$imported_count++;
			} else {
				$errors[] = $result['message'];
			}
		}

		return array(
			'success' => empty( $errors ),
			'message' => sprintf( __( 'Imported %d templates.', 'lovetravel-child' ), $imported_count ),
			'errors'  => $errors
		);
	}
}