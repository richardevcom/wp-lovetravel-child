# LoveTravel Child Theme

[![WordPress](https://img.shields.io/badge/WordPress-6.4+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net/)

A professional WordPress child theme extending the LoveTravel theme with custom enhancements for tribetravel.eu. This theme provides advanced integration with Payload CMS, Elementor page builder, and specialized travel industry tools.

## 🌟 Features

### Core Functionality
- **WordPress Child Theme**: Proper inheritance from LoveTravel parent theme
- **Travel Industry Focus**: Custom post type overrides for "Adventures" (instead of generic "Travel")
- **Translation Ready**: Full i18n support with `lovetravel-child` text domain
- **Accessibility Ready**: Follows WordPress accessibility standards
- **Modern PHP**: Compatible with PHP 7.4+ and follows WordPress coding standards

### Content Management
- **Adventure Management**: Enhanced CPT labels and taxonomy overrides
  - Custom post type: `nd_travel_cpt_1` → "Adventures"
  - Taxonomy overrides: Duration, Difficulty, Month
  - Automatic permalink structure updates
- **Elementor Integration**: Pre-built templates and import utilities
- **Custom Meta Fields**: Adventure pricing, availability, and booking details

### Admin Tools & Integrations

#### 🎨 Elementor Templates
- **Pre-built Sections**: Ready-to-use adventure page sections
  - Include/Exclude info section
  - Adventure About section  
  - Adventure Description section
  - Day Plan/Program section (toggle itinerary)
- **Template Importer**: Easy import via WP Admin interface
- **Library Integration**: Seamless Elementor Library compatibility

#### 📧 Mailchimp Integration
- **Subscriber Export**: Export newsletter subscribers from Payload CMS
- **Mailchimp Compatible**: CSV format optimized for Mailchimp import
- **Advanced Filtering**: Date ranges, subscription status, batch processing
- **Real-time Statistics**: Live subscriber counts and analytics
- **AJAX Interface**: Smooth, non-blocking export process
- **Admin Menu**: Integrated under Mailchimp for WP plugin

#### 🔄 Payload CMS Sync Tools

##### Media Import
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
├── style.css                    # Theme header and base styles
├── functions.php                # Main theme bootstrap
├── assets/
│   ├── css/
│   │   ├── custom.css          # Custom theme styles
│   │   └── admin-tools.css     # Admin interface styling
│   └── js/
│       ├── custom.js           # Frontend JavaScript
│       ├── admin-mailchimp-export.js
│       └── admin-adventures-import.js
├── inc/
│   ├── includes/
│   │   ├── theme-setup.php     # Core theme setup
│   │   ├── admin-utilities.php # Admin helpers
│   │   └── elementor-templates.php # Elementor integration
│   ├── hooks/
│   │   └── cpt-overrides.php   # Post type customizations
│   └── tools/
│       ├── mailchimp-subscriber-export.php
│       ├── payload-media-import.php
│       ├── payload-adventures-import.php
│       └── payload-import-admin-page.php
├── elementor-templates/         # Pre-built Elementor sections
│   ├── README.md                # Template usage guide
│   ├── include-exclude-info-section.json
│   ├── adventure-about-section.json
│   ├── adventure-description-section.json
│   └── adventure-day-plan-program-section.json
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
