# Changelog

All notable changes to the LoveTravel Child Theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Comprehensive README.md documentation
- This CHANGELOG.md file for tracking changes

## [1.0.0] - 2025-09-23

### Added

#### Core Theme Features
- WordPress child theme setup with proper parent theme inheritance
- Custom constants: `LOVETRAVEL_CHILD_VERSION`, `LOVETRAVEL_CHILD_DIR`, `LOVETRAVEL_CHILD_URI`
- Modular architecture with organized file structure in `inc/` directory
- Translation support with `lovetravel-child` text domain
- Custom CSS variables for consistent theming
- Responsive design framework with mobile-first approach

#### Adventure Management System
- Custom post type label overrides: "Travel" → "Adventures" 
- Enhanced CPT labels for `nd_travel_cpt_1` with proper i18n
- Taxonomy overrides for Duration, Difficulty, and Month
- Automatic permalink structure updates with rewrite rule flushing
- Admin notice system for permalink refresh requirements
- Custom meta fields for adventure pricing and availability

#### Elementor Integration
- Pre-built Elementor template library with 4 adventure-focused sections:
  - Include/Exclude info section
  - Adventure About section  
  - Adventure Description section
  - Day Plan/Program section (toggle itinerary)
- Template import utility with admin interface
- Enhanced importer UI showing template titles and types from JSON metadata
- "Import Templates" button integrated into Elementor Library list view
- Automatic `elementor_library_type` taxonomy assignment during import
- Templates submenu under Elementor admin menu

#### Payload CMS Integration Tools

##### Mailchimp Subscriber Export
- Complete subscriber export system from Payload CMS to Mailchimp-compatible formats
- Support for CSV (recommended) and JSON export formats
- Advanced filtering options:
  - Include/exclude unsubscribed users
  - Date range filtering for targeted exports
  - Batch processing with pagination
- Real-time statistics dashboard showing:
  - Total subscribers count
  - Active subscribers count  
  - Recent subscribers (last 30 days)
- AJAX-powered interface with progress tracking
- Secure download system with nonce verification
- Integration with Mailchimp for WP plugin admin menu

##### Media Import System
- Comprehensive media import from Payload CMS to WordPress Media Library
- Background processing with reliable state management
- Configurable batch sizes (10-100 files per batch)
- Smart duplicate detection and skip logic
- Automatic WordPress thumbnail generation
- Real-time progress monitoring with:
  - Current file processing indicator
  - Progress bar visualization
  - Import statistics (total, imported, remaining)
- File-based logging system for debugging
- Start/Stop/Reset functionality with proper state persistence
- Admin interface under Media menu

##### Adventures Import Tool  
- Complete adventure content migration from Payload CMS
- Automatic field mapping between Payload and WordPress:
  - Core post fields (title, content, excerpt, status)
  - Adventure meta fields (pricing, availability, booking details)
  - Taxonomy assignments (Duration, Difficulty, Month)
- Media sideloading with automatic image attachment
- Configurable import options:
  - Batch processing with pagination
  - Dry run mode for preview without changes
  - Overwrite existing vs create new posts
- AJAX interface with real-time progress tracking
- Statistics dashboard and import monitoring
- Integration with Adventures CPT admin menu

#### Admin Utilities & Tools
- Centralized admin utilities in `inc/includes/admin-utilities.php`
- Rewrite rules notification system with dismissible notices
- Admin-specific styling for import/export tools
- Externalized JavaScript assets for better maintainability:
  - `admin-mailchimp-export.js` with AJAX configuration
  - `admin-adventures-import.js` with progress tracking
  - `admin-tools.css` for consistent admin styling
- Proper script/style enqueuing with version control and conditional loading

#### Development & Documentation
- Comprehensive development process documentation:
  - `process-develop.md` - Feature development workflow
  - `process-docs.md` - Documentation guidelines  
  - `process-release.md` - Release management process
- AI coding assistant guidelines in `.github/copilot-instructions.md`
- Detailed change tracking in `copilot-edit-log.md`
- WordPress Coding Standards compliance
- Proper error handling and user feedback systems
- Security best practices with nonce verification and capability checks

### Changed
- Custom post type `nd_travel_cpt_1` now displays as "Adventures" throughout admin interface
- Taxonomy labels updated to use travel-industry appropriate terminology
- Parent theme style inheritance improved with proper dependency handling
- Admin menu organization enhanced with logical tool grouping

### Technical Implementation Details

#### Architecture
- PHP 7.4+ compatibility with modern PHP practices
- Object-oriented approach for complex tools (import/export classes)
- Proper WordPress hook integration with appropriate priorities
- Modular design enabling easy feature additions/removals
- Comprehensive error handling with user-friendly feedback

#### Performance Optimizations  
- Conditional script/style loading to reduce overhead
- Background processing for resource-intensive operations
- Efficient database queries with proper indexing considerations
- Batch processing to prevent timeout issues
- Smart caching where appropriate

#### Security Features
- Nonce verification for all AJAX operations
- Capability checks for admin functionality
- Input sanitization and output escaping
- Secure file handling for uploads and downloads
- Protection against direct file access

#### Accessibility & Standards
- WordPress accessibility guidelines compliance
- Proper semantic HTML structure
- Screen reader friendly admin interfaces
- Keyboard navigation support
- WCAG 2.1 AA level considerations

### Developer Notes
- All custom functionality properly namespaced with `lovetravel_child_` prefix
- Extensive inline documentation following WordPress standards
- Translation-ready with proper text domain usage
- Hooks and filters available for further customization
- Follows WordPress coding standards (WPCS)

---

## Version History Summary

- **v1.0.0**: Initial release with complete adventure management system, Payload CMS integration, Elementor templates, and admin tools
- **Future versions**: Will follow semantic versioning with proper changelog documentation

## Upgrade Notes

### From Parent Theme Only
- Activate child theme and visit Settings → Permalinks to flush rewrite rules
- Import desired Elementor templates from the template library
- Configure Payload CMS endpoints if using integration tools

## Support & Resources

- **Documentation**: See README.md for comprehensive setup and usage instructions
- **Development**: Follow guidelines in `process-develop.md`
- **Issues**: Check WordPress error logs and verify parent theme compatibility
- **Customization**: Use provided hooks and filters for theme modifications

---

**Maintainer**: richardevcom  
**Repository**: wp-lovetravel-child  
**License**: GPL v2 or later