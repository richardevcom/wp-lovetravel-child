/**
 * WordPress Integration Utilities
 *
 * WordPress-specific utilities for modern JavaScript.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

/**
 * Check if we're in WordPress admin
 */
export function isAdmin() {
	return document.body.classList.contains('wp-admin');
}

/**
 * Check if we're in Elementor editor
 */
export function isElementorEditor() {
	return typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode();
}

/**
 * Check if we're in Elementor preview
 */
export function isElementorPreview() {
	return document.body.classList.contains('elementor-page');
}

/**
 * Get WordPress AJAX URL
 */
export function getAjaxUrl() {
	return window.ajaxurl || window.wp?.ajax?.settings?.url || '/wp-admin/admin-ajax.php';
}

/**
 * Get WordPress REST API URL
 */
export function getRestUrl(endpoint = '') {
	const restUrl = window.wpApiSettings?.root || '/wp-json/wp/v2/';
	return restUrl + endpoint.replace(/^\//, '');
}

/**
 * Get WordPress nonce for REST API
 */
export function getRestNonce() {
	return window.wpApiSettings?.nonce || '';
}

/**
 * Elementor hooks system integration
 */
export class ElementorHooks {
	static addAction(hook, callback, priority = 10) {
		if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
			elementorFrontend.hooks.addAction(hook, callback, priority);
		}
	}

	static addFilter(hook, callback, priority = 10) {
		if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
			elementorFrontend.hooks.addFilter(hook, callback, priority);
		}
	}

	static doAction(hook, ...args) {
		if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
			elementorFrontend.hooks.doAction(hook, ...args);
		}
	}
}

/**
 * WordPress localized data helper
 */
export function getLocalizedData(handle) {
	return window[handle] || {};
}

/**
 * Check if script is localized
 */
export function isLocalized(handle) {
	return typeof window[handle] === 'object';
}

/**
 * Media library utilities
 */
export class MediaLibrary {
	static open(options = {}) {
		if (typeof wp === 'undefined' || !wp.media) {
			console.warn('WordPress media library not available');
			return;
		}

		const defaultOptions = {
			title: 'Select Media',
			button: { text: 'Use this media' },
			multiple: false
		};

		const frame = wp.media({
			...defaultOptions,
			...options
		});

		frame.open();
		return frame;
	}
}

/**
 * WordPress customizer utilities
 */
export class Customizer {
	static isActive() {
		return typeof wp !== 'undefined' && wp.customize;
	}

	static bind(setting, callback) {
		if (this.isActive() && wp.customize(setting)) {
			wp.customize(setting, callback);
		}
	}

	static preview(callback) {
		if (this.isActive() && wp.customize.preview) {
			wp.customize.preview.bind('ready', callback);
		}
	}
}