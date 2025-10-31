/**
 * DOM Utility Functions
 *
 * Modern vanilla JavaScript DOM manipulation utilities.
 * Replaces jQuery for common DOM operations.
 *
 * @package LoveTravelChild
 * @since   2.3.0
 */

/**
 * DOM ready handler that works across browsers
 */
export function ready(callback) {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', callback);
	} else {
		callback();
	}
}

/**
 * Query selector wrapper with multiple element support
 */
export function $(selector, context = document) {
	const elements = context.querySelectorAll(selector);
	return new DOMCollection(elements);
}

/**
 * Single element selector
 */
export function $$(selector, context = document) {
	return context.querySelector(selector);
}

/**
 * DOMCollection class for chainable operations
 */
class DOMCollection {
	constructor(elements) {
		this.elements = Array.from(elements);
		this.length = this.elements.length;
	}

	/**
	 * Execute callback for each element
	 */
	each(callback) {
		this.elements.forEach((element, index) => callback.call(element, element, index));
		return this;
	}

	/**
	 * Add event listener to all elements
	 */
	on(event, handler, options = {}) {
		this.elements.forEach(element => {
			element.addEventListener(event, handler, options);
		});
		return this;
	}

	/**
	 * Remove event listener from all elements
	 */
	off(event, handler, options = {}) {
		this.elements.forEach(element => {
			element.removeEventListener(event, handler, options);
		});
		return this;
	}

	/**
	 * Add CSS class to all elements
	 */
	addClass(className) {
		this.elements.forEach(element => element.classList.add(className));
		return this;
	}

	/**
	 * Remove CSS class from all elements
	 */
	removeClass(className) {
		this.elements.forEach(element => element.classList.remove(className));
		return this;
	}

	/**
	 * Toggle CSS class on all elements
	 */
	toggleClass(className, force) {
		this.elements.forEach(element => element.classList.toggle(className, force));
		return this;
	}

	/**
	 * Check if first element has class
	 */
	hasClass(className) {
		return this.elements[0]?.classList.contains(className) || false;
	}

	/**
	 * Set/get attribute
	 */
	attr(name, value) {
		if (value === undefined) {
			return this.elements[0]?.getAttribute(name);
		}
		this.elements.forEach(element => element.setAttribute(name, value));
		return this;
	}

	/**
	 * Set/get data attribute
	 */
	data(name, value) {
		if (value === undefined) {
			return this.elements[0]?.dataset[name];
		}
		this.elements.forEach(element => element.dataset[name] = value);
		return this;
	}

	/**
	 * Set/get text content
	 */
	text(content) {
		if (content === undefined) {
			return this.elements[0]?.textContent;
		}
		this.elements.forEach(element => element.textContent = content);
		return this;
	}

	/**
	 * Set/get innerHTML
	 */
	html(content) {
		if (content === undefined) {
			return this.elements[0]?.innerHTML;
		}
		this.elements.forEach(element => element.innerHTML = content);
		return this;
	}

	/**
	 * Hide elements
	 */
	hide() {
		this.elements.forEach(element => element.style.display = 'none');
		return this;
	}

	/**
	 * Show elements
	 */
	show(display = 'block') {
		this.elements.forEach(element => element.style.display = display);
		return this;
	}

	/**
	 * Fade out elements
	 */
	fadeOut(duration = 300, callback) {
		this.elements.forEach(element => {
			element.style.transition = `opacity ${duration}ms ease`;
			element.style.opacity = '0';
			setTimeout(() => {
				element.style.display = 'none';
				if (callback) callback(element);
			}, duration);
		});
		return this;
	}

	/**
	 * Fade in elements
	 */
	fadeIn(duration = 300, callback) {
		this.elements.forEach(element => {
			element.style.display = element.dataset.originalDisplay || 'block';
			element.style.opacity = '0';
			element.style.transition = `opacity ${duration}ms ease`;
			setTimeout(() => {
				element.style.opacity = '1';
				if (callback) callback(element);
			}, 10);
		});
		return this;
	}

	/**
	 * Find child elements
	 */
	find(selector) {
		const found = [];
		this.elements.forEach(element => {
			found.push(...element.querySelectorAll(selector));
		});
		return new DOMCollection(found);
	}

	/**
	 * Get closest parent matching selector
	 */
	closest(selector) {
		const found = [];
		this.elements.forEach(element => {
			const closest = element.closest(selector);
			if (closest && !found.includes(closest)) {
				found.push(closest);
			}
		});
		return new DOMCollection(found);
	}

	/**
	 * Append HTML content
	 */
	append(html) {
		this.elements.forEach(element => {
			if (typeof html === 'string') {
				element.insertAdjacentHTML('beforeend', html);
			} else {
				element.appendChild(html);
			}
		});
		return this;
	}

	/**
	 * Remove elements
	 */
	remove() {
		this.elements.forEach(element => element.remove());
		return this;
	}

	/**
	 * Get first element
	 */
	first() {
		return this.elements[0] || null;
	}

	/**
	 * Get element at index
	 */
	get(index) {
		return this.elements[index] || null;
	}
}

/**
 * Check if element is visible
 */
export function isVisible(element) {
	return element.offsetWidth > 0 && element.offsetHeight > 0;
}

/**
 * Get element position relative to document
 */
export function getOffset(element) {
	const rect = element.getBoundingClientRect();
	return {
		top: rect.top + window.pageYOffset,
		left: rect.left + window.pageXOffset
	};
}

/**
 * Trigger custom event
 */
export function trigger(element, eventName, detail = {}) {
	const event = new CustomEvent(eventName, { detail, bubbles: true });
	element.dispatchEvent(event);
}