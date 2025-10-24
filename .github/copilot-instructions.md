# GitHub Copilot Agent Instructions — TribeTravel WordPress Child Theme

**Last Updated**: October 25, 2025  
**WordPress Version**: 6.8.3  
**Scope**: `/home/richardevcom/dev/tribetravel.eu/wp-content/themes/lovetravel-child` (child theme ONLY)

---

## MANDATORY CONTEXT

**Workspace root**: `/home/richardevcom/dev/tribetravel.eu`

**EDITABLE (child theme only)**:
- `/home/richardevcom/dev/tribetravel.eu/wp-content/themes/lovetravel-child`

**BACKUP FOLDER RULE**:
- Do NOT inspect or modify `/home/richardevcom/dev/tribetravel.eu/wp-content/themes/lovetravel-child-backup` unless the user explicitly asks for audit/restore of that backup. This folder is a read-only historical snapshot and should only be referenced on request.

**READ-ONLY (reference but never edit)**:
- Parent theme: `/home/richardevcom/dev/tribetravel.eu/wp-content/themes/lovetravel`
- Core plugins: `/home/richardevcom/dev/tribetravel.eu/wp-content/plugins/nd-travel`, `nd-elements`, `nd-shortcodes`
- Other plugins: `elementor-pro`, `contact-form-7`, `mailchimp`
- **WordPress core reference**: `/home/richardevcom/dev/wordpress` (latest WordPress source for reference)

**Debug log**: `/home/richardevcom/dev/tribetravel.eu/wp-content/debug.log`

**Docker environment**: Containers run from workspace root; use WP-CLI inside container for site-level tasks.

---

## PRIMARY OBJECTIVES

1. Learn and audit child theme codebase plus parent/plugin references
2. Produce safe, maintainable changes ONLY inside child theme
3. Update this file and README.md (never create separate report files)
4. Propose small, tested, incremental improvements after user confirmation

---

## HARD RULES (never break)

1. **Never edit anything outside `lovetravel-child`** except this file or README.md
2. **Never invent** WordPress functions, hooks, plugin internals, or credentials — use `#vscode-websearchforcopilot_webSearch` and cite sources
3. **Do not create report files** — update `copilot-instructions.md` or `README.md` only
4. **Keep chat output minimal** — return only what explicitly requested (code diffs, short summaries, questions)
5. **Always ask clarifying questions** before design/feature decisions you cannot verify

---

## ANTI-HALLUCINATION RULE (mandatory)

**ALWAYS use `#vscode-websearchforcopilot_webSearch`** for:
- Any WordPress function, hook, or API behavior
- Plugin internals or compatibility with WP 6.8.3
- When uncertain about implementation approach

**Research hierarchy**:
1. Search codebase first
2. Use `#vscode-websearchforcopilot_webSearch` on developer.wordpress.org or official plugin docs
3. Community sources (cite URL and date)

**Citation format**: Include source URL in plan before implementing.

---

## SCOPE & EDITING RESTRICTIONS

**ONLY edit files in**: `/home/richardevcom/dev/tribetravel.eu/wp-content/themes/lovetravel-child`

**Never edit**:
- Parent theme files
- Plugin files (nd-travel, nd-elements, nd-shortcodes, elementor-pro, etc.)
- WordPress core files
- Files outside child theme (except this instructions file and README.md)

**When you must override parent template**:
- Copy minimal file to child theme
- Add file header comment explaining reason and date

---

## CODE STYLE & STANDARDS

**WordPress Coding Standards (WPCS)**:
- Tabs for indentation (not spaces)
- Snake_case for functions, PascalCase for classes
- Use `load_child_theme_textdomain('lovetravel-child')` for i18n
- Enqueue parent assets first, child assets after with proper dependencies
- Use `add_action`/`add_filter` instead of editing parent/plugin files

**PHPDoc blocks**:
```php
/**
 * Short one-line description.
 *
 * @param type $var Description.
 * @return type Description.
 */
```

**Avoid commenting self-explanatory code** — add comments only where necessary, keep short and clean.

