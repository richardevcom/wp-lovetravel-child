/**
 * Unified Import UI Manager
 * Standardizes import tool UI behavior across all WordPress admin tools
 * 
 * @package LoveTravel_Child
 * @since 2.1.0
 */

(function($) {
    'use strict';

    /**
     * Import UI Manager Class
     * Handles consistent button states, notices, and progress displays
     */
    window.ImportUIManager = function(options) {
        // Default configuration
        this.config = $.extend({
            noticeContainer: '.wrap h1:first', // Insert notices after main heading
            noticeDuration: 0, // 0 = no auto-dismiss, manual only
            buttonStates: {
                ready: 'Import',
                processing: 'Importing...',
                completed: 'Remove Imports',
                removing: 'Removing...'
            },
            cssClasses: {
                notice: 'notice',
                noticeSuccess: 'notice-success',
                noticeError: 'notice-error', 
                noticeWarning: 'notice-warning',
                noticeInfo: 'notice-info',
                dismissible: 'is-dismissible',
                button: 'button',
                buttonPrimary: 'button-primary',
                buttonSecondary: 'button-secondary',
                buttonDanger: 'button-danger',
                disabled: 'disabled',
                importing: 'importing'
            }
        }, options || {});

        this.activeNotices = [];
        this.init();
    };

    ImportUIManager.prototype = {
        /**
         * Initialize the UI Manager
         */
        init: function() {
            this.bindDismissHandlers();
            console.log('ImportUIManager initialized with config:', this.config);
        },

        /**
         * Show a notice without auto-dismiss
         * @param {string} type - success, error, warning, info
         * @param {string} message - The message to display
         * @param {object} options - Additional options
         */
        showNotice: function(type, message, options) {
            options = options || {};
            
            var noticeClass = this.config.cssClasses.notice + ' ' + 
                             this.config.cssClasses['notice' + this.capitalizeFirst(type)];
            
            // Add dismissible class unless explicitly disabled
            if (options.dismissible !== false) {
                noticeClass += ' ' + this.config.cssClasses.dismissible;
            }

            // Create notice HTML
            var $notice = $('<div class="' + noticeClass + '">' +
                          '<p>' + message + '</p>' +
                          '</div>');

            // Add manual dismiss button if dismissible
            if (options.dismissible !== false) {
                var $dismissButton = $('<button type="button" class="notice-dismiss">' +
                                     '<span class="screen-reader-text">Dismiss this notice.</span>' +
                                     '</button>');
                $notice.append($dismissButton);
            }

            // Insert notice in DOM
            var $container = $(this.config.noticeContainer);
            if ($container.length === 0) {
                $container = $('.wrap h1').first(); // Fallback
            }
            $container.after($notice);

            // Track active notices
            this.activeNotices.push($notice);

            // Bind dismiss handler
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut(300, function() {
                    $notice.remove();
                });
            });

            // Accessibility: Announce to screen readers
            $notice.attr('role', 'alert');
            $notice.attr('aria-live', 'polite');

            console.log('Notice shown:', type, message);
            return $notice;
        },

        /**
         * Update button state and appearance
         * @param {jQuery} $button - The button element
         * @param {string} state - ready, processing, completed, removing
         * @param {object} options - Additional options
         */
        updateButtonState: function($button, state, options) {
            options = options || {};
            
            if (!$button || $button.length === 0) {
                console.warn('ImportUIManager: Invalid button element');
                return;
            }

            // Remove all state classes
            $button.removeClass(this.config.cssClasses.disabled)
                   .removeClass(this.config.cssClasses.importing)
                   .removeClass(this.config.cssClasses.buttonPrimary)
                   .removeClass(this.config.cssClasses.buttonSecondary)
                   .removeClass(this.config.cssClasses.buttonDanger);

            // Apply state-specific changes
            switch (state) {
                case 'ready':
                    $button.text(options.text || this.config.buttonStates.ready)
                           .prop('disabled', false)
                           .addClass(this.config.cssClasses.buttonPrimary);
                    break;

                case 'processing':
                    $button.text(options.text || this.config.buttonStates.processing)
                           .prop('disabled', true)
                           .addClass(this.config.cssClasses.disabled)
                           .addClass(this.config.cssClasses.importing);
                    break;

                case 'completed':
                    $button.text(options.text || this.config.buttonStates.completed)
                           .prop('disabled', false)
                           .addClass(this.config.cssClasses.buttonSecondary)
                           .addClass(this.config.cssClasses.buttonDanger);
                    break;

                case 'removing':
                    $button.text(options.text || this.config.buttonStates.removing)
                           .prop('disabled', true)
                           .addClass(this.config.cssClasses.disabled);
                    break;

                default:
                    console.warn('ImportUIManager: Unknown button state:', state);
            }

            console.log('Button state updated:', state, $button.get(0));
        },

        /**
         * Update progress display
         * @param {object} progressData - Progress information
         * @param {jQuery} $container - Progress container element
         */
        updateProgress: function(progressData, $container) {
            if (!$container || $container.length === 0) {
                console.warn('ImportUIManager: Invalid progress container');
                return;
            }

            var percentage = progressData.percentage || 0;
            var message = progressData.message || 'Processing...';
            var details = progressData.details || '';

            // Update progress bar
            $container.find('.progress-fill').css('width', percentage + '%');
            
            // Update status text
            $container.find('.progress-status').text(message);
            $container.find('.progress-details').text(details);

            // Update percentage display if exists
            $container.find('.progress-percentage').text(Math.round(percentage) + '%');

            console.log('Progress updated:', percentage + '%', message);
        },

        /**
         * Clear all active notices
         */
        clearNotices: function() {
            this.activeNotices.forEach(function($notice) {
                $notice.fadeOut(300, function() {
                    $notice.remove();
                });
            });
            this.activeNotices = [];
            console.log('All notices cleared');
        },

        /**
         * Bind global dismiss handlers for WordPress native dismissible notices
         */
        bindDismissHandlers: function() {
            $(document).on('click', '.notice-dismiss', function() {
                $(this).closest('.notice').fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },

        /**
         * Utility: Capitalize first letter
         */
        capitalizeFirst: function(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        /**
         * Create a standard Import/Remove button pair
         * @param {string} section - The import section name
         * @param {object} options - Button configuration
         */
        createButtonPair: function(section, options) {
            options = options || {};
            
            var importId = 'import-' + section;
            var removeId = 'remove-' + section;
            var sectionName = options.sectionName || this.capitalizeFirst(section.replace('_', ' '));

            var $importButton = $('<button type="button" class="button button-primary" id="' + importId + '">')
                               .text('Import ' + sectionName)
                               .data('step', section)
                               .data('action', 'import');

            var $removeButton = $('<button type="button" class="button button-secondary button-danger" id="' + removeId + '">')
                               .text('Remove Imports')
                               .data('step', section)
                               .data('action', 'remove')
                               .hide();

            return {
                import: $importButton,
                remove: $removeButton,
                container: $('<div class="import-button-group">').append($importButton, $removeButton)
            };
        },

        /**
         * Standard import flow handler
         * @param {string} section - The import section
         * @param {jQuery} $button - The button that was clicked
         * @param {function} importCallback - Function to handle the actual import
         */
        handleImportClick: function(section, $button, importCallback) {
            var self = this;
            
            // Update button to processing state
            self.updateButtonState($button, 'processing');
            
            // Show processing notice
            var $notice = self.showNotice('info', 'Starting ' + section + ' import...');
            
            // Call the import function
            if (typeof importCallback === 'function') {
                importCallback.call(this, section, $button, function(success, message, data) {
                    // Import completed callback
                    $notice.remove();
                    
                    if (success) {
                        self.updateButtonState($button, 'completed');
                        self.showNotice('success', message || (section + ' import completed successfully!'));
                        
                        // Switch button visibility
                        $button.hide();
                        $('#remove-' + section).show();
                        
                    } else {
                        self.updateButtonState($button, 'ready');
                        self.showNotice('error', message || (section + ' import failed.'));
                    }
                });
            }
        },

        /**
         * Standard remove flow handler
         * @param {string} section - The import section
         * @param {jQuery} $button - The button that was clicked  
         * @param {function} removeCallback - Function to handle the actual removal
         */
        handleRemoveClick: function(section, $button, removeCallback) {
            var self = this;
            var sectionName = this.capitalizeFirst(section.replace('_', ' '));
            
            // Confirm with user
            if (!confirm('Are you sure you want to remove all imported ' + sectionName + '? This action cannot be undone.')) {
                return;
            }
            
            // Update button to removing state
            self.updateButtonState($button, 'removing');
            
            // Show processing notice
            var $notice = self.showNotice('info', 'Removing ' + section + ' imports...');
            
            // Call the remove function
            if (typeof removeCallback === 'function') {
                removeCallback.call(this, section, $button, function(success, message, data) {
                    // Remove completed callback
                    $notice.remove();
                    
                    if (success) {
                        self.updateButtonState($button, 'ready', {text: 'Import ' + sectionName});
                        self.showNotice('success', message || (section + ' imports removed successfully!'));
                        
                        // Switch button visibility
                        $button.hide();
                        $('#import-' + section).show();
                        
                    } else {
                        self.updateButtonState($button, 'completed');
                        self.showNotice('error', message || (section + ' removal failed.'));
                    }
                });
            }
        }
    };

    // Global utility functions for backward compatibility
    window.showImportNotice = function(type, message, options) {
        if (!window.importUIManager) {
            window.importUIManager = new ImportUIManager();
        }
        return window.importUIManager.showNotice(type, message, options);
    };

    window.updateImportButtonState = function($button, state, options) {
        if (!window.importUIManager) {
            window.importUIManager = new ImportUIManager();
        }
        return window.importUIManager.updateButtonState($button, state, options);
    };

    // Initialize default instance when DOM is ready
    $(document).ready(function() {
        // Only initialize if we're on an admin page with import functionality
        if ($('.lovetravel-wizard').length > 0 || $('.import-tool').length > 0) {
            window.importUIManager = new ImportUIManager();
            console.log('Default ImportUIManager instance created');
        }
    });

})(jQuery);