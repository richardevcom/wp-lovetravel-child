<?php
/**
 * Admin Functionality
 *
 * Admin-specific hooks, asset enqueuing, and settings pages.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/admin
 * @since      2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Class
 *
 * Manages admin area functionality including asset loading and settings pages.
 */
class LoveTravelChildAdmin {

	/**
	 * Theme identifier.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $themeName Theme slug.
	 */
	private $themeName;

	/**
	 * Theme version.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $version Theme version.
	 */
	private $version;

	/**
	 * Initialize the class.
	 *
	 * @since 2.0.0
	 * @param string $themeName Theme identifier.
	 * @param string $version   Theme version.
	 */
	public function __construct( $themeName, $version ) {
		$this->themeName = $themeName;
		$this->version   = $version;
	}

	/**
	 * Enqueue admin styles.
	 *
	 * Priority 20 ensures child theme styles override parent theme and plugins.
	 *
	 * @since 2.0.0
	 */
	public function enqueueStyles() {
		wp_enqueue_style(
			$this->themeName . '-admin',
			get_stylesheet_directory_uri() . '/admin/assets/css/admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * Priority 20 ensures child theme scripts override parent theme and plugins.
	 *
	 * @since 2.0.0
	 */
	public function enqueueScripts() {
		wp_enqueue_script(
			$this->themeName . '-admin',
			get_stylesheet_directory_uri() . '/admin/assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Localize script for AJAX
		wp_localize_script(
			$this->themeName . '-admin',
			'lovetravelChildAdmin',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'lovetravel_child_admin' ),
			)
		);
	}

	/**
	 * Add admin menu page under Appearance.
	 *
	 * @since 2.0.0
	 */
	public function addAdminMenu() {
		add_theme_page(
			__( 'LoveTravel Child Settings', 'lovetravel-child' ),
			__( 'Child Theme', 'lovetravel-child' ),
			'manage_options',
			'lovetravel-child-settings',
			array( $this, 'displaySettingsPage' )
		);
	}

	/**
	 * Display settings page.
	 *
	 * @since 2.0.0
	 */
	public function displaySettingsPage() {
		include_once LOVETRAVEL_CHILD_PATH . '/admin/partials/settings-page.php';
	}





	/**
	 * Handle AJAX request to import templates manually.
	 *
	 * @since 2.0.0
	 */
	public function ajaxImportTemplates() {
		check_ajax_referer( 'lovetravel_child_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'lovetravel-child' ) ) );
		}

		// Initialize importer
		$adminNotices     = new LoveTravelChildAdminNotices();
		$templateImporter = new LoveTravelChildElementorTemplateImporter( $adminNotices );

		// Check dependencies
		$deps = $templateImporter->checkDependencies();
		if ( ! $deps['status'] ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %s: comma-separated list of missing dependencies */
						__( 'Cannot import templates. Missing: %s', 'lovetravel-child' ),
						implode( ', ', $deps['missing'] )
					),
				)
			);
		}

		// Get all templates
		$templates = $templateImporter->scanTemplates();
		$total     = 0;
		$imported  = 0;
		$skipped   = 0;
		$errors    = array();

		foreach ( $templates as $type => $files ) {
			foreach ( $files as $filepath ) {
				$total++;
				$result = $templateImporter->importTemplate( $filepath );

				if ( $result['success'] ) {
					if ( isset( $result['skip'] ) ) {
						$skipped++;
					} else {
						$imported++;
					}
				} else {
					$errors[] = basename( $filepath ) . ': ' . $result['message'];
				}
			}
		}

		// Build response message
		$messages = array();

		if ( $imported > 0 ) {
			$messages[] = sprintf(
				/* translators: %d: number of templates imported */
				_n(
					'Successfully imported %d template.',
					'Successfully imported %d templates.',
					$imported,
					'lovetravel-child'
				),
				$imported
			);
		}

		if ( $skipped > 0 ) {
			$messages[] = sprintf(
				/* translators: %d: number of templates skipped */
				_n(
					'%d template was already imported.',
					'%d templates were already imported.',
					$skipped,
					'lovetravel-child'
				),
				$skipped
			);
		}

		if ( ! empty( $errors ) ) {
			$messages[] = __( 'Errors:', 'lovetravel-child' ) . ' ' . implode( ', ', $errors );
		}

		if ( empty( $messages ) ) {
			$messages[] = __( 'No templates found to import.', 'lovetravel-child' );
		}

		wp_send_json_success(
			array(
				'message'  => implode( ' ', $messages ),
				'imported' => $imported,
				'skipped'  => $skipped,
				'errors'   => count( $errors ),
			)
		);
	}
}
