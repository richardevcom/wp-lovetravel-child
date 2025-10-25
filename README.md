# LoveTravel Child Theme v2.2.0

[![WordPress](https://img.shields.io/badge/WordPress-6.8+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net/)
[![Version](https://img.shields.io/badge/Version-2.2.0-green.svg)]()

A professional WordPress child theme extending the LoveTravel parent theme with custom Elementor widgets, Dynamic Tags, and enhanced travel functionality for tribetravel.eu.

## 🎯 Overview

This child theme provides **standalone custom Elementor widgets** that replace and enhance the parent theme's functionality. Built using **WordPress Plugin Boilerplate architecture** for maintainability and scalability.

### Key Features

- ✅ **Standalone Elementor Widgets** (no dependencies on parent widgets)
- ✅ **Dynamic Tags System** for WordPress ↔ Elementor sync
- ✅ **AJAX Load More** pagination with masonry support
- ✅ **Month Taxonomy** integration for travel seasons
- ✅ **Admin Settings Panel** for template management
- ✅ **Auto-import Elementor Templates** on theme activation

---

## 📁 Project Structure

### Architecture Pattern: WordPress Plugin Boilerplate

```
lovetravel-child/
├── functions.php                    # Bootstrap (loads main class)
├── style.css                        # Theme header + CSS variables
├── includes/                        # Core functionality
│   ├── class-lovetravel-child.php   # Main orchestrator class
│   ├── class-lovetravel-child-loader.php  # Hook manager
│   ├── class-lovetravel-child-i18n.php    # Internationalization
│   ├── class-lovetravel-child-taxonomy-manager.php  # Custom taxonomies
│   └── helpers.php                  # Utility functions
├── admin/                           # Admin-only functionality
│   ├── class-lovetravel-child-admin.php           # Admin hooks/pages
│   ├── partials/settings-page.php  # Settings page template
│   └── assets/                      # Admin CSS/JS
├── public/                          # Frontend-only functionality
│   ├── class-lovetravel-child-public.php          # Frontend hooks
│   └── assets/                      # Frontend CSS/JS
├── elementor/                       # Elementor integration (CORE)
│   ├── class-lovetravel-child-elementor-manager.php  # Elementor orchestrator
│   ├── widgets/                     # Custom Elementor widgets
│   ├── dynamic-tags/                # Dynamic Tags for WP ↔ Elementor sync
│   ├── post-meta/                   # Post meta registration
│   ├── metaboxes/                   # WordPress admin metaboxes
│   ├── templates/                   # Widget layout templates
│   └── library/                     # JSON templates (auto-import)
└── assets/                          # Shared assets (admin + public)
    ├── css/variables.css            # CSS custom properties
    ├── js/common.js                 # Shared JavaScript
    └── favicon/                     # Favicon files
```

---

## 🧩 Custom Elementor Widgets

### Standalone Widgets (No Parent Dependencies)

| Widget | Purpose | Key Features |
|--------|---------|-------------|
| **Adventure Search** | Travel search form | 8 content sections, 6 style sections, Month taxonomy |
| **Adventure Packages** | Package listings | Load More pagination, masonry grid, nd-travel 1:1 controls |
| **Typology Card** | Single adventure type card | Dynamic Tags integration, custom meta fields |
| **Typology Cards** | Grid of adventure types | Responsive grid, custom styling options |

### Advanced Features

#### AJAX Load More (Packages Widget)
- **Nonce Security**: `lovetravel_load_more_nonce` verification
- **Masonry Re-init**: Proper grid layout after loading
- **Editor Support**: Context detection for Elementor preview
- **Error Handling**: User-friendly error messages

#### Dynamic Tags System
- **WordPress ↔ Elementor Sync**: Edit in WordPress admin OR Elementor
- **REST API Integration**: `show_in_rest: true` for modern compatibility
- **Tag Types**: Text, Image, Color, URL
- **Use Case**: Typology card icons, colors, descriptions

---

## 🛠️ Installation & Setup

### Prerequisites

- WordPress 6.8+
- PHP 8.2+
- **LoveTravel parent theme** (active)
- **Elementor** + **Elementor Pro**
- **nd-travel plugin** (for travel functionality)

### Installation

1. **Upload child theme** to `/wp-content/themes/lovetravel-child/`
2. **Activate child theme** in WordPress admin
3. **Auto-import runs**: Elementor templates imported automatically
4. **Check import status**: Go to **Appearance → Child Theme**

### First Use

1. **Add widgets**: Go to Elementor editor, find "LoveTravel Child" category
2. **Replace old widgets**: Replace any nd-travel widgets with child theme versions
3. **Configure settings**: Visit **Appearance → Child Theme** for options
4. **Test Load More**: Add Packages widget, enable Load More, test functionality

---

## 📋 Usage Guide

### Adding Widgets in Elementor

1. **Open Elementor editor** on any page/post
2. **Find category**: Look for "LoveTravel Child" in widget panel
3. **Drag & drop**: Add widgets to your layout
4. **Configure**: Use controls panel to customize

### Widget-Specific Guides

#### Adventure Search Widget
- **Content Controls**: 8 sections (Main Options, Keyword, Destinations, etc.)
- **Style Controls**: 6 sections (Content, Label, Fields, Submit, Columns, Icons)
- **Month Integration**: Automatically shows months if Month taxonomy has terms

#### Adventure Packages Widget
- **Layout Options**: 1-4 columns, various layouts
- **Load More**: Enable via switcher, customize button text and posts per load
- **Filtering**: By destination, typology, or specific package IDs

#### Typology Widgets
- **Meta Integration**: Use WordPress metaboxes OR Dynamic Tags
- **Card Settings**: Icon URL, color, description (syncs between WP and Elementor)

### Admin Settings

**Location**: Appearance → Child Theme

- **Template Status**: View Elementor template import status
- **Dependencies**: Check if required plugins are active
- **Documentation**: Links to Elementor Library

---

## 🎨 Customization

### CSS Variables

**File**: `assets/css/variables.css`

```css
:root {
    --primary-color: #EA5B10;    /* Adventure Orange */
    --secondary-color: #2E5BBA;  /* Adventure Blue */
    --accent-color: #28A745;     /* Success Green */
    /* Add custom properties here */
}
```

### Adding Custom Widgets

1. **Create widget class** in `elementor/widgets/`
2. **Follow naming**: `class-widget-name-widget.php`
3. **Register in manager**: Add to `register_widgets()` method
4. **Create templates**: Add layout files in `elementor/templates/`

### Extending Dynamic Tags

1. **Create tag class** in `elementor/dynamic-tags/`
2. **Register meta field** in `post-meta/class-post-meta-manager.php`
3. **Add to tags manager**: Register in `register_tags()` method

---

## 🧪 Development

### Local Development Setup

```bash
# Clone repository
git clone [repository-url] lovetravel-child
cd lovetravel-child

# Activate theme (via WordPress admin)
# Check debug.log for errors
tail -f /path/to/wp-content/debug.log
```

### Debugging

1. **Enable WordPress debugging** in `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

2. **Check logs**: Monitor `/wp-content/debug.log`

3. **Test widgets**: Use Elementor editor preview and frontend

4. **AJAX testing**: Check browser Network tab for Load More requests

### Code Standards

- **WordPress Coding Standards** (WPCS)
- **Plugin Boilerplate architecture**
- **Security**: Nonce verification, sanitization, escaping
- **Accessibility**: ARIA labels, keyboard navigation

---

## 📚 Changelog

### v2.2.0 (October 25, 2025) - Current

#### ✨ New Features
- **Load More pagination** for Packages widget with AJAX
- **Masonry re-initialization** for proper grid layouts
- **Editor context detection** for Elementor preview

#### 🐛 Bug Fixes
- Fixed grid layout in Elementor editor
- Fixed modal rendering in editor preview
- Removed gray background overlay on widgets

#### � Improvements
- Complete legacy code cleanup
- Updated documentation
- Performance optimizations

### v2.1.0 (October 25, 2025)

#### 🏗️ Architecture
- **Folder structure migration** to plugin-boilerplate pattern
- **Dynamic Tags system** implementation
- **Post Meta Manager** with REST API integration

### v2.0.0 (October 25, 2025)

#### 🎯 Major Refactor
- **Standalone widgets** replacing hook-based extensions
- **Search widget** with complete style controls
- **Packages widget** with nd-travel 1:1 feature parity
- **OOP architecture** using WordPress Plugin Boilerplate

---

## 🆘 Support

### Common Issues

**Q: Widgets not appearing in Elementor**
A: Check if Elementor and parent theme are active. Visit Appearance → Child Theme to verify dependencies.

**Q: Load More not working**
A: Check browser console for JavaScript errors. Verify AJAX URL and nonce in Network tab.

**Q: Templates not imported**
A: Deactivate and reactivate the child theme to trigger auto-import.

### Getting Help

1. **Check debug.log** for PHP errors
2. **Test with default theme** to isolate issues
3. **Disable other plugins** to check for conflicts
4. **Review documentation** in `.github/copilot-instructions.md`

---

## 📄 License

This project follows the same license as the parent LoveTravel theme.

---

## 🔗 Links

- **WordPress Codex**: [Child Themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/)
- **Elementor Developers**: [Custom Widgets](https://developers.elementor.com/docs/widgets/)
- **Plugin Boilerplate**: [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate)

---

**Last Updated**: October 25, 2025  
**Version**: 2.2.0  
**Compatibility**: WordPress 6.8+, Elementor 3.0+

- **Bulk Media Import**: Import media files from Payload CMS to WordPress
- **Background Processing**: Non-blocking batch import with progress tracking
- **Smart Skip Logic**: Avoid duplicate imports with existing file detection
- **Thumbnail Generation**: Automatic WordPress thumbnail creation
- **State Management**: Reliable start/stop/resume functionality
- **Admin Interface**: User-friendly progress monitoring

##### Adventures Import

- **Content Migration**: Import adventure posts from Payload CMS
- **Field Mapping**: Automatic mapping of Payload fields to WordPress meta
- **Taxonomy Sync**: Duration, Difficulty, Month term management
- **Media Sideloading**: Automatic image import and attachment
- **Dry Run Mode**: Preview imports without making changes
- **Overwrite Control**: Choose to update existing or create new posts

### Developer Features

- **Modular Architecture**: Well-organized file structure with clear separation of concerns
- **Hook System**: Extensive WordPress hook integration for customization
- **Error Handling**: Comprehensive error logging and user feedback
- **Security**: Proper nonce verification and capability checks
- **Performance**: Conditional script loading and optimized queries

## 📁 File Structure

```
lovetravel-child/
├── README.md                    # Main documentation (this file)
├── CHANGELOG.md                 # Version history and changes
├── readme.txt                   # WordPress theme information
├── style.css                    # Theme header and base styles
├── functions.php                # Main theme bootstrap (modular loading)
├── .gitignore                   # Version control exclusions
├── assets/                      # Static assets (CSS, JS, images, fonts)
│   ├── css/
│   │   ├── custom.css          # Custom theme styles (auto-enqueued)
│   │   └── admin-tools.css     # Admin interface styling
│   ├── js/
│   │   ├── custom.js           # Frontend JavaScript (auto-enqueued)
│   │   ├── admin-mailchimp-export.js
│   │   └── admin-adventures-import.js
│   ├── images/                  # Theme images and assets
│   └── fonts/                   # Custom fonts
├── inc/                         # PHP includes (organized by functionality)
│   ├── setup/
│   │   └── theme-setup.php     # Core theme setup and enqueuing
│   ├── admin/
│   │   └── admin-utilities.php # Admin notices and helpers
│   ├── integrations/
│   │   ├── elementor-templates.php # Elementor template tools
│   │   ├── payload-media-import.php
│   │   ├── mailchimp-subscriber-export.php
│   │   ├── payload-adventures-import.php
│   │   └── payload-import-admin-page.php
│   ├── utilities/
│   │   ├── common-functions.php # Shared utility functions
│   │   └── cpt-overrides.php   # Post type customizations
│   └── templates/
│       └── elementor/          # Elementor template JSON files
│           ├── README.md
│           ├── include-exclude-info-section.json
│           ├── adventure-about-section.json
│           ├── adventure-description-section.json
│           └── adventure-day-plan-program-section.json
├── template-parts/              # PHP template parts for theme customization
│   └── content-adventure.php   # Adventure post content template
├── languages/                   # Translation files
│   └── lovetravel-child.pot    # Translation template
├── patterns/                    # Block patterns (future use)
├── styles/                      # Theme style variations (future use)
└── .github/
    └── copilot-instructions.md  # AI coding assistant guidelines
```

## 🚀 Installation

1. **Prerequisites**:

   - WordPress 6.0+
   - LoveTravel parent theme installed and activated
   - PHP 7.4+

2. **Install Child Theme**:

   ```bash
   cd wp-content/themes/
   git clone [repository-url] lovetravel-child
   ```

3. **Activate Theme**:

   - Go to WordPress Admin → Appearance → Themes
   - Activate "LoveTravel Child"

4. **Configure Permalinks**:
   - Go to Settings → Permalinks
   - Click "Save Changes" to flush rewrite rules

## ⚙️ Configuration

### Payload CMS Integration

Configure the Payload CMS endpoints in the respective tool files:

```php
// Default configuration
$payload_base_url = 'https://tribetravel.eu';
$mailing_endpoint = '/api/mailing';
$media_endpoint = '/api/media';
$adventures_endpoint = '/api/adventures';
```

### Elementor Templates

Import pre-built templates:

1. Navigate to **Templates → Saved Templates**
2. Click **Import Templates** (button appears at bottom of list)
3. Select JSON files from `elementor-templates/` folder
4. Templates will be available in Elementor Library

### Admin Tools Access

- **Mailchimp Export**: `Mailchimp for WP → Payload Export`
- **Media Import**: `Media → Payload Import`
- **Adventures Import**: `Adventures → Import from Payload`
- **Elementor Import**: `Templates → Saved Templates → Import Templates`

## 🛠️ Development

### Coding Standards

- Follows WordPress Coding Standards (WPCS)
- PHP 7.4+ compatible with modern PHP practices
- Extensive inline documentation with proper DocBlocks
- Comprehensive error handling and user feedback
- Security best practices (nonce verification, capability checks)
- Translation-ready with proper text domain usage

### Development Workflow

#### Git Workflow

1. **Sync**: `git fetch origin && git checkout main && git pull --ff-only`
2. **Branch**: `git checkout -b feat/<slug>` (kebab-case, descriptive)
3. **Develop**: Edit minimal files, keep functions modular
4. **Test**: Manual testing, lint PHP files
5. **Commit**: `feat(theme): <change summary>` or `fix(theme): <issue>`
6. **Push**: `git push -u origin feat/<slug>`
7. **PR**: Open pull request with clear description

#### Pre-Commit Checklist

- [ ] `git diff --name-only` shows minimal, relevant changes
- [ ] No debug code: `grep -R "var_dump\|console.log" . || true` (should be empty)
- [ ] PHP syntax: `php -l` on changed files
- [ ] i18n check: Proper use of `__()`, `_e()`, `esc_html__()` with `lovetravel-child` domain
- [ ] WordPress hooks properly documented and cited

#### Release Process

1. **Prepare**: Clean working tree, merged features into `main`
2. **Version**: Update version in `style.css` and `functions.php`
3. **Changelog**: Update `CHANGELOG.md` with new version
4. **Tag**: `git tag -a v<version> -m "release: v<version> <summary>"`
5. **Push**: `git push origin v<version>`

### Customization

- **Custom Functions**: Add to `functions.php` (marked section at bottom)
- **Custom Styles**: Place in `assets/css/custom.css` (auto-enqueued)
- **Custom JavaScript**: Add to `assets/js/custom.js` (auto-enqueued)
- **Hooks Available**: Use `lovetravel_child_` prefixed hooks for customization

### Translation

Text domain: `lovetravel-child`

Generate POT file:

```bash
wp i18n make-pot . languages/lovetravel-child.pot --domain=lovetravel-child
```

### Development History

**Key Milestones:**

- **Sept 2025**: Initial v1.0.0 release with complete feature set
- **Major Features Added**: Payload CMS integration, Elementor templates, admin tools
- **Architecture**: Modular design with organized file structure in `inc/` directory
- **Security**: Comprehensive nonce verification and capability checks implemented
- **Performance**: Background processing for resource-intensive operations

## 🔧 API Integration

### Payload CMS Endpoints

The theme integrates with several Payload CMS endpoints:

- **Mailing**: `/api/mailing` - Newsletter subscribers
- **Media**: `/api/media` - File uploads and assets
- **Adventures**: `/api/adventures` - Travel packages/adventures

### WordPress Integration

- **Custom Post Types**: Enhanced `nd_travel_cpt_1` (Adventures)
- **Taxonomies**: Duration, Difficulty, Month
- **Meta Fields**: Pricing, availability, booking details
- **Media Library**: Automatic Payload media synchronization

## 📊 Admin Tools Usage

### Mailchimp Subscriber Export

1. Navigate to **Mailchimp for WP → Payload Export**
2. View subscriber statistics and analytics
3. Configure export options:
   - Format: CSV (recommended) or JSON
   - Include unsubscribed users (optional)
   - Date range filtering (optional)
4. Click **Export Subscribers**
5. Download generated file for Mailchimp import

### Media Import from Payload

1. Go to **Media → Payload Import**
2. Review statistics (Payload files vs WordPress media)
3. Configure import settings:
   - Batch size (10-100 files)
   - Skip existing files
   - Generate thumbnails
4. Click **Start Import**
5. Monitor progress in real-time
6. Use **Stop** or **Reset** as needed

### Adventure Content Import

1. Navigate to **Adventures → Import from Payload**
2. View import statistics and progress
3. Configure import options:
   - Batch size and pagination
   - Overwrite existing posts
   - Dry run mode (preview only)
4. Start import process
5. Monitor field mapping and media sideloading

## 🎨 Styling & Theming

### CSS Variables

The theme defines custom CSS variables for consistent styling:

```css
:root {
  --accent-color: #ea5b10;
  --background-color: #ffffff;
  --text-primary: #000000;
  --text-secondary: #363635;
  --border-light: #e8e6e1;
}
```

### Responsive Design

- Mobile-first approach
- Flexible grid system
- Optimized for travel industry needs
- Accessibility considerations

## 🐛 Troubleshooting

### Common Issues

**Permalinks not working after activation:**

- Go to Settings → Permalinks and click "Save Changes"

**Import tools not appearing:**

- Ensure parent plugins are active (Mailchimp for WP, Elementor)
- Check user permissions (manage_options capability required)

**Payload API connection issues:**

- Verify API endpoints in configuration
- Check network connectivity and CORS settings
- Review WordPress error logs

### Debug Mode

Enable WordPress debug mode for detailed error information:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📖 Documentation

- **Main Documentation**: This README.md (comprehensive setup and usage guide)
- **Changelog**: See `CHANGELOG.md` for version history and changes
- **AI Assistant Guidelines**: See `.github/copilot-instructions.md` for development assistance
- **Elementor Templates**: See `elementor-templates/README.md` for template usage

## 🤝 Contributing

1. **Follow Development Workflow**: Use feature branches and semantic commits
2. **Code Quality**: Ensure WordPress coding standards compliance and proper testing
3. **Documentation**: Update README.md and CHANGELOG.md for significant changes
4. **Translation**: Use `lovetravel-child` text domain for all translatable strings
5. **Security**: Follow WordPress security best practices (nonces, capability checks)
6. **Performance**: Consider impact on site performance, use background processing for heavy operations

## 📄 License

This theme is licensed under the GNU General Public License v2 or later.

**Parent Theme**: LoveTravel (commercial theme)  
**Child Theme**: GPL v2+ (custom enhancements)

## 🆘 Support

For theme-specific issues:

- Check documentation in this repository
- Review WordPress error logs
- Ensure parent theme compatibility

For travel industry specific features:

- Payload CMS integration documentation
- Adventure post type customizations
- Booking and pricing meta field usage

---

**Version**: 1.0.0  
**Author**: richardevcom  
**Requires**: WordPress 6.0+, PHP 7.4+  
**Parent Theme**: LoveTravel  
**Text Domain**: lovetravel-child
