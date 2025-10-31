/**
 * Packages Load More Component
 *
 * Modern ES6 class for AJAX package loading functionality.
 * Handles pagination, masonry layout, and error states.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

import { $, ready } from '../core/dom-utils.js';
import { ajax, LoadingButton, showError } from '../core/ajax-utils.js';
import { ElementorHooks, isElementorEditor, getLocalizedData } from '../core/wp-utils.js';

export class PackagesLoadMore {
	constructor(container = document) {
		this.container = container;
		this.selectors = {
			button: '.lovetravel-load-more-btn',
			packagesContainer: '.nd_travel_masonry_content',
			packageItem: '.nd_travel_masonry_item'
		};
		
		this.loadingButtons = new Map();
		this.localizedData = getLocalizedData('lovetravelLoadMore');
		
		this.init();
	}

	/**
	 * Fetch and replace packages based on filters (AJAX)
	 * params: object of filter params (date_from, date_to, price_min, etc.)
	 */
	async fetchFilteredPackages(params = {}) {
		// Ensure nonce and basics
		params.nonce = params.nonce || this.localizedData.nonce || '';
		params.offset = 0;
		params.is_editor = isElementorEditor() ? '1' : '0';

		// Determine container: allow passing container or a selector
		let container = null;
		if (params.container) {
			container = params.container;
			delete params.container;
		} else if (params.widgetSelector) {
			container = document.querySelector(params.widgetSelector)?.querySelector(this.selectors.packagesContainer) || null;
			delete params.widgetSelector;
		}

		if (!container) {
			// Fallback to first packages container on the page
			container = document.querySelector(this.selectors.packagesContainer);
			if (!container) return;
		}

		try {
			const response = await ajax.post('lovetravel_load_more_packages', params);
			if (response.success && response.data?.html) {
				// Replace current items with returned HTML
				container.innerHTML = response.data.html;

				// Reset offset on related button if present
				const widget = container.closest('.elementor-widget-lovetravel-child-packages');
				const button = widget?.querySelector(this.selectors.button) || null;
				if (button) {
					const postsPerPage = parseInt(button.dataset.postsPerPage) || 4;
					button.dataset.currentOffset = (response.data.count || container.querySelectorAll(this.selectors.packageItem).length).toString();
					// Show or hide button
					if (!response.data.has_more) {
						button.style.display = 'none';
					} else {
						button.style.display = '';
					}
				}

				// Re-init masonry
				await this.reinitMasonry(container);

				this.triggerEvent(container, 'packagesLoadMore:filtered', { hasMore: response.data.has_more });
			} else {
				throw new Error(response.data?.message || 'No content received');
			}
		} catch (err) {
			console.error('Fetch Filtered Packages Error', err);
			this.triggerEvent(container, 'packagesLoadMore:error', { message: err.message });
		}
	}

	/**
	 * Initialize component
	 */
	init() {
		this.initializeButtons();
		this.bindEvents();
	}

	/**
	 * Initialize all load more buttons
	 */
	initializeButtons() {
		const buttons = $(this.selectors.button, this.container);
		
		buttons.each(button => {
			const container = this.getPackagesContainer(button);
			if (!container) return;

			// Store initial offset (number of currently loaded posts)
			const currentItems = container.querySelectorAll(this.selectors.packageItem);
			button.dataset.currentOffset = currentItems.length.toString();

			// Create loading button instance
			const loadingText = button.dataset.loadingText || 'Loading...';
			this.loadingButtons.set(button, new LoadingButton(button, loadingText));
		});
	}

	/**
	 * Bind event handlers
	 */
	bindEvents() {
		this.container.addEventListener('click', this.handleClick.bind(this));
	}

	/**
	 * Handle button clicks
	 */
	async handleClick(event) {
		if (!event.target.matches(this.selectors.button)) return;
		
		event.preventDefault();
		
		const button = event.target;
		const loadingButton = this.loadingButtons.get(button);
		
		// Prevent duplicate requests
		if (loadingButton?.isLoading) return;
		
		try {
			await this.loadMorePackages(button);
		} catch (error) {
			console.error('Load More Error:', error);
			this.handleError(button, 'Unable to load more items. Please try again.');
		}
	}

	/**
	 * Load more packages via AJAX
	 */
	async loadMorePackages(button) {
		const loadingButton = this.loadingButtons.get(button);
		const container = this.getPackagesContainer(button);
		
		if (!container || !loadingButton) return;

		// Start loading state
		loadingButton.start();

		// Get request parameters
		const params = this.getRequestParams(button);
		
		try {
			// Make AJAX request
			const response = await ajax.post('lovetravel_load_more_packages', params);
			
			if (response.success && response.data?.html) {
				await this.handleSuccess(button, container, response.data);
			} else {
				throw new Error(response.data?.message || 'No content received');
			}
		} catch (error) {
			this.handleError(button, error.message);
			throw error;
		}
	}

	/**
	 * Get request parameters from button data attributes
	 */
	getRequestParams(button) {
		const currentOffset = parseInt(button.dataset.currentOffset) || 0;
		
		return {
			nonce: this.localizedData.nonce || '',
			offset: currentOffset,
			posts_per_page: parseInt(button.dataset.postsPerPage) || 4,
			order: button.dataset.order || 'DESC',
			orderby: button.dataset.orderby || 'date',
			width: button.dataset.width || 'nd_travel_width_25_percentage',
			layout: button.dataset.layout || 'layout-1',
			packages_id: button.dataset.packagesId || '',
			destination_id: button.dataset.destinationId || '',
			typology_slug: button.dataset.typologySlug || '',
			image_size: button.dataset.imageSize || 'large',
			is_editor: isElementorEditor() ? '1' : '0'
		};
	}

	/**
	 * Handle successful response
	 */
	async handleSuccess(button, container, data) {
		const loadingButton = this.loadingButtons.get(button);
		
		// Create new items from HTML
		const tempDiv = document.createElement('div');
		tempDiv.innerHTML = data.html;
		const newItems = Array.from(tempDiv.children);
		
		// Append new items to container
		newItems.forEach(item => container.appendChild(item));
		
		// Update offset
		const postsPerPage = parseInt(button.dataset.postsPerPage) || 4;
		const currentOffset = parseInt(button.dataset.currentOffset) || 0;
		button.dataset.currentOffset = (currentOffset + postsPerPage).toString();
		
		// Re-initialize masonry layout
		await this.reinitMasonry(container);
		
		// Handle button state
		if (!data.has_more) {
			// No more items - hide button
			button.style.transition = 'opacity 0.3s ease';
			button.style.opacity = '0';
			setTimeout(() => button.style.display = 'none', 300);
		} else {
			// More items available - reset button
			loadingButton.stop();
		}

		// Trigger custom event
		this.triggerEvent(container, 'packagesLoadMore:success', { 
			newItems, 
			hasMore: data.has_more 
		});
	}

	/**
	 * Handle error state
	 */
	handleError(button, message) {
		const loadingButton = this.loadingButtons.get(button);
		loadingButton?.stop();
		
		// Show error message
		const container = button.parentElement;
		showError(container, message, 5000);
		
		// Trigger custom event
		this.triggerEvent(button, 'packagesLoadMore:error', { message });
	}

	/**
	 * Re-initialize masonry layout
	 */
	async reinitMasonry(container) {
		return new Promise((resolve) => {
			// Wait for images to load
			this.imagesLoaded(container, () => {
				// Destroy existing masonry instance
				if (container.masonryInstance) {
					container.masonryInstance.destroy();
				}

				// Re-initialize masonry if available
				if (typeof window.Masonry === 'function') {
					container.masonryInstance = new window.Masonry(container, {
						itemSelector: this.selectors.packageItem
					});
				} else if (window.jQuery && typeof window.jQuery.fn.masonry === 'function') {
					// Fallback to jQuery masonry
					window.jQuery(container).masonry({
						itemSelector: this.selectors.packageItem
					});
				}

				// Trigger resize to ensure proper layout
				setTimeout(() => {
					window.dispatchEvent(new Event('resize'));
					resolve();
				}, 100);
			});
		});
	}

	/**
	 * Images loaded utility (replaces imagesLoaded plugin)
	 */
	imagesLoaded(container, callback) {
		const images = container.querySelectorAll('img');
		if (images.length === 0) {
			callback();
			return;
		}

		let loadedCount = 0;
		const totalImages = images.length;

		const checkComplete = () => {
			loadedCount++;
			if (loadedCount === totalImages) {
				callback();
			}
		};

		images.forEach(img => {
			if (img.complete) {
				checkComplete();
			} else {
				img.addEventListener('load', checkComplete);
				img.addEventListener('error', checkComplete);
			}
		});
	}

	/**
	 * Get packages container for a button
	 */
	getPackagesContainer(button) {
		const widget = button.closest('.elementor-widget-lovetravel-child-packages');
		return widget?.querySelector(this.selectors.packagesContainer);
	}

	/**
	 * Trigger custom event
	 */
	triggerEvent(element, eventName, detail = {}) {
		const event = new CustomEvent(eventName, { 
			detail, 
			bubbles: true,
			cancelable: true 
		});
		element.dispatchEvent(event);
	}

	/**
	 * Refresh component (useful for dynamic content)
	 */
	refresh() {
		this.loadingButtons.clear();
		this.initializeButtons();
	}

	/**
	 * Destroy component (cleanup)
	 */
	destroy() {
		this.container.removeEventListener('click', this.handleClick.bind(this));
		this.loadingButtons.clear();
	}
}

/**
 * Auto-initialize when DOM is ready
 */
ready(() => {
	new PackagesLoadMore();
});

/**
 * Elementor integration
 */
ElementorHooks.addAction('frontend/element_ready/lovetravel-child-packages.default', ($scope) => {
	new PackagesLoadMore($scope[0] || $scope);
});

export default PackagesLoadMore;