<?php
/**
 * Elementor Template Importer
 *
 * Manages automatic import of Elementor templates stored in child theme.
 * Validates dependencies, tracks import status, and integrates with admin notices.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

/**
 * Elementor Template Importer class.
 *
 * Handles scanning, importing, and tracking of Elementor template JSON files
 * from the child theme's /elementor-templates/ directory.
 *
 * @since 2.0.0
 */
class LoveTravelChildElementorTemplateImporter {

	/**
	 * Base directory for templates.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $templateDir Absolute path to template directory.
	 */
	private $templateDir;

	/**
	 * Option key for tracking imported templates.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $importedOption Option name in wp_options.
	 */
	private $importedOption = 'lovetravel_child_imported_templates';

	/**
	 * Admin notices instance.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    LoveTravelChildAdminNotices $notices Admin notices manager.
	 */
	private $notices;

	/**
	 * Initialize the template importer.
	 *
	 * @since 2.0.0
	 * @param LoveTravelChildAdminNotices $notices Admin notices instance.
	 */
	public function __construct( $notices ) {
		$this->templateDir = get_stylesheet_directory() . '/elementor-templates';
		$this->notices     = $notices;
	}

	/**
	 * Check if all dependencies are met.
	 *
	 * @since  2.0.0
	 * @return array Array with 'status' (bool) and 'missing' (array of strings).
	 */
	public function checkDependencies() {
		$missing = array();

		// Check parent theme
		$theme = wp_get_theme();
		if ( ! $theme->parent() || 'lovetravel' !== $theme->parent()->get( 'TextDomain' ) ) {
			$missing[] = __( 'LoveTravel parent theme', 'lovetravel-child' );
		}

		// Check Elementor
		if ( ! did_action( 'elementor/loaded' ) ) {
			$missing[] = __( 'Elementor plugin', 'lovetravel-child' );
		}

		// Check Elementor Pro
		if ( ! function_exists( 'elementor_pro_load_plugin' ) ) {
			$missing[] = __( 'Elementor Pro plugin', 'lovetravel-child' );
		}

		// Check nd-travel plugin (via its main function or CPT registration)
		if ( ! function_exists( 'nd_travel_scripts' ) && ! post_type_exists( 'nd_travel_cpt_1' ) ) {
			$missing[] = __( 'ND Travel plugin', 'lovetravel-child' );
		}

		return array(
			'status'  => empty( $missing ),
			'missing' => $missing,
		);
	}

	/**
	 * Scan template directories for JSON files.
	 *
	 * @since  2.0.0
	 * @return array Array of template file paths grouped by type.
	 */
	public function scanTemplates() {
		$templates = array(
			'sections' => array(),
			'pages'    => array(),
			'widgets'  => array(),
		);

		foreach ( array( 'sections', 'pages', 'widgets' ) as $type ) {
			$dir = $this->templateDir . '/' . $type;
			if ( ! is_dir( $dir ) ) {
				continue;
			}

			$files = glob( $dir . '/*.json' );
			if ( ! empty( $files ) ) {
				$templates[ $type ] = $files;
			}
		}

		return $templates;
	}

