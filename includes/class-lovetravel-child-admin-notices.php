<?php
/**
 * Admin Notices Manager
 *
 * Centralized system for displaying WordPress admin notices throughout the child theme.
 * Supports dismissible, persistent, and conditional notices.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

/**
 * Admin Notices Manager class.
 *
 * Provides a unified API for displaying admin notices with various types,
 * dismissibility options, and persistence.
 *
 * @since 2.0.0
 */
class LoveTravelChildAdminNotices {

	/**
	 * Array of registered notices.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    array $notices Registered admin notices.
	 */
	private $notices = array();

	/**
	 * Option name for storing dismissed notices.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    string $dismissedOption Option key in wp_options table.
	 */
	private $dismissedOption = 'lovetravel_child_dismissed_notices';

	/**
	 * Initialize the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// Display notices will be hooked via Loader
	}

	/**
	 * Add a notice to the queue.
	 *
	 * @since 2.0.0
	 * @param string $id          Unique notice identifier.
	 * @param string $message     Notice message (HTML allowed).
	 * @param string $type        Notice type: success, error, warning, info.
	 * @param bool   $dismissible Whether notice can be dismissed.
	 * @param array  $conditions  Optional conditions for displaying notice.
	 */
	public function addNotice( $id, $message, $type = 'info', $dismissible = true, $conditions = array() ) {
		$this->notices[ $id ] = array(
			'message'     => $message,
			'type'        => $type,
			'dismissible' => $dismissible,
			'conditions'  => $conditions,
		);
	}

	/**
	 * Display all queued notices.
	 *
	 * Checks conditions and dismissed state before rendering.
	 *
	 * @since 2.0.0
	 */
	public function displayNotices() {
		if ( empty( $this->notices ) ) {
			return;
		}

		$dismissed = get_option( $this->dismissedOption, array() );

		foreach ( $this->notices as $id => $notice ) {
			// Skip dismissed notices
			if ( $notice['dismissible'] && in_array( $id, $dismissed, true ) ) {
				continue;
			}

			// Check conditions
			if ( ! empty( $notice['conditions'] ) && ! $this->checkConditions( $notice['conditions'] ) ) {
				continue;
			}

			$this->renderNotice( $id, $notice );
		}
	}

	/**
	 * Render a single notice.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  string $id     Notice ID.
	 * @param  array  $notice Notice configuration.
	 */
	private function renderNotice( $id, $notice ) {
		$classes = array( 'notice', 'notice-' . $notice['type'] );
		
		if ( $notice['dismissible'] ) {
			$classes[] = 'is-dismissible';
		}

		$class_string = implode( ' ', $classes );
		$data_attr    = $notice['dismissible'] ? ' data-notice-id="' . esc_attr( $id ) . '"' : '';

		echo '<div class="' . esc_attr( $class_string ) . '"' . $data_attr . '>';
		echo '<p>' . wp_kses_post( $notice['message'] ) . '</p>';
		echo '</div>';
	}

	/**
	 * Check if notice conditions are met.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  array $conditions Array of conditions to check.
	 * @return bool              Whether all conditions are met.
	 */
	private function checkConditions( $conditions ) {
		// Check screen condition
		if ( isset( $conditions['screen'] ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->id !== $conditions['screen'] ) {
				return false;
			}
		}

		// Check capability condition
		if ( isset( $conditions['capability'] ) && ! current_user_can( $conditions['capability'] ) ) {
			return false;
		}

		// Check custom callback condition
		if ( isset( $conditions['callback'] ) && is_callable( $conditions['callback'] ) ) {
			if ( ! call_user_func( $conditions['callback'] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Handle AJAX request to dismiss a notice.
	 *
	 * @since 2.0.0
	 */
	public function dismissNotice() {
		check_ajax_referer( 'lovetravel_child_dismiss_notice', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}

		$notice_id = isset( $_POST['notice_id'] ) ? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';

		if ( empty( $notice_id ) ) {
			wp_send_json_error( 'Invalid notice ID' );
		}

		$dismissed = get_option( $this->dismissedOption, array() );
		if ( ! in_array( $notice_id, $dismissed, true ) ) {
			$dismissed[] = $notice_id;
			update_option( $this->dismissedOption, $dismissed );
		}

		wp_send_json_success();
	}

	/**
	 * Enqueue admin scripts for notice dismissal.
	 *
	 * @since 2.0.0
	 */
	public function enqueueScripts() {
		if ( empty( $this->notices ) ) {
			return;
		}

		wp_enqueue_script(
			'lovetravel-child-admin-notices',
			get_stylesheet_directory_uri() . '/admin/assets/js/admin-notices.js',
			array( 'jquery' ),
			'2.0.0',
			true
		);

		wp_localize_script(
			'lovetravel-child-admin-notices',
			'lovetravelChildNotices',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'lovetravel_child_dismiss_notice' ),
			)
		);
	}

	/**
	 * Clear all dismissed notices.
	 *
	 * Useful for testing or after major updates.
	 *
	 * @since 2.0.0
	 */
	public function clearDismissed() {
		delete_option( $this->dismissedOption );
	}

	/**
	 * Remove a specific notice from dismissed list.
	 *
	 * @since 2.0.0
	 * @param string $notice_id Notice ID to re-enable.
	 */
	public function undismissNotice( $notice_id ) {
		$dismissed = get_option( $this->dismissedOption, array() );
		$key       = array_search( $notice_id, $dismissed, true );
		
		if ( false !== $key ) {
			unset( $dismissed[ $key ] );
			update_option( $this->dismissedOption, array_values( $dismissed ) );
		}
	}
}