**JavaScript**:
- Use ES modules where possible
- Avoid global pollution
- Modular structure in `assets/js/`

---

## SECURITY CHECKLIST (enforce always)

- Nonces + capability checks for state changes
- `wp_verify_nonce()` / `check_ajax_referer()` for AJAX
- `current_user_can()` checks
- `sanitize_*()` on all inputs
- `esc_*()` on all outputs
- `wpdb->prepare()` for raw SQL

---

## WORDPRESS ADMIN UI/UX STANDARDS (mandatory)

**Always use WordPress native admin UI patterns** — never invent custom admin interfaces.

### Core Principles

1. **Use ONLY WordPress default styles, layout, and components**
2. **Reference local WordPress core** at `/home/richardevcom/dev/wordpress` for markup/CSS examples
3. **If component doesn't exist** — use `#vscode-websearchforcopilot_webSearch` to find solutions, then adapt to WordPress styling

### Required Admin UI Elements

**Page wrapper structure**:
```php
<div class="wrap">
    <h1 class="wp-heading-inline">Page Title</h1>
    <?php settings_errors(); ?>
    <!-- Content here -->
</div>
```

**Essential CSS classes**:
- `.wrap` — Main admin page wrapper
- `.button`, `.button-primary`, `.button-secondary` — Standard buttons
- `.notice`, `.notice-success`, `.notice-error`, `.notice-warning`, `.notice-info` — Admin notices
- `.form-table` — Settings form tables
- `.wp-list-table`, `.widefat` — Data tables
- `.postbox`, `.hndle`, `.inside` — Meta boxes

**Typography & Forms**:
- Use semantic headings: `<h1>` for page title, `<h2>` for sections, `<h3>` for meta boxes
- Form inputs: `.regular-text`, `.small-text`, `.widefat`
- Always use `<label for="id">` with form elements
- Helper text: `<p class="description">`

**Dashicons for icons**:
```php
<span class="dashicons dashicons-admin-generic"></span>
```

### Research Workflow for Admin UI

When building admin interfaces:

1. **Check WordPress core reference** at `/home/richardevcom/dev/wordpress/wp-admin/` for examples
2. **Search codebase** for similar existing admin pages
3. **If no local example** — use `#vscode-websearchforcopilot_webSearch` for:
   - WordPress Developer Handbook
   - WordPress Codex admin UI patterns
   - Popular plugins (WooCommerce, Elementor) for complex UI patterns
4. **Adapt found solutions** to match WordPress default styling (colors, spacing, components)

### Forbidden Practices

- ❌ Custom CSS frameworks (Bootstrap, Tailwind, etc.) in admin
- ❌ Non-standard UI components or interaction models
- ❌ Inline styles (use WordPress admin CSS classes)
- ❌ Custom JavaScript UI libraries (use WordPress native or jQuery UI)

### Verification

For each admin interface, confirm:
- [ ] Uses `.wrap` container
- [ ] Uses WordPress core button/notice/form classes
- [ ] Follows Settings API for option pages
- [ ] Matches WordPress admin color scheme
- [ ] Works with all WordPress admin color schemes (Default, Light, Blue, etc.)
- [ ] Responsive (uses WordPress breakpoints)
- [ ] Accessible (ARIA labels, keyboard navigation, screen reader text)

---

## SECURITY CHECKLIST (enforce always)

**For each change**:
1. Test in Docker environment
2. Check admin pages involved
3. Review `debug.log` for PHP errors
4. Check browser console for JS errors

**Provide verification checklist** (3–6 steps) with each deliverable.

**WP-CLI examples**:
```bash
docker compose exec wordpress wp cache flush
docker compose exec wordpress wp theme list
```

**Do not run DB-destructive commands** without explicit approval.

---

## COMMUNICATION RULES

**Ask specific questions only** — no broad/open questions.

Example: "Should I remove `test-import-state.php` now, or move it to `lovetravel-child/dev/` for later removal?"

**When research needed**: "I will run `#vscode-websearchforcopilot_webSearch` on [query]. Proceed?" — then execute after confirmation.

---

## DELIVERABLES FORMAT

