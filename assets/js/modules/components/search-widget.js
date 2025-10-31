/**
 * Modern Search Widget Component
 *
 * Handles interactions for the completely rewritten travel search widget.
 * Features: column click handling, HTML5 datepicker integration with showPicker(),
 * responsive interactions, and accessibility support.
 *
 * @package LoveTravelChild
 * @since   2.6.2
 */

export default class SearchWidget {
	constructor() {
		this.selectors = {
			widget: '.lovetravel-search-widget',
			form: '.lovetravel-search-form',
			column: '.lovetravel-search-column',
			field: '.lovetravel-search-field',
			input: '.lovetravel-search-input',
			select: '.lovetravel-search-select',
			dateInput: '.lovetravel-date-input',
			dateGroup: '.lovetravel-date-input-group',
			submitBtn: '.lovetravel-search-submit-btn'
		};
		
		this.init();
	}

	/**
	 * Initialize search widget functionality
	 */
	init() {
		this.bindEvents();
		this.initDateInputs();
	}

	/**
	 * Bind all event handlers
	 */
	bindEvents() {
		// Column click handling - trigger field focus/interaction
		document.addEventListener('click', (event) => {
			const column = event.target.closest(this.selectors.column);
			if (!column) return;

			// Skip if clicking on actual form controls
			if (this.isDirectFormControl(event.target)) return;

			this.handleColumnClick(column, event);
		});

		// Date input specific events
		document.addEventListener('click', (event) => {
			if (event.target.matches(this.selectors.dateInput)) {
				this.handleDateInputClick(event.target);
			}
		});

		// Select dropdown events
		document.addEventListener('click', (event) => {
			if (event.target.matches(this.selectors.select)) {
				this.handleSelectClick(event.target);
			}
		});

		// Date validation on change
		document.addEventListener('change', (event) => {
			if (event.target.matches(this.selectors.dateInput)) {
				this.validateDateInput(event.target);
			}
		});
	}

	/**
	 * Check if target is a direct form control that should handle its own events
	 */
	isDirectFormControl(target) {
		return (
			target.matches(this.selectors.input) ||
			target.matches(this.selectors.select) ||
			target.matches(this.selectors.dateInput) ||
			target.matches(this.selectors.submitBtn) ||
			target.matches('input, select, button')
		);
	}

	/**
	 * Handle column click - find and trigger appropriate field
	 */
	handleColumnClick(column, event) {
		// Find the primary input/select in this column
		const dateInput = column.querySelector(this.selectors.dateInput);
		const select = column.querySelector(this.selectors.select);
		const textInput = column.querySelector(this.selectors.input);

		if (dateInput) {
			this.triggerDatePicker(dateInput);
		} else if (select) {
			this.triggerSelectDropdown(select);
		} else if (textInput) {
			textInput.focus();
		}
	}

	/**
	 * Initialize HTML5 date inputs with proper format and validation
	 */
	initDateInputs() {
		const dateInputs = document.querySelectorAll(this.selectors.dateInput);
		
		dateInputs.forEach(input => {
			// Set minimum date to today to disable past dates
			const today = new Date().toISOString().split('T')[0];
			input.setAttribute('min', today);

			// Ensure proper attributes for mobile compatibility
			input.setAttribute('pattern', '[0-9]{4}-[0-9]{2}-[0-9]{2}');
			input.setAttribute('placeholder', 'dd.mm.yyyy');
			
			// Set max date to reasonable future limit (2 years)
			const maxDate = new Date();
			maxDate.setFullYear(maxDate.getFullYear() + 2);
			input.setAttribute('max', maxDate.toISOString().split('T')[0]);
		});
	}

	/**
	 * Handle date input click - trigger datepicker with showPicker()
	 */
	handleDateInputClick(dateInput) {
		this.triggerDatePicker(dateInput);
	}

