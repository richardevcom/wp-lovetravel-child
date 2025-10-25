# Deprecated Files - October 25, 2025

**Phase 1**: Migration to new folder structure (completed)  
**Phase 2**: Standalone widgets + Dynamic Tags implementation (completed)

**Reason**: Migration to WordPress Plugin Boilerplate pattern + Elementor best practices + standalone widgets

---

## Phase 1: Folder Structure Migration (October 25, 2025)

**Migration**: All Elementor-related code moved from `includes/` to new `elementor/` top-level folder

---

## Files Moved to Backup

### Hook-Based Widget Extensions ⚠️ **DEPRECATED in Phase 2**
These files were replaced with standalone custom widgets:

**1. Search Widget Extension** - ⚠️ DEPRECATED (Phase 2)
- **File**: `class-lovetravel-child-elementor-search-widget-extension.php`
- **Old location**: `includes/`
- **Pattern**: Hooked into nd-travel Search widget via `elementor/element` and `elementor/widget/render_content` filters
- **Replacement**: `elementor/widgets/class-search-widget.php` (standalone) ✅
- **Status**: Hooks commented out in main class (October 25, 2025)
- **Removal plan**: Delete after verifying no pages use nd-travel Search widget with Month extension

**2. Packages Widget Extension** - ⚠️ DEPRECATED (Phase 2)
- **File**: `class-lovetravel-child-elementor-packages-widget-extension.php`
- **Old location**: `includes/`
- **Pattern**: Hooked into nd-travel Packages widget via `elementor/frontend/widget/should_render` and `elementor/widget/render_content` filters
- **Replacement**: `elementor/widgets/class-packages-widget.php` (standalone) ✅
- **Status**: Hooks commented out in main class (October 25, 2025)
- **Removal plan**: Delete after verifying no pages use nd-travel Packages widget with custom layout

### Old Widgets Manager (deprecated structure)
- **File**: `class-lovetravel-child-elementor-widgets-manager.php`
- **Old location**: `includes/`
- **Replacement**: `elementor/class-lovetravel-child-elementor-manager.php` ✅
- **Changes**: Renamed + expanded with Dynamic Tags support

---

## Files Migrated (not in backup)

### Moved to `elementor/widgets/`
- `includes/elementor-widgets/class-lovetravel-child-typology-card-widget.php` → `elementor/widgets/class-typology-card-widget.php` ✅
- `includes/elementor-widgets/class-lovetravel-child-typology-cards-widget.php` → `elementor/widgets/class-typology-cards-widget.php` ✅

### Moved to `elementor/metaboxes/`
- `includes/class-lovetravel-child-typology-card-metabox.php` → `elementor/metaboxes/class-typology-card-metabox.php` ✅

### Moved to `elementor/templates/`
- `elementor-widgets/packages/layouts/layout-1.php` → `elementor/templates/packages/layout-1.php` ✅

---

## Phase 2: Standalone Widgets (October 25, 2025)

**NEW FILES CREATED**:

### Dynamic Tags System
- `elementor/post-meta/class-post-meta-manager.php` - Registers post meta with REST API
- `elementor/dynamic-tags/class-dynamic-tags-manager.php` - Manages Dynamic Tags registration
- `elementor/dynamic-tags/class-image-dynamic-tag.php` - Image Dynamic Tag for WordPress ↔ Elementor sync

### Standalone Widgets
- `elementor/widgets/class-search-widget.php` - Adventure Search widget (replaces Search extension)
- `elementor/widgets/class-packages-widget.php` - Adventure Packages widget (replaces Packages extension)

**CHANGES**:
- `elementor/class-lovetravel-child-elementor-manager.php` - Added Dynamic Tags + new widgets
- `includes/class-lovetravel-child.php` - Commented out legacy extension hooks

---

## Deprecation Details

### Why Hook-Based Extensions Were Deprecated

**Search Extension Problems**:
- Fragile dependency on nd-travel widget internal structure
- Hook injection breaks if plugin changes section names
- Regex-based render modification (brittle, hard to maintain)

**Packages Extension Problems**:
- Complex render interception logic
- Global setting dependency
- Fragile hook-based pattern

**Standalone Widget Benefits**:
- Self-contained (no external dependencies)
- Full control over markup and behavior
- Easier to maintain and test
- No breaking when parent plugin updates

### Backward Compatibility

**✅ No breaking changes**:
- Old pages using nd-travel Search widget: Continue working (no Month field)
- Old pages using nd-travel Packages widget: Continue working (default layout)
- New pages: Use standalone widgets ("Adventure Search", "Adventure Packages")

---

## Restoration (if needed)

To restore old structure:
1. Uncomment legacy extension hooks in `includes/class-lovetravel-child.php`
2. Copy files back to `includes/`:
   ```bash
   cp backup/deprecated-2025-10-25/class-lovetravel-child-elementor-search-widget-extension.php includes/
   cp backup/deprecated-2025-10-25/class-lovetravel-child-elementor-packages-widget-extension.php includes/
   ```
3. Clear cache: `wp cache flush`

**⚠️ Not recommended** - new structure follows industry best practices

---

## Final Removal Checklist

**Before deleting this backup folder**:
- [ ] Audit all Elementor pages
- [ ] Replace nd-travel Search widgets with "Adventure Search"
- [ ] Replace nd-travel Packages widgets with "Adventure Packages"
- [ ] Test all replaced widgets (frontend + editor)
- [ ] Verify debug.log has no errors
- [ ] Git commit all changes
- [ ] Delete this folder

---

## References

- Plugin Boilerplate: `/wp-content/plugins/plugin-boilerplate/`
- Elementor Docs: https://developers.elementor.com/
- Copilot Instructions: `.github/copilot-instructions.md` (FOLDER STRUCTURE RULES section)
- Phase 2 Commit: [pending]

---

**Last Updated**: October 25, 2025 (Phase 2 complete)