**For each task provide**:
1. Short plan (2–4 bullets)
2. Minimal code diff or single-file edits (no full-file dumps unless requested)
3. Short verification checklist (3–6 steps)
4. If refactor: list impacted areas and migration notes

---

## COMMIT MESSAGE FORMAT

```
type(scope): short summary — emoji

Examples:
fix(import): handle null index when parsing API response 🐛
chore(theme): tidy imports and add sanitization helper ✨
feat(wizard): add progress indicator for media imports ⚡
```

---

## FILE ORGANIZATION (WordPress standards)

**APPROVED STRUCTURE** (do not refactor without approval):

```
lovetravel-child/
├── functions.php                           # Bootstrap only (instantiate main class)
├── style.css                               # Theme header + CSS variables
├── screenshot.jpg
├── README.md
├── CHANGELOG.md
├── TODO.md
│
├── assets/                                 # SHARED assets (used by both admin & public)
│   ├── css/
│   │   └── variables.css                  # CSS custom properties
│   ├── js/
│   │   └── common.js                      # Shared utilities
│   ├── images/
│   └── fonts/
│
├── admin/                                  # ADMIN-ONLY functionality
│   ├── class-lovetravel-child-admin.php   # Admin hooks, enqueue, pages
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin.css                  # Admin-specific styles
│   │   └── js/
│   │       └── admin.js                   # Admin-specific scripts
│   └── partials/                          # Admin view templates
│       └── settings-page.php              # Settings page HTML
│
├── public/                                 # PUBLIC-ONLY functionality
│   ├── class-lovetravel-child-public.php  # Frontend hooks, enqueue
│   ├── assets/
│   │   ├── css/
│   │   │   └── public.css                 # Frontend-specific styles
│   │   └── js/
│   │       └── public.js                  # Frontend-specific scripts
│   └── partials/                          # Frontend view templates
│
├── includes/                               # SHARED utilities & core (NOT Elementor-specific)
│   ├── class-lovetravel-child.php         # Main theme class (core orchestrator)
│   ├── class-lovetravel-child-loader.php  # Hook loader with priority control
│   ├── class-lovetravel-child-i18n.php    # Internationalization
│   ├── class-lovetravel-child-taxonomy-manager.php  # Taxonomy registration & management
│   ├── class-lovetravel-child-favicon.php # Favicon output manager
│   ├── helpers.php                        # Helper/utility functions
│   └── favicon-helpers.php                # Favicon standalone functions
│
├── elementor/                              # ALL Elementor-related code (MANDATORY)
│   ├── class-lovetravel-child-elementor-manager.php  # Elementor main loader/orchestrator
│   │
│   ├── widgets/                           # Custom Elementor widgets
│   │   ├── class-typology-card-widget.php
│   │   ├── class-typology-cards-widget.php
│   │   ├── class-packages-widget.php      # Standalone (replaces hook-based extension)
│   │   └── class-search-widget.php        # Standalone (replaces hook-based extension)
│   │
│   ├── dynamic-tags/                      # Elementor Dynamic Tags for WordPress ↔ Elementor sync
│   │   ├── class-dynamic-tags-manager.php
│   │   ├── class-text-dynamic-tag.php
│   │   ├── class-image-dynamic-tag.php
│   │   ├── class-color-dynamic-tag.php
│   │   └── class-url-dynamic-tag.php
│   │
│   ├── post-meta/                         # Post meta registration (for Dynamic Tags)
│   │   └── class-post-meta-manager.php
│   │
│   ├── metaboxes/                         # WordPress metaboxes (admin UI for post data)
│   │   └── class-typology-card-metabox.php
│   │
│   ├── templates/                         # Widget layout PHP templates
│   │   ├── packages/
│   │   │   ├── layout-1.php
│   │   │   └── layout-2.php
│   │   └── search/
│   │       └── layout-1.php
│   │
│   └── library/                           # Imported JSON templates (auto-import on activation)
│       ├── sections/
│       │   ├── 01-hero-slider.json
│       │   └── 02-search-form.json
│       └── pages/
│           └── 01-homepage.json
│
├── languages/
│   └── lovetravel-child.pot
│
└── .github/
    └── copilot-instructions.md            # This file
```

