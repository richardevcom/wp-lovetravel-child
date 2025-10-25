/**
 * Packages Widget - Load More Functionality
 *
 * Handles AJAX loading of additional packages for the Packages widget.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/elementor/assets/js
 * @since      2.2.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize Load More functionality when DOM is ready.
	 */
	$(document).ready(function() {
		initLoadMoreButtons();
	});

	/**
	 * Initialize all Load More buttons on the page.
	 *
	 * @since 2.2.0
	 */
	function initLoadMoreButtons() {
		$('.lovetravel-load-more-btn').each(function() {
			var $button = $(this);
			var $container = $button.closest('.elementor-widget-lovetravel-child-packages').find('.nd_travel_masonry_content');

			// Store initial offset (number of currently loaded posts)
			$button.data('current-offset', $container.find('.nd_travel_masonry_item').length);

			// Attach click handler
			$button.off('click.loadmore').on('click.loadmore', handleLoadMoreClick);
		});
	}

	/**
	 * Handle Load More button click.
	 *
	 * @since 2.2.0
	 * @param {Event} e Click event.
	 */
	function handleLoadMoreClick(e) {
		e.preventDefault();

		var $button = $(this);

		// Prevent duplicate requests
		if ($button.hasClass('loading')) {
			return;
		}

		// Get data attributes
		var widgetId = $button.data('widget-id');
		var postsPerPage = parseInt($button.data('posts-per-page'), 10) || 4;
		var currentOffset = parseInt($button.data('current-offset'), 10) || 0;
		var order = $button.data('order') || 'DESC';
		var orderby = $button.data('orderby') || 'date';
		var width = $button.data('width') || 'nd_travel_width_25_percentage';
		var layout = $button.data('layout') || 'layout-1';
		var packagesId = $button.data('packages-id') || '';
		var destinationId = $button.data('destination-id') || '';
		var typologySlug = $button.data('typology-slug') || '';
		var imageSize = $button.data('image-size') || 'large';

		// Get container
		var $container = $button.closest('.elementor-widget-lovetravel-child-packages').find('.nd_travel_masonry_content');

		// Set loading state
		$button.addClass('loading').prop('disabled', true);
		var originalText = $button.text();
		$button.text($button.data('loading-text') || 'Loading...');

		// Prepare AJAX data
		var ajaxData = {
			action: 'lovetravel_load_more_packages',
			nonce: lovetravelLoadMore.nonce,
			offset: currentOffset,
			posts_per_page: postsPerPage,
			order: order,
			orderby: orderby,
			width: width,
			layout: layout,
			packages_id: packagesId,
			destination_id: destinationId,
			typology_slug: typologySlug,
			image_size: imageSize
		};

		// Make AJAX request
		$.ajax({
			url: lovetravelLoadMore.ajaxurl,
			type: 'POST',
			data: ajaxData,
			dataType: 'json',
			success: function(response) {
				if (response.success && response.data.html) {
					// Append new items to container
					var $newItems = $(response.data.html);
					$container.append($newItems);

					// Update offset
					var newOffset = currentOffset + postsPerPage;
					$button.data('current-offset', newOffset);

					// Re-initialize masonry layout
					reinitMasonry($container);

					// Hide button if no more posts
					if (!response.data.has_more) {
						$button.fadeOut(300);
					} else {
						// Reset button state
						$button.removeClass('loading').prop('disabled', false).text(originalText);
					}
				} else {
					handleLoadError($button, originalText, 'No content received');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				var errorMsg = 'AJAX error: ' + textStatus;
				if (errorThrown) {
					errorMsg += ' - ' + errorThrown;
				}
				handleLoadError($button, originalText, errorMsg);
			}
		});
	}

	/**
	 * Handle AJAX error.
	 *
	 * @since 2.2.0
	 * @param {jQuery} $button The Load More button.
	 * @param {string} originalText Original button text.
	 * @param {string} errorMsg Error message for console.
	 */
	function handleLoadError($button, originalText, errorMsg) {
		console.error('Load More Error:', errorMsg);

		// Reset button state
		$button.removeClass('loading').prop('disabled', false).text(originalText);

		// Show user-friendly error message
		var $error = $('<div class="lovetravel-load-more-error" style="text-align: center; color: #dc3545; margin-top: 10px; padding: 10px; background: #f8d7da; border-radius: 4px;">Unable to load more items. Please try again.</div>');
		$button.after($error);

		// Auto-hide error after 5 seconds
		setTimeout(function() {
			$error.fadeOut(300, function() {
				$(this).remove();
			});
		}, 5000);
	}

	/**
	 * Re-initialize masonry layout after adding new items.
	 *
	 * @since 2.2.0
	 * @param {jQuery} $container The masonry container.
	 */
	function reinitMasonry($container) {
		// Wait for images to load
		$container.imagesLoaded(function() {
			// Trigger masonry re-layout if available
			if (typeof $.fn.masonry === 'function') {
				$container.masonry('reloadItems').masonry('layout');
			}

			// Alternative: Trigger window resize event (nd-travel uses this)
			$(window).trigger('resize');
		});
	}

	/**
	 * Re-initialize on Elementor preview refresh.
	 *
	 * @since 2.2.0
	 */
	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
			if ($scope.hasClass('elementor-widget-lovetravel-child-packages')) {
				initLoadMoreButtons();
			}
		});
	});

})(jQuery);