	/**
	 * Import a single template file.
	 *
	 * @since  2.0.0
	 * @param  string $filepath Absolute path to template JSON file.
	 * @return array            Import result with status and message.
	 */
	public function importTemplate( $filepath ) {
		if ( ! file_exists( $filepath ) ) {
			return array(
				'success' => false,
				'message' => __( 'Template file not found.', 'lovetravel-child' ),
			);
		}

		// Read and decode JSON
		$json_data = file_get_contents( $filepath );
		$data      = json_decode( $json_data, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: JSON error message */
					__( 'Invalid JSON: %s', 'lovetravel-child' ),
					json_last_error_msg()
				),
			);
		}

		// Extract template name from filename
		$template_name = basename( $filepath, '.json' );
		
		// Check if already imported
		if ( $this->isTemplateImported( $template_name ) ) {
			return array(
				'success' => true,
				'message' => __( 'Template already imported.', 'lovetravel-child' ),
				'skip'    => true,
			);
		}

		// Create template post
		$template_id = $this->createTemplatePost( $template_name, $data );

		if ( is_wp_error( $template_id ) ) {
			return array(
				'success' => false,
				'message' => $template_id->get_error_message(),
			);
		}

		// Track successful import
		$this->markTemplateImported( $template_name, $template_id );

		return array(
			'success'     => true,
			'message'     => __( 'Template imported successfully.', 'lovetravel-child' ),
			'template_id' => $template_id,
		);
	}

	/**
	 * Create Elementor template post.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  string $name Template name.
	 * @param  array  $data Template data.
	 * @return int|WP_Error Template post ID or error.
	 */
	private function createTemplatePost( $name, $data ) {
		// Prepare post data
		$post_data = array(
			'post_title'  => ucwords( str_replace( array( '-', '_' ), ' ', $name ) ),
			'post_type'   => 'elementor_library',
			'post_status' => 'publish',
		);

		// Insert post
		$template_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		// Determine template type from data or filename
		$template_type = $this->getTemplateType( $name, $data );

		// Save template metadata
		update_post_meta( $template_id, '_elementor_template_type', $template_type );
		update_post_meta( $template_id, '_elementor_edit_mode', 'builder' );
		
		// Save template content
		if ( isset( $data['content'] ) ) {
			update_post_meta( $template_id, '_elementor_data', wp_slash( wp_json_encode( $data['content'] ) ) );
		} elseif ( isset( $data['elements'] ) ) {
			update_post_meta( $template_id, '_elementor_data', wp_slash( wp_json_encode( $data['elements'] ) ) );
		}

		// Save page settings if present
		if ( isset( $data['page_settings'] ) ) {
			update_post_meta( $template_id, '_elementor_page_settings', $data['page_settings'] );
		}

		return $template_id;
	}

	/**
	 * Determine template type from data or directory.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  string $name Template name/path.
	 * @param  array  $data Template data.
	 * @return string       Template type (section, page, etc.).
	 */
	private function getTemplateType( $name, $data ) {
		// Check if type specified in data
		if ( isset( $data['type'] ) && 'section' === $data['type'] ) {
			return 'section';
		}

		// Determine from directory
		if ( strpos( $name, 'sections/' ) !== false ) {
			return 'section';
		}

		if ( strpos( $name, 'pages/' ) !== false ) {
			return 'page';
		}

		// Default to section
		return 'section';
	}

	/**
	 * Check if template has been imported.
	 *
	 * @since  2.0.0
	 * @param  string $template_name Template name.
	 * @return bool                  Whether template is imported.
	 */
	public function isTemplateImported( $template_name ) {
		$imported = get_option( $this->importedOption, array() );
		return isset( $imported[ $template_name ] );
	}

	/**
	 * Mark template as imported.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  string $template_name Template name.
	 * @param  int    $template_id   WordPress post ID.
	 */
	private function markTemplateImported( $template_name, $template_id ) {
		$imported                      = get_option( $this->importedOption, array() );
		$imported[ $template_name ]    = array(
			'template_id' => $template_id,
			'imported_at' => current_time( 'mysql' ),
		);
		update_option( $this->importedOption, $imported );
	}

	/**
	 * Run automatic import on theme activation.
	 *
	 * @since 2.0.0
	 */
	public function autoImport() {
		// Check dependencies
		$deps = $this->checkDependencies();
		if ( ! $deps['status'] ) {
			$this->notices->addNotice(
				'elementor_dependencies',
				sprintf(
					/* translators: %s: comma-separated list of missing dependencies */
					__( '<strong>Elementor Templates:</strong> Cannot import templates. Missing: %s', 'lovetravel-child' ),
					implode( ', ', $deps['missing'] )
				),
				'error',
				true
			);
			return;
		}

		// Get all templates
		$templates = $this->scanTemplates();
		$total     = 0;
		$imported  = 0;
		$skipped   = 0;
		$errors    = array();

		foreach ( $templates as $type => $files ) {
			foreach ( $files as $filepath ) {
				$total++;
				$result = $this->importTemplate( $filepath );

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

		// Add success notice
		if ( $imported > 0 ) {
			$this->notices->addNotice(
				'elementor_import_success',
				sprintf(
					/* translators: %d: number of templates imported */
					_n(
						'<strong>Elementor Templates:</strong> Successfully imported %d template.',
						'<strong>Elementor Templates:</strong> Successfully imported %d templates.',
						$imported,
						'lovetravel-child'
					),
					$imported
				),
				'success',
				true
			);
		}

		// Add error notices
		if ( ! empty( $errors ) ) {
			$this->notices->addNotice(
				'elementor_import_errors',
				sprintf(
					/* translators: %s: error messages */
					__( '<strong>Elementor Templates:</strong> Import errors:<br>%s', 'lovetravel-child' ),
					implode( '<br>', $errors )
				),
				'error',
				true
			);
		}

		// Add info notice if templates exist but were skipped
		if ( $skipped > 0 && $imported === 0 ) {
			$this->notices->addNotice(
				'elementor_already_imported',
				sprintf(
					/* translators: %d: number of templates */
					_n(
						'<strong>Elementor Templates:</strong> %d template already imported.',
						'<strong>Elementor Templates:</strong> %d templates already imported.',
						$skipped,
						'lovetravel-child'
					),
					$skipped
				),
				'info',
				true
			);
		}
	}

	/**
	 * Get import status for all templates.
	 *
	 * @since  2.0.0
	 * @return array Array of templates with import status.
	 */
	public function getImportStatus() {
		$templates = $this->scanTemplates();
		$imported  = get_option( $this->importedOption, array() );
		$status    = array();

		foreach ( $templates as $type => $files ) {
			foreach ( $files as $filepath ) {
				$template_name = basename( $filepath, '.json' );
				$is_imported   = false;
				$import_date   = null;

				// Check if tracked as imported
				if ( isset( $imported[ $template_name ] ) ) {
					$template_id = $imported[ $template_name ]['template_id'];
					
					// Verify the post actually exists in database
					$post_exists = get_post( $template_id );
					
					if ( $post_exists && 'elementor_library' === $post_exists->post_type ) {
						$is_imported = true;
						$import_date = $imported[ $template_name ]['imported_at'];
					} else {
						// Post was deleted, remove from tracking
						unset( $imported[ $template_name ] );
						update_option( $this->importedOption, $imported );
					}
				}

				$status[] = array(
					'name'     => $template_name,
					'type'     => $type,
					'imported' => $is_imported,
					'date'     => $import_date,
				);
			}
		}

		return $status;
	}

	/**
	 * Clear all import tracking data.
	 *
	 * Useful for testing or forcing re-import.
	 *
	 * @since 2.0.0
	 */
	public function clearImportTracking() {
		delete_option( $this->importedOption );
	}
}
