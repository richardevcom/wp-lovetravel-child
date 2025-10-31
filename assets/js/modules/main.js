/**
 * LoveTravel Child Theme - Main Module Entry Point
 *
 * Modern ES6 module system orchestrator.
 * Initializes all components and provides global theme functionality.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

import { ready } from './core/dom-utils.js';
import { isElementorEditor, isAdmin } from './core/wp-utils.js';

// Import components
import TeamMemberCard from './components/team-member-card.js';
import PackagesLoadMore from './components/packages-load-more.js';
import AdminNotices from './components/admin-notices.js';
import SearchWidget from './components/search-widget.js';

/**
 * Main Theme Class
 */
class LoveTravelChildTheme {
	constructor() {
		this.version = '2.3.0';
		this.components = new Map();
		
		this.init();
	}

	/**
	 * Initialize theme
	 */
	init() {
		this.logStart();
		this.registerComponents();
		this.bindGlobalEvents();
	}

	/**
	 * Register all components
	 */
	registerComponents() {
		// Always available components
		this.components.set('teamMemberCard', new TeamMemberCard());
		this.components.set('packagesLoadMore', new PackagesLoadMore());
		this.components.set('searchWidget', new SearchWidget());
		
		// Admin-only components
		if (isAdmin()) {
			this.components.set('adminNotices', new AdminNotices());
		}
	}

	/**
	 * Bind global event handlers
	 */
	bindGlobalEvents() {
		// Handle Elementor editor mode (with proper null checking)
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/global', () => {
				this.refreshComponents();
			});
		}

		// Handle dynamic content loading
		document.addEventListener('lovetravel:contentLoaded', () => {
			this.refreshComponents();
		});

		// Handle window resize for responsive components
		let resizeTimeout;
		window.addEventListener('resize', () => {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(() => {
				this.triggerEvent(document, 'lovetravel:resize');
			}, 250);
		});
	}

	/**
	 * Refresh all components (useful for dynamic content)
	 */
	refreshComponents() {
		this.components.forEach(component => {
			if (typeof component.refresh === 'function') {
				component.refresh();
			}
		});
	}

	/**
	 * Get component instance
	 */
	getComponent(name) {
		return this.components.get(name);
	}

	/**
	 * Trigger custom event
	 */
	triggerEvent(element, eventName, detail = {}) {
		const event = new CustomEvent(eventName, { 
			detail: { ...detail, theme: this }, 
			bubbles: true,
			cancelable: true 
		});
		element.dispatchEvent(event);
	}

	/**
	 * Log initialization
	 */
	logStart() {
		// Production-ready logging
		if (window.console && console.log && window.location.hostname === 'localhost') {
			console.log(`🚀 LoveTravel Child Theme v${this.version} - Modern ES6 Edition`);
			console.log('📦 Components:', Array.from(this.components.keys()));
		}
	}

	/**
	 * Destroy all components (cleanup)
	 */
	destroy() {
		this.components.forEach(component => {
			if (typeof component.destroy === 'function') {
				component.destroy();
			}
		});
		this.components.clear();
	}
}

/**
 * Initialize theme when DOM is ready
 */
let themeInstance = null;

ready(() => {
	themeInstance = new LoveTravelChildTheme();
	
	// Make globally available for debugging
	if (window.console) {
		window.LoveTravelChild = themeInstance;
	}
});

/**
 * Export for module use
 */
export default LoveTravelChildTheme;