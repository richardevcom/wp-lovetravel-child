/**
 * Admin Notices Component
 *
 * Modern ES6 class for WordPress admin notice functionality.
 * Handles dismissible notices, AJAX operations, and animations.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

import { $, ready } from '../core/dom-utils.js';
import { ajax, showSuccess, showError } from '../core/ajax-utils.js';
import { isAdmin, getLocalizedData } from '../core/wp-utils.js';

export class AdminNotices {
	constructor(container = document) {
		this.container = container;
		this.selectors = {
			notice: '.notice.is-dismissible',
			dismissButton: '.notice-dismiss',
			importButton: '.lovetravel-child-import-templates',
			refreshButton: '.lovetravel-child-refresh-status'
		};
		
		this.localizedData = getLocalizedData('lovetravelAdmin');
		
		// Only initialize in admin
		if (isAdmin()) {
			this.init();
		}
	}

	/**
	 * Initialize component
	 */
	init() {
		this.bindEvents();
		this.enhanceExistingNotices();
	}

	/**
	 * Bind event handlers
	 */
	bindEvents() {
		this.container.addEventListener('click', this.handleClick.bind(this));
	}

	/**
	 * Handle all click events
	 */
	async handleClick(event) {
		if (event.target.matches(this.selectors.dismissButton)) {
			this.handleDismiss(event);
		} else if (event.target.matches(this.selectors.importButton)) {
			await this.handleImportTemplates(event);
		} else if (event.target.matches(this.selectors.refreshButton)) {
			await this.handleRefreshStatus(event);
		}
	}

	/**
	 * Handle notice dismiss
	 */
	handleDismiss(event) {
		event.preventDefault();
		
		const notice = event.target.closest(this.selectors.notice);
		if (!notice) return;

		// Get notice key for persistent dismissal
		const noticeKey = notice.dataset.dismissKey;
		
		// Animate out
		this.dismissNotice(notice);
		
		// Save dismissal state if key provided
		if (noticeKey) {
			this.saveDismissalState(noticeKey);
		}
	}

	/**
	 * Handle template import
	 */
	async handleImportTemplates(event) {
		event.preventDefault();
		
		const button = event.target;
		const originalText = button.textContent;
		
		// Set loading state
		button.disabled = true;
		button.textContent = 'Importing...';
		button.classList.add('updating-message');
		
		try {
			const response = await ajax.post('lovetravel_import_templates', {
				nonce: this.localizedData.nonce || ''
			});
			
			if (response.success) {
				showSuccess(button.parentElement, response.data.message || 'Templates imported successfully!');
				
				// Refresh the page after success
				setTimeout(() => {
					window.location.reload();
				}, 1500);
			} else {
				throw new Error(response.data?.message || 'Import failed');
			}
		} catch (error) {
			showError(button.parentElement, error.message);
			
			// Reset button state
			button.disabled = false;
			button.textContent = originalText;
			button.classList.remove('updating-message');
		}
	}

	/**
	 * Handle status refresh
	 */
	async handleRefreshStatus(event) {
		event.preventDefault();
		
		const button = event.target;
		const originalText = button.textContent;
		
		// Set loading state
		button.disabled = true;
		button.textContent = 'Refreshing...';
		
		try {
			const response = await ajax.post('lovetravel_refresh_template_status', {
				nonce: this.localizedData.nonce || ''
			});
			
			if (response.success) {
				// Update status table if present
				const statusTable = document.querySelector('.lovetravel-template-status-table');
				if (statusTable && response.data.html) {
					statusTable.innerHTML = response.data.html;
				}
				
				showSuccess(button.parentElement, 'Status refreshed successfully!');
			} else {
				throw new Error(response.data?.message || 'Refresh failed');
			}
		} catch (error) {
			showError(button.parentElement, error.message);
		} finally {
			// Reset button state
			setTimeout(() => {
				button.disabled = false;
				button.textContent = originalText;
			}, 1000);
		}
	}

	/**
	 * Dismiss notice with animation
	 */
	dismissNotice(notice) {
		notice.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
		notice.style.opacity = '0';
		notice.style.transform = 'translateX(100%)';
		
		setTimeout(() => {
			notice.remove();
		}, 300);
	}

	/**
	 * Save dismissal state via AJAX
	 */
	async saveDismissalState(noticeKey) {
		try {
			await ajax.post('lovetravel_dismiss_notice', {
				notice_key: noticeKey,
				nonce: this.localizedData.nonce || ''
			});
		} catch (error) {
			console.warn('Failed to save notice dismissal state:', error);
		}
	}

	/**
	 * Enhance existing notices with better styling
	 */
	enhanceExistingNotices() {
		const notices = $(this.selectors.notice, this.container);
		
		notices.each(notice => {
			// Add smooth transitions
			notice.style.transition = 'all 0.3s ease';
			
			// Enhance dismiss button
			const dismissBtn = notice.querySelector(this.selectors.dismissButton);
			if (dismissBtn) {
				dismissBtn.setAttribute('aria-label', 'Dismiss notice');
				dismissBtn.title = 'Dismiss this notice';
			}
		});
	}

	/**
	 * Create new notice programmatically
	 */
	createNotice(type, message, options = {}) {
		const {
			dismissible = true,
			dismissKey = null,
			actions = [],
			container = null
		} = options;
		
		// Create notice element
		const notice = document.createElement('div');
		notice.className = `notice notice-${type}`;
		if (dismissible) {
			notice.classList.add('is-dismissible');
		}
		
		// Add dismiss key if provided
		if (dismissKey) {
			notice.dataset.dismissKey = dismissKey;
		}
		
		// Create message paragraph
		const messagePara = document.createElement('p');
		messagePara.innerHTML = message;
		notice.appendChild(messagePara);
		
		// Add action buttons if provided
		if (actions.length > 0) {
			const actionsContainer = document.createElement('p');
			actions.forEach(action => {
				const button = document.createElement('button');
				button.className = action.class || 'button button-secondary';
				button.textContent = action.text;
				button.onclick = action.onclick;
				actionsContainer.appendChild(button);
			});
			notice.appendChild(actionsContainer);
		}
		
		// Add dismiss button if dismissible
		if (dismissible) {
			const dismissBtn = document.createElement('button');
			dismissBtn.type = 'button';
			dismissBtn.className = 'notice-dismiss';
			dismissBtn.innerHTML = '<span class="screen-reader-text">Dismiss this notice.</span>';
			notice.appendChild(dismissBtn);
		}
		
		// Insert into container
		const targetContainer = container || document.querySelector('.wrap h1') || document.body;
		if (targetContainer.tagName === 'H1') {
			targetContainer.parentNode.insertBefore(notice, targetContainer.nextSibling);
		} else {
			targetContainer.appendChild(notice);
		}
		
		// Animate in
		notice.style.opacity = '0';
		notice.style.transform = 'translateY(-10px)';
		setTimeout(() => {
			notice.style.transition = 'all 0.3s ease';
			notice.style.opacity = '1';
			notice.style.transform = 'translateY(0)';
		}, 10);
		
		return notice;
	}

	/**
	 * Show success notice
	 */
	showSuccess(message, options = {}) {
		return this.createNotice('success', message, options);
	}

	/**
	 * Show error notice
	 */
	showError(message, options = {}) {
		return this.createNotice('error', message, options);
	}

	/**
	 * Show warning notice
	 */
	showWarning(message, options = {}) {
		return this.createNotice('warning', message, options);
	}

	/**
	 * Show info notice
	 */
	showInfo(message, options = {}) {
		return this.createNotice('info', message, options);
	}

	/**
	 * Remove all notices of a specific type
	 */
	clearNotices(type = null) {
		const selector = type ? `.notice-${type}` : '.notice';
		const notices = $(selector, this.container);
		
		notices.each(notice => this.dismissNotice(notice));
	}

	/**
	 * Refresh component (useful for dynamic content)
	 */
	refresh() {
		this.enhanceExistingNotices();
	}

	/**
	 * Destroy component (cleanup)
	 */
	destroy() {
		this.container.removeEventListener('click', this.handleClick.bind(this));
	}
}

/**
 * Auto-initialize when DOM is ready (admin only)
 */
ready(() => {
	if (isAdmin()) {
		new AdminNotices();
	}
});

export default AdminNotices;