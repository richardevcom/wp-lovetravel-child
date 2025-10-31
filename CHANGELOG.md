# Changelog

All notable changes to the LoveTravel Child Theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.6.4] - 2025-01-28

### Fixed
- **Date search functionality**: Fixed compatibility with nd-travel plugin search
- Date input now sends correct parameter `nd_travel_archive_form_date` in YYYYMMDD format
- JavaScript automatically converts HTML5 date format (YYYY-MM-DD) to plugin format on submit
- Simplified date input to single "Departure Date" field (plugin searches for availability ranges)
- Search now correctly finds adventures that have availability covering the selected date

### Changed
- Date range inputs simplified to single departure date selector
- Updated date input label from "From/To" to "Departure Date"
- Hidden input handles plugin parameter compatibility automatically

## [2.6.3] - 2025-01-28

### Changed
- Finalized search widget with automatic column sizing and flex-wrap behavior
- Updated submit button: black default, accent hover, 100px minimum width
- Removed all hover effects from columns as requested
- Improved responsive layout with proper content-based column sizing

### Removed
- Columns setting from Elementor widget controls (now automatic)
- Calendar icons from date inputs (native browser icons only)
- Error popup notifications (replaced with date restrictions)
- Background color from submit button column

### Fixed
- Date column overflow issues with flexible sizing
- Past date selection (now disabled via min attribute)
- Submit button adaptability to column width

### Added
- Custom datepicker styling to match site theme
- Enhanced native date picker appearance with site colors
- Automatic column wrapping when insufficient width
- Maximum date limit (2 years from today)

## [2.6.2] - 2025-01-28

### Added
- Completely rewritten Search widget with modern booking.com-inspired design
- New HTML structure: flexbox container with icon+content columns and gray dividers
- HTML5 date inputs with reliable showPicker() method for datepicker triggering
- Modern CSS: white rounded container, 35px accent-colored icons, minimal borderless inputs
- Responsive design with mobile-first approach and proper breakpoints
- Enhanced JavaScript: column click handling, date validation, error toast notifications
- Accessibility features: focus management, keyboard navigation, screen reader support

### Changed
- Complete HTML template rewrite with semantic class structure (lovetravel-*)
- Modern flexbox-based layout replacing old float-based system
- Improved date range functionality with side-by-side From/To inputs
- Enhanced user interactions with visual feedback and error handling

### Removed
- All legacy HTML structure and CSS classes (nd_travel_*)
- Old JavaScript with dd.mm.yyyy text formatting (replaced with native HTML5 dates)
- Deprecated render methods and styling approaches

## [2.6.1] - 2025-01-28

### Removed
- Months control section from Search widget Elementor editor (cleanup after taxonomy removal)

### Fixed
- Elementor editor now only shows relevant controls (date range instead of months)

## [2.6.0] - 2025-01-28

### Added
- Complete Search widget redesign with modern travel platform UX (inspired by booking.com/Airbnb)
- Flexbox-based layout with columns and gray dividers
- 35px icons with modern spacing and visual hierarchy
- Responsive design with proper mobile/tablet breakpoints

### Changed
- Replaced months taxonomy filtering with HTML5 date range inputs (dd.mm.yyyy format)
- Enhanced CSS with modern box-shadows, transitions, and hover effects
- Improved JavaScript with better event handling and date validation

### Removed
- Months taxonomy (nd_travel_cpt_1_tax_4) completely eliminated from system

## [2.0.0] - 2025-01-27

### Added
- Clean restart of child theme development
- Selective import strategy from previous version backup
- Enhanced project structure and organization

### Changed
- Complete rebuild from ground up
- Improved code organization and WordPress standards compliance
- Streamlined development workflow

### Security
- Fresh codebase with modern security practices
- Removal of legacy/temporary development tools

### Notes
- Previous version backed up to `lovetravel-child-backup`
- Selective feature import from v1.x based on production requirements
- Focus on maintainable, standards-compliant code

## [1.0.0] - 2025-09-23

### Added
- Initial child theme setup
- Custom post type terminology (Adventures)
- Payload CMS import tools (temporary)
- Elementor template library integration
- Mailchimp subscriber export functionality
- Setup wizard for content migration

---

**Maintainer**: richardevcom  
**Repository**: wp-lovetravel-child  
**License**: To be determined