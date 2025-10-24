# Deprecated Files - October 25, 2025

**Reason**: Migration to new folder structure following WordPress Plugin Boilerplate pattern + Elementor best practices

**Migration**: All Elementor-related code moved from `includes/` to new `elementor/` top-level folder

---

## Files Moved to Backup

### Hook-Based Widget Extensions (deprecated)
These files will be replaced with standalone custom widgets in Phase 2:

- `class-lovetravel-child-elementor-packages-widget-extension.php`
  - **Old location**: `includes/`
  - **Pattern**: Hooked into nd-travel Packages widget via filters
  - **Replacement**: Standalone `elementor/widgets/class-packages-widget.php` (Phase 2)

- `class-lovetravel-child-elementor-search-widget-extension.php`
  - **Old location**: `includes/`
  - **Pattern**: Hooked into nd-travel Search widget via filters
  - **Replacement**: Standalone `elementor/widgets/class-search-widget.php` (Phase 2)

### Old Widgets Manager (deprecated structure)
- `class-lovetravel-child-elementor-widgets-manager.php`
  - **Old location**: `includes/`
  - **Replacement**: `elementor/class-lovetravel-child-elementor-manager.php`

---

## Files Migrated (not in backup)

### Moved to `elementor/widgets/`
- `includes/elementor-widgets/class-lovetravel-child-typology-card-widget.php` → `elementor/widgets/class-typology-card-widget.php`
- `includes/elementor-widgets/class-lovetravel-child-typology-cards-widget.php` → `elementor/widgets/class-typology-cards-widget.php`

### Moved to `elementor/metaboxes/`
- `includes/class-lovetravel-child-typology-card-metabox.php` → `elementor/metaboxes/class-typology-card-metabox.php`

### Moved to `elementor/templates/`
- `elementor-widgets/packages/layouts/layout-1.php` → `elementor/templates/packages/layout-1.php`

---

## Restoration (if needed)

To restore old structure:
1. Copy files back to original locations
2. Revert main class changes in `includes/class-lovetravel-child.php`
3. Clear cache: `wp cache flush`

**⚠️ Not recommended** - new structure follows industry best practices

---

## References

- Plugin Boilerplate: `/wp-content/plugins/plugin-boilerplate/`
- Elementor Docs: https://developers.elementor.com/
- Copilot Instructions: `.github/copilot-instructions.md` (FOLDER STRUCTURE RULES section)
