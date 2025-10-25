<?php
/**
 * Admin Settings Page
 *
 * Template for the child theme settings page under Appearance menu.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/admin/partials
 * @since      2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Initialize template importer for status display
$adminNotices     = new LoveTravelChildAdminNotices();
$templateImporter = new LoveTravelChildElementorTemplateImporter( $adminNotices );
$templateStatus   = $templateImporter->getImportStatus();
$dependencies     = $templateImporter->checkDependencies();
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<?php settings_errors(); ?>

	<!-- Elementor Templates Section -->
	<div class="card">
		<h2><?php esc_html_e( 'Elementor Templates', 'lovetravel-child' ); ?></h2>
		
		<?php if ( ! $dependencies['status'] ) : ?>
			<div class="notice notice-error inline">
				<p>
					<strong><?php esc_html_e( 'Missing Dependencies:', 'lovetravel-child' ); ?></strong>
					<?php echo esc_html( implode( ', ', $dependencies['missing'] ) ); ?>
				</p>
				<p><?php esc_html_e( 'Templates cannot be imported until all dependencies are active.', 'lovetravel-child' ); ?></p>
			</div>
		<?php else : ?>
			<p>
				<?php esc_html_e( 'Manage Elementor templates stored in the child theme. Templates are automatically imported on theme activation.', 'lovetravel-child' ); ?>
			</p>

			<?php if ( ! empty( $templateStatus ) ) : ?>
				<?php
				// Check if any templates need importing
				$needsImport = false;
				foreach ( $templateStatus as $template ) {
					if ( ! $template['imported'] ) {
						$needsImport = true;
						break;
					}
				}
				?>

				<?php if ( $needsImport ) : ?>
					<div class="notice notice-info inline">
						<p>
							<strong><?php esc_html_e( 'Templates detected but not imported.', 'lovetravel-child' ); ?></strong>
							<?php esc_html_e( 'Click "Import Templates Now" below or reactivate the theme to import.', 'lovetravel-child' ); ?>
						</p>
					</div>
				<?php endif; ?>

				<table class="wp-list-table widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Template Name', 'lovetravel-child' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Type', 'lovetravel-child' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Status', 'lovetravel-child' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Imported Date', 'lovetravel-child' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $templateStatus as $template ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $template['name'] ); ?></strong></td>
								<td><?php echo esc_html( ucfirst( $template['type'] ) ); ?></td>
								<td>
									<?php if ( $template['imported'] ) : ?>
										<span class="dashicons dashicons-yes" style="color: #46b450;"></span>
										<?php esc_html_e( 'Imported', 'lovetravel-child' ); ?>
									<?php else : ?>
										<span class="dashicons dashicons-marker" style="color: #dba617;"></span>
										<?php esc_html_e( 'Not Imported', 'lovetravel-child' ); ?>
									<?php endif; ?>
								</td>
								<td>
									<?php
									if ( $template['date'] ) {
										echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $template['date'] ) ) );
									} else {
										echo '—';
									}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<p class="submit">
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ); ?>" class="button">
						<span class="dashicons dashicons-editor-table" style="vertical-align: text-top;"></span>
						<?php esc_html_e( 'View Templates in Elementor', 'lovetravel-child' ); ?>
					</a>
					<button type="button" class="button button-primary" id="lovetravel-child-import-templates">
						<span class="dashicons dashicons-download" style="vertical-align: text-top;"></span>
						<?php esc_html_e( 'Import Templates Now', 'lovetravel-child' ); ?>
					</button>
					<span class="spinner" style="float: none; margin-top: 5px;"></span>
				</p>
				<div id="lovetravel-child-import-message"></div>
			<?php else : ?>
				<p><?php esc_html_e( 'No templates found in /elementor-templates/ directory.', 'lovetravel-child' ); ?></p>
				<p class="description">
					<?php
					printf(
/* translators: %s: path to templates directory */
esc_html__( 'Add template JSON files to %s to get started.', 'lovetravel-child' ),
'<code>/wp-content/themes/lovetravel-child/elementor-templates/sections/</code>'
);
					?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
	</div>



	<!-- Theme Information Section -->
	<div class="card">
		<h2><?php esc_html_e( 'Theme Information', 'lovetravel-child' ); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Child Theme Version', 'lovetravel-child' ); ?></th>
					<td>
						<code><?php echo esc_html( LOVETRAVEL_CHILD_VERSION ); ?></code>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Parent Theme', 'lovetravel-child' ); ?></th>
					<td>
						<?php echo esc_html( wp_get_theme()->parent()->get( 'Name' ) ); ?>
						<span class="description">
							(<?php echo esc_html( wp_get_theme()->parent()->get( 'Version' ) ); ?>)
						</span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Template Directory', 'lovetravel-child' ); ?></th>
					<td>
						<code><?php echo esc_html( str_replace( ABSPATH, '/', get_stylesheet_directory() ) ); ?></code>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
