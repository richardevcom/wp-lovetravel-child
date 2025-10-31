/**
 * AJAX Utility Functions
 *
 * Modern fetch-based AJAX utilities for WordPress.
 * Replaces jQuery AJAX with native fetch API.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

/**
 * Default AJAX configuration
 */
const defaultConfig = {
	method: 'POST',
	credentials: 'same-origin',
	headers: {
		'Content-Type': 'application/x-www-form-urlencoded'
	}
};

/**
 * WordPress AJAX helper class
 */
export class WPAjax {
	constructor(ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php') {
		this.ajaxUrl = ajaxUrl;
	}

	/**
	 * Perform WordPress AJAX request
	 */
	async request(action, data = {}, options = {}) {
		const config = { ...defaultConfig, ...options };
		
		// Prepare form data
		const formData = new URLSearchParams();
		formData.append('action', action);
		
		// Add data to form
		Object.entries(data).forEach(([key, value]) => {
			if (value !== null && value !== undefined) {
				formData.append(key, value);
			}
		});

		try {
			const response = await fetch(this.ajaxUrl, {
				...config,
				body: formData
			});

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}

			const result = await response.json();
			return result;
		} catch (error) {
			console.error('AJAX Error:', error);
			throw error;
		}
	}

	/**
	 * POST request shorthand
	 */
	async post(action, data = {}, options = {}) {
		return this.request(action, data, { ...options, method: 'POST' });
	}

	/**
	 * GET request shorthand
	 */
	async get(action, data = {}, options = {}) {
		const params = new URLSearchParams(data);
		params.append('action', action);
		
		const url = `${this.ajaxUrl}?${params}`;
		
		try {
			const response = await fetch(url, {
				...defaultConfig,
				...options,
				method: 'GET'
			});

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}

			return await response.json();
		} catch (error) {
			console.error('AJAX GET Error:', error);
			throw error;
		}
	}
}

/**
 * Create default AJAX instance
 */
export const ajax = new WPAjax();

/**
 * Handle loading states for buttons
 */
export class LoadingButton {
	constructor(button, loadingText = 'Loading...') {
		this.button = button;
		this.originalText = button.textContent;
		this.loadingText = loadingText;
		this.isLoading = false;
	}

	start() {
		if (this.isLoading) return;
		
		this.isLoading = true;
		this.button.disabled = true;
		this.button.classList.add('loading');
		this.button.textContent = this.loadingText;
	}

	stop() {
		if (!this.isLoading) return;
		
		this.isLoading = false;
		this.button.disabled = false;
		this.button.classList.remove('loading');
		this.button.textContent = this.originalText;
	}

	error(errorText = 'Error occurred') {
		this.stop();
		this.button.classList.add('error');
		this.button.textContent = errorText;
		
		setTimeout(() => {
			this.button.classList.remove('error');
			this.button.textContent = this.originalText;
		}, 3000);
	}
}

/**
 * Show temporary error message
 */
export function showError(container, message, duration = 5000) {
	const errorDiv = document.createElement('div');
	errorDiv.className = 'lovetravel-error-message';
	errorDiv.style.cssText = `
		text-align: center;
		color: #dc3545;
		margin: 10px 0;
		padding: 10px;
		background: #f8d7da;
		border: 1px solid #f5c6cb;
		border-radius: 4px;
		animation: fadeIn 0.3s ease;
	`;
	errorDiv.textContent = message;

	// Add CSS animation
	if (!document.querySelector('#lovetravel-error-styles')) {
		const style = document.createElement('style');
		style.id = 'lovetravel-error-styles';
		style.textContent = `
			@keyframes fadeIn {
				from { opacity: 0; transform: translateY(-10px); }
				to { opacity: 1; transform: translateY(0); }
			}
			@keyframes fadeOut {
				from { opacity: 1; transform: translateY(0); }
				to { opacity: 0; transform: translateY(-10px); }
			}
		`;
		document.head.appendChild(style);
	}

	container.appendChild(errorDiv);

	// Auto-remove after duration
	setTimeout(() => {
		errorDiv.style.animation = 'fadeOut 0.3s ease';
		setTimeout(() => errorDiv.remove(), 300);
	}, duration);

	return errorDiv;
}

/**
 * Show temporary success message
 */
export function showSuccess(container, message, duration = 3000) {
	const successDiv = document.createElement('div');
	successDiv.className = 'lovetravel-success-message';
	successDiv.style.cssText = `
		text-align: center;
		color: #155724;
		margin: 10px 0;
		padding: 10px;
		background: #d4edda;
		border: 1px solid #c3e6cb;
		border-radius: 4px;
		animation: fadeIn 0.3s ease;
	`;
	successDiv.textContent = message;

	container.appendChild(successDiv);

	// Auto-remove after duration
	setTimeout(() => {
		successDiv.style.animation = 'fadeOut 0.3s ease';
		setTimeout(() => successDiv.remove(), 300);
	}, duration);

	return successDiv;
}