	/**
	 * Trigger HTML5 datepicker using modern showPicker() method
	 */
	triggerDatePicker(dateInput) {
		try {
			// First, focus the input (required for mobile)
			dateInput.focus();

			// Use showPicker() method if available (modern browsers)
			if (typeof dateInput.showPicker === 'function') {
				// Delay slightly to ensure focus is processed
				setTimeout(() => {
					try {
						dateInput.showPicker();
					} catch (error) {
						console.warn('SearchWidget: showPicker() failed:', error);
						this.fallbackDateTrigger(dateInput);
					}
				}, 100);
			} else {
				// Fallback for browsers without showPicker()
				this.fallbackDateTrigger(dateInput);
			}
		} catch (error) {
			console.warn('SearchWidget: Date picker trigger failed:', error);
		}
	}

	/**
	 * Fallback method for triggering date picker in older browsers
	 */
	fallbackDateTrigger(dateInput) {
		try {
			// Method 1: Trigger click on the calendar indicator
			const calendarIndicator = dateInput.querySelector('::-webkit-calendar-picker-indicator');
			if (calendarIndicator) {
				calendarIndicator.click();
				return;
			}

			// Method 2: Simulate mouse events
			const events = ['mousedown', 'mouseup', 'click'];
			events.forEach(eventType => {
				const event = new MouseEvent(eventType, {
					bubbles: true,
					cancelable: true,
					view: window,
					detail: 1
				});
				dateInput.dispatchEvent(event);
			});

			// Method 3: Try focus + Enter key
			setTimeout(() => {
				const enterEvent = new KeyboardEvent('keydown', {
					key: 'Enter',
					code: 'Enter',
					keyCode: 13,
					bubbles: true
				});
				dateInput.dispatchEvent(enterEvent);
			}, 50);
		} catch (error) {
			console.warn('SearchWidget: Fallback date trigger failed:', error);
		}
	}

	/**
	 * Handle select dropdown click
	 */
	handleSelectClick(select) {
		this.triggerSelectDropdown(select);
	}

	/**
	 * Trigger select dropdown opening
	 */
	triggerSelectDropdown(select) {
		try {
			// Focus first
			select.focus();

			// Use showPicker() if available
			if (typeof select.showPicker === 'function') {
				setTimeout(() => {
					try {
						select.showPicker();
					} catch (error) {
						console.warn('SearchWidget: Select showPicker() failed:', error);
					}
				}, 50);
			}
		} catch (error) {
			console.warn('SearchWidget: Select dropdown trigger failed:', error);
		}
	}

	/**
	 * Validate date input and update related fields
	 */
	validateDateInput(dateInput) {
		const value = dateInput.value;
		if (!value) return;

		// Handle date range validation only
		this.validateDateRange(dateInput);
	}

	/**
	 * Validate date range (from/to dates)
	 */
	validateDateRange(changedInput) {
		const dateRangeContainer = changedInput.closest('.lovetravel-date-range-inputs');
		if (!dateRangeContainer) return;

		const fromInput = dateRangeContainer.querySelector('input[name="nd_travel_archive_form_date_from"]');
		const toInput = dateRangeContainer.querySelector('input[name="nd_travel_archive_form_date_to"]');

		if (!fromInput || !toInput) return;

		const fromDate = fromInput.value ? new Date(fromInput.value) : null;
		const toDate = toInput.value ? new Date(toInput.value) : null;

		// Update minimum date for "to" field when "from" changes
		if (changedInput === fromInput && fromDate) {
			toInput.setAttribute('min', fromInput.value);
		}

		// If both dates are set and "to" is before "from", clear "to" field
		if (fromDate && toDate && toDate < fromDate) {
			toInput.value = '';
		}
	}

	/**
	 * Format date for display (convert from YYYY-MM-DD to DD.MM.YYYY)
	 */
	formatDateForDisplay(isoDate) {
		if (!isoDate) return '';
		
		const date = new Date(isoDate);
		const day = String(date.getDate()).padStart(2, '0');
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const year = date.getFullYear();
		
		return `${day}.${month}.${year}`;
	}

	/**
	 * Convert DD.MM.YYYY to YYYY-MM-DD for HTML5 date input
	 */
	formatDateForInput(displayDate) {
		if (!displayDate) return '';
		
		const parts = displayDate.split('.');
		if (parts.length === 3) {
			const [day, month, year] = parts;
			return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
		}
		
		return displayDate;
	}
}