---

## FOLDER STRUCTURE RULES (MANDATORY — never violate)

### **Core Principles**

**Based on**:
- WordPress Plugin Boilerplate (OOP architecture)
- WordPress Theme Developer Handbook (developer.wordpress.org)
- Elementor addon development best practices (developers.elementor.com)

**Sources**:
- Plugin Boilerplate: `/home/richardevcom/dev/tribetravel.eu/wp-content/plugins/plugin-boilerplate`
- Web research: Codeable.io, WP White Label, Elementor Developers Documentation (2025)

### **Strict Placement Rules**

#### ❌ **FORBIDDEN: Never place these in `includes/`**
- Elementor widgets
- Elementor Dynamic Tags
- Elementor-specific managers
- Post meta managers (if for Elementor sync)
- WordPress metaboxes that integrate with Elementor

**Reason**: `includes/` is for GLOBAL, non-Elementor utilities only (follows plugin boilerplate pattern)

#### ✅ **REQUIRED: All Elementor code goes in `elementor/`**

**Mandatory subdirectories**:

1. **`elementor/widgets/`** - Custom widget classes
   - Each widget = separate file
   - Class naming: `class-widget-name-widget.php`
   - Example: `class-typology-card-widget.php`

2. **`elementor/dynamic-tags/`** - Dynamic Tag classes for WordPress ↔ Elementor sync
   - Manager class + individual tag types
   - Example: `class-text-dynamic-tag.php`

3. **`elementor/post-meta/`** - Post meta registration
   - Registers meta with REST API (`show_in_rest: true`)
   - Used by Dynamic Tags for syncing

4. **`elementor/metaboxes/`** - WordPress admin metaboxes
   - Admin UI for post data that syncs to Elementor
   - Example: `class-typology-card-metabox.php`

5. **`elementor/templates/`** - Widget layout PHP files
   - Sub-folders per widget (e.g., `packages/`, `search/`)
   - Used by `render()` methods

6. **`elementor/library/`** - JSON template files
   - Auto-imported on theme activation
   - Organized: `sections/`, `pages/`

#### ✅ **ALLOWED: What goes in `includes/`**

ONLY global utilities NOT specific to Elementor:
- Main theme class (`class-lovetravel-child.php`)
- Loader class (`class-lovetravel-child-loader.php`)
- i18n class (`class-lovetravel-child-i18n.php`)
- Taxonomy manager (global taxonomies)
- Favicon manager (Customizer integration)
- Helper functions (global utilities)

#### ✅ **REQUIRED: Admin/Public separation maintained**

- `admin/` - Admin-only hooks, assets, partials
- `public/` - Frontend-only hooks, assets, partials
- `assets/` - Shared assets (both admin + public)

### **File Naming Conventions**

**Classes**:
- Format: `class-name-with-hyphens.php`
- Examples:
  - `class-lovetravel-child-elementor-manager.php`
  - `class-typology-card-widget.php`
  - `class-dynamic-tags-manager.php`

**Class Names (inside files)**:
- Format: `LoveTravelChild_Name_With_Underscores`
- Examples:
  - `LoveTravelChild_Elementor_Manager`
  - `LoveTravelChild_Typology_Card_Widget`

**Functions**:
- Format: `lovetravelChild_name_with_underscores()`
- Example: `lovetravelChild_enqueue_elementor_assets()`

**Hooks**:
- Format: `lovetravel_child/hook_name`
- Example: `lovetravel_child/elementor/widgets_registered`

**Text Domain**: `lovetravel-child` (kebab-case)

### **Architecture Pattern**

**Inspired by WordPress Plugin Boilerplate**:

1. **Bootstrap file** (`functions.php`):
   - Load main class
   - Instantiate and run: `$theme = new LoveTravelChild(); $theme->run();`

2. **Main orchestrator** (`includes/class-lovetravel-child.php`):
   - `load_dependencies()` - Require all classes
   - `set_locale()` - i18n setup
   - `define_admin_hooks()` - Admin functionality
   - `define_public_hooks()` - Frontend functionality
   - `define_elementor_hooks()` - Elementor functionality
   - `run()` - Execute loader

3. **Loader class** (`includes/class-lovetravel-child-loader.php`):
   - `add_action($hook, $component, $callback, $priority, $accepted_args)`
   - `add_filter($hook, $component, $callback, $priority, $accepted_args)`
   - `run()` - Register all hooks with WordPress

4. **Specialized managers**:
   - Admin class (`admin/class-lovetravel-child-admin.php`)
   - Public class (`public/class-lovetravel-child-public.php`)
   - Elementor manager (`elementor/class-lovetravel-child-elementor-manager.php`)

5. **Hook registration via Loader**:
   ```php
   // In main class:
   $elementor_manager = new LoveTravelChild_Elementor_Manager();
   $this->loader->add_action('elementor/widgets/register', $elementor_manager, 'register_widgets');
   ```

### **Asset Loading Priorities**

**Critical for child theme override**:
- Parent theme (lovetravel): priority `10` (default)
- Core plugins (nd-travel, nd-elements, nd-shortcodes): priority `10-15`
- **Child theme: priority `20`** (ensures our styles/scripts always override)

**Enqueue pattern**:
```php
// In Admin/Public/Elementor classes:
public function enqueue_styles() {
	wp_enqueue_style(
		$this->plugin_name . '-admin',
		plugin_dir_url(__FILE__) . 'assets/css/admin.css',
		array(), // Dependencies
		$this->version,
		'all'
	);
}
```

### **Migration from Old Structure**

**OLD (deprecated)**:
```
includes/
├── class-lovetravel-child-elementor-widgets-manager.php  ❌
├── class-lovetravel-child-elementor-search-widget-extension.php  ❌
└── elementor-widgets/  ❌
    ├── class-lovetravel-child-typology-card-widget.php  ❌
    └── packages/
        └── layouts/
            └── layout-1.php  ❌
```

**NEW (required)**:
```
elementor/
├── class-lovetravel-child-elementor-manager.php  ✅
├── widgets/
│   ├── class-typology-card-widget.php  ✅
│   └── class-search-widget.php  ✅ (standalone, not hook-based extension)
└── templates/
    └── packages/
        └── layout-1.php  ✅
```

**Backup rule**:
- Before migration: Move old files to `./backup/deprecated-[date]/`
- Never delete without user approval
- Document what was moved in backup folder's README.md

---

## NAMING CONVENTIONS (consolidated)

**Classes**:
- File: `class-name-with-hyphens.php`
- Class: `LoveTravelChild_Name_With_Underscores`
- Example: `class-elementor-manager.php` → `LoveTravelChild_Elementor_Manager`

**Functions**:
- Format: `lovetravelChild_function_name()`
- Example: `lovetravelChild_register_dynamic_tags()`

**Hooks**:
- Format: `lovetravel_child/context/action`
- Example: `lovetravel_child/elementor/widgets_loaded`

**Text Domain**: `lovetravel-child` (all i18n functions)

**Constants**:
- Format: `LOVETRAVEL_CHILD_CONSTANT_NAME`
- Example: `LOVETRAVEL_CHILD_VERSION`

---

## CURRENT PROJECT STATE

WordPress child theme extending LoveTravel parent theme with:
- **Version**: 2.0.0
- **Base Architecture**: OOP structure (plugin-boilerplate pattern)
- **Clean slate**: Previous temporary tools removed
- **Admin Settings**: Settings page under Appearance menu with template management
- **i18n Ready**: Full translation support

**Development Status**:
- ✅ Core OOP structure established (v2.0.0 clean rebuild)
- ✅ Asset loading with correct priorities
- ✅ Admin/Public separation
- ✅ Favicon system with Customizer integration
- ✅ Taxonomy Manager class (modular, reusable)
- ✅ Month taxonomy registered with all 12 months
- ✅ Elementor Search widget extension (Month taxonomy integrated)
- ✅ **Admin Notices framework (OOP, reusable)** ✅
- ✅ **Elementor Template Importer (auto-import, dependency validation)** ✅
- ⏳ CPT terminology overrides (to be added - currently NOT implemented in v2.0.0)
- ⏳ Feature integrations (on-demand basis)

**Known nd-travel Plugin Taxonomies**:
- `nd_travel_cpt_1` - Main CPT (labeled "Packages" in plugin, should be "Adventures")
- `nd_travel_cpt_1_tax_1` - Durations taxonomy (child theme overrides labels)
- `nd_travel_cpt_1_tax_2` - Difficulty taxonomy (child theme overrides labels)
- `nd_travel_cpt_1_tax_3` - Min Age taxonomy (child theme overrides labels)
- `nd_travel_cpt_1_tax_4` - **Month taxonomy (NEW - registered by child theme, integrated into Elementor Search widget)** ✅

**Elementor Integration**:
- Search widget extended via `elementor/element/Search/content_section_minages/after_section_end` hook
- Month taxonomy controls injected (show/hide toggle, label, icon)
- Widget render filter modifies output to include Month dropdown
- Pattern: LoveTravelChildElementorSearchWidgetExtension class handles all Search widget modifications
- **Template management system:**
  - Auto-imports JSON templates from `/elementor-templates/` on theme activation
  - Validates dependencies (parent theme, Elementor, Elementor Pro, nd-travel)
  - Tracks imports to avoid duplicates
  - Admin UI shows import status table
  - Admin notices for import success/errors

---

## ELEMENTOR TEMPLATE MANAGEMENT (mandatory workflow)

**Directory Structure**:
```
/elementor-templates/
├── sections/          # Reusable sections (hero sliders, search forms, CTAs)
└── pages/             # Full page layouts
```

**Adding New Templates**:
1. Export from Elementor UI (Tools → Export Template)
2. Rename: `##-descriptive-name.json` (e.g., `01-hero-slider.json`, `02-search-form.json`)
3. Place in correct directory (sections/ or pages/)
4. Commit to Git
5. Auto-imports on theme activation

**Naming Rules**:
- ✅ `01-hero-slider.json` (numeric prefix + hyphens)
- ❌ `hero-slider.json` (missing number)
- ❌ `1-hero.json` (use two digits: 01, 02)
- ❌ `01_hero_slider.json` (use hyphens, not underscores)

**Classes**:
- `LoveTravelChildElementorTemplateImporter` — Import logic, dependency checks
- `LoveTravelChildAdminNotices` — Reusable notice system (dismissible, conditional)

**Dependency Checks**:
- Parent theme: `wp_get_theme()->parent()->get('TextDomain')` === 'lovetravel'
- Elementor: `did_action('elementor/loaded')`
- Elementor Pro: `function_exists('elementor_pro_load_plugin')`
- ND Travel: `function_exists('nd_travel_scripts')` OR `post_type_exists('nd_travel_cpt_1')`

**Hooks**:
- `after_switch_theme` — Auto-import templates
- `admin_notices` — Display import status

**Admin UI**:
- Location: **Appearance → Child Theme → Elementor Templates**
- Shows: Import status table, dependency warnings, Elementor link
- Does NOT allow: Manual re-import (must reactivate theme)

**Documentation**:
- Full workflow: `/elementor-templates/README.md`
- User guide includes: export, naming, commit, verify

## FILE ORGANIZATION (WordPress standards)

## INITIAL AUDIT TASKS

1. **Find temp/test files** in theme root — propose move to `dev/` or delete
2. **Identify duplicate logic** — propose refactor into reusable functions
3. **Verify i18n** — ensure all strings use `lovetravel-child` text domain
4. **Security audit** — check for unsanitized inputs, missing nonces
5. **Check debug.log** — flag any child theme errors

---

## SUCCESS CRITERIA

- Clean working tree post-commit
- All changes tested in Docker environment
- No PHP/JS errors in logs/console
- WordPress standards compliance
- Minimal chat output (no unnecessary commentary)
- Evidence-based decisions (cite sources when using `#vscode-websearchforcopilot_webSearch`)

---

**END OF INSTRUCTIONS**