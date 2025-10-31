/**
 * Team Member Card Component
 *
 * Modern ES6 class for team member card functionality.
 * Handles expandable about text and card interactions.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

import { $, ready } from '../core/dom-utils.js';
import { ElementorHooks } from '../core/wp-utils.js';

export class TeamMemberCard {
	constructor(container = document) {
		this.container = container;
		this.selectors = {
			card: '.team-member-card',
			readMore: '.read-more-link',
			readLess: '.read-less-link',
			aboutSection: '.team-member-about',
			excerpt: '.about-excerpt',
			fullText: '.about-full',
			socialLinks: '.team-member-social-overlay a'
		};
		
		this.init();
	}

	/**
	 * Initialize component
	 */
	init() {
		this.bindEvents();
		this.setupAccessibility();
	}

	/**
	 * Bind event handlers
	 */
	bindEvents() {
		// Use event delegation for dynamic content
		this.container.addEventListener('click', this.handleClick.bind(this));
	}

	/**
	 * Handle all click events
	 */
	handleClick(event) {
		const target = event.target;

		// Handle read more links
		if (target.matches(this.selectors.readMore)) {
			event.preventDefault();
			event.stopPropagation();
			this.expandAboutText(target);
			return;
		}
		
		// Handle read less links
		if (target.matches(this.selectors.readLess)) {
			event.preventDefault();
			event.stopPropagation();
			this.collapseAboutText(target);
			return;
		}
		
		// Don't handle card click if clicking on social links
		if (target.matches(this.selectors.socialLinks) || target.closest(this.selectors.socialLinks)) {
			return; // Let social links work normally
		}
		
		// Handle card click (expand/collapse toggle)
		const card = target.closest(this.selectors.card);
		if (card) {
			const aboutSection = card.querySelector(this.selectors.aboutSection);
			const readMoreLink = card.querySelector(this.selectors.readMore);
			const readLessLink = card.querySelector(this.selectors.readLess);
			
			// Check if card is already expanded
			const isExpanded = aboutSection && aboutSection.getAttribute('aria-expanded') === 'true';
			
			if (isExpanded && readLessLink) {
				event.preventDefault();
				this.collapseAboutText(readLessLink);
			} else if (readMoreLink && this.isVisible(readMoreLink)) {
				event.preventDefault();
				// First collapse any other expanded cards
				this.collapseOtherCards(card);
				this.expandAboutText(readMoreLink);
			}
		}
	}

	/**
	 * Expand about text
	 */
	expandAboutText(readMoreLink) {
		const aboutSection = readMoreLink.closest(this.selectors.aboutSection);
		if (!aboutSection) return;

		// First collapse other expanded cards
		const currentCard = readMoreLink.closest(this.selectors.card);
		this.collapseOtherCards(currentCard);

		const excerpt = aboutSection.querySelector(this.selectors.excerpt);
		const fullText = aboutSection.querySelector(this.selectors.fullText);

		if (excerpt && fullText) {
			// Use CSS classes for show/hide instead of inline styles
			excerpt.classList.add('collapsed');
			fullText.classList.add('expanded');
			
			// Update accessibility
			aboutSection.setAttribute('aria-expanded', 'true');

			// Trigger custom event
			this.triggerEvent(aboutSection, 'teamMember:expanded');
		}
	}

	/**
	 * Collapse about text
	 */
	collapseAboutText(readLessLink) {
		const aboutSection = readLessLink.closest(this.selectors.aboutSection);
		if (!aboutSection) return;

		const excerpt = aboutSection.querySelector(this.selectors.excerpt);
		const fullText = aboutSection.querySelector(this.selectors.fullText);

		if (excerpt && fullText) {
			// Use CSS classes for show/hide instead of inline styles
			fullText.classList.remove('expanded');
			excerpt.classList.remove('collapsed');
			
			// Update accessibility
			aboutSection.setAttribute('aria-expanded', 'false');

			// Trigger custom event
			this.triggerEvent(aboutSection, 'teamMember:collapsed');
		}
	}

	/**
	 * Collapse all other expanded cards except the current one
	 */
	collapseOtherCards(currentCard) {
		const allCards = this.container.querySelectorAll(this.selectors.card);
		
		allCards.forEach(card => {
			if (card === currentCard) return; // Skip current card
			
			const aboutSection = card.querySelector(this.selectors.aboutSection);
			if (aboutSection && aboutSection.getAttribute('aria-expanded') === 'true') {
				const readLessLink = card.querySelector(this.selectors.readLess);
				if (readLessLink) {
					this.collapseAboutText(readLessLink);
				}
			}
		});
	}

	/**
	 * Setup accessibility features
	 */
	setupAccessibility() {
		const cards = $(this.selectors.card, this.container);
		
		cards.each(card => {
			// Add keyboard support for card expansion
			const readMoreLink = card.querySelector(this.selectors.readMore);
			if (readMoreLink) {
				card.setAttribute('tabindex', '0');
				card.setAttribute('role', 'button');
				card.setAttribute('aria-label', 'Click to expand team member details');
				
				// Handle keyboard activation
				card.addEventListener('keydown', (event) => {
					if (event.key === 'Enter' || event.key === ' ') {
						event.preventDefault();
						this.expandAboutText(readMoreLink);
					}
				});
			}

			// Add proper ARIA labels to social links
			const socialLinks = card.querySelectorAll(this.selectors.socialLinks);
			socialLinks.forEach(link => {
				const existing = link.getAttribute('aria-label');
				if (!existing) {
					const icon = link.querySelector('i');
					const platform = this.getSocialPlatform(icon);
					if (platform) {
						link.setAttribute('aria-label', `Visit ${platform} profile`);
					}
				}
			});
		});
	}

	/**
	 * Get social platform name from icon class
	 */
	getSocialPlatform(iconElement) {
		if (!iconElement) return '';
		
		const className = iconElement.className;
		const platforms = {
			'facebook': 'Facebook',
			'twitter': 'Twitter',
			'instagram': 'Instagram',
			'linkedin': 'LinkedIn',
			'youtube': 'YouTube',
			'github': 'GitHub'
		};

		for (const [key, name] of Object.entries(platforms)) {
			if (className.includes(key)) {
				return name;
			}
		}
		
		return 'Social Media';
	}

	/**
	 * Check if element is visible
	 */
	isVisible(element) {
		return element && element.offsetWidth > 0 && element.offsetHeight > 0;
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
	 * Destroy component (cleanup)
	 */
	destroy() {
		this.container.removeEventListener('click', this.handleClick.bind(this));
	}
}

/**
 * Auto-initialize when DOM is ready
 */
ready(() => {
	// Initialize for main document
	new TeamMemberCard();
});

/**
 * Elementor integration
 */
ElementorHooks.addAction('frontend/element_ready/lovetravel-child-team-member-card.default', ($scope) => {
	new TeamMemberCard($scope[0] || $scope);
});

ElementorHooks.addAction('frontend/element_ready/lovetravel-child-team-member-grid.default', ($scope) => {
	new TeamMemberCard($scope[0] || $scope);
});

export default TeamMemberCard;