# Elementor Templates

This directory contains reusable Elementor templates stored as JSON files. Templates are automatically imported when the theme is activated and appear in **Elementor → My Templates**.

## Directory Structure

```
elementor-templates/
├── sections/          # Reusable sections (hero sliders, forms, CTAs, etc.)
│   └── 01-hero-slider.json
└── pages/             # Full page layouts
```

## Template Organization

### Sections (`/sections/`)
Components and sections that can be inserted into any page:
- Hero sliders
- Search forms
- Call-to-action sections
- Headers and footers
- Feature sections
- Testimonial carousels
- Contact forms

### Pages (`/pages/`)
Complete page layouts:
- Landing pages
- Archive templates
- Single post templates
- Custom page designs

## Naming Convention

Templates **must** follow this naming pattern:

```
##-descriptive-name.json
```

**Examples:**
- `01-hero-slider.json` ✅
- `02-adventure-search-form.json` ✅
- `03-cta-newsletter.json` ✅
- `hero-slider.json` ❌ (missing number)
- `01_hero_slider.json` ❌ (use hyphens, not underscores)

**Numbering:**
- Start from `01` (not `1` or `001`)
- Increment sequentially
- Numbers determine display order in admin UI

## Adding New Templates

### Step 1: Export from Elementor

1. Open template in Elementor editor
2. Click **Tools** (wrench icon) in bottom left
3. Navigate to **General Settings**
4. Click **Export Template** button
5. Save JSON file to your computer

### Step 2: Prepare Template File

1. Rename file following naming convention: `##-descriptive-name.json`
2. Move to appropriate directory:
   - Sections: `/elementor-templates/sections/`
   - Pages: `/elementor-templates/pages/`

### Step 3: Commit to Version Control

```bash
cd /home/richardevcom/dev/tribetravel.eu/wp-content/themes/lovetravel-child
git add elementor-templates/
git commit -m "feat(templates): add hero slider Elementor template ✨"
git push
```

### Step 4: Import Templates

Templates are **automatically imported** on theme activation. To manually trigger import:

1. Deactivate child theme
2. Reactivate child theme
3. Check **Appearance → Child Theme** for import status

**Or via WP-CLI:**
```bash
docker compose exec wordpress wp theme activate lovetravel
docker compose exec wordpress wp theme activate lovetravel-child
```

## Verifying Import

1. Navigate to **WordPress Admin → Elementor → My Templates**
2. Look for templates with names matching your JSON files
3. Templates can be inserted via Elementor's **Add Template** button

**Admin Status:**
- Go to **Appearance → Child Theme**
- Check **Elementor Templates** section
- Green checkmark (✓) = Imported successfully
- Warning icon = Not yet imported

## Template JSON Structure

### Minimal Section Template

```json
{
  "type": "elementor",
  "siteurl": "http://localhost:8080/wp-json/",
  "elements": [
    {
      "id": "unique-id",
      "elType": "section",
      "settings": { ... },
      "elements": [ ... ]
    }
  ]
}
```

### Image Handling

**Important:** Image URLs are **not** remapped automatically. Best practices:

1. **Use placeholder images** during development
2. **Reupload images** after production deployment
3. **Update image IDs** in Elementor editor after import

Example image reference in JSON:
```json
"background_image": {
  "id": 7480,
  "url": "http://localhost:8080/wp-content/uploads/2023/05/hero.jpeg"
}
```

After import, manually:
1. Edit template in Elementor
2. Replace images via Media Library
3. Save template

## Troubleshooting

### Templates Not Importing

**Check dependencies:**
- ✅ LoveTravel parent theme active
- ✅ Elementor plugin active
- ✅ Elementor Pro active
- ✅ ND Travel plugin active (Travel Management)

**View errors:**
1. Go to **Appearance → Child Theme**
2. Check admin notices for import errors
3. Review `/wp-content/debug.log` for PHP errors

### Duplicate Templates

Templates are tracked in `wp_options` table under key `lovetravel_child_imported_templates`. If template already exists, it will be skipped.

**To force re-import:**
```php
// In functions.php temporarily
delete_option( 'lovetravel_child_imported_templates' );
// Then reactivate theme
```

### Invalid JSON Errors

**Validate JSON syntax:**
```bash
# From theme root
cat elementor-templates/sections/01-hero-slider.json | jq .
```

**Common issues:**
- Missing closing braces `}`
- Trailing commas in arrays
- Unescaped quotes inside strings
- Non-UTF-8 characters

## Template Metadata

Each imported template includes:

- **Post Type:** `elementor_library`
- **Template Type:** `section` or `page` (auto-detected from directory)
- **Edit Mode:** `builder` (editable in Elementor)
- **Import Tracking:** Stored in `wp_options`

## Best Practices

### 1. Clean Templates Before Export

- Remove hard-coded URLs
- Use relative paths where possible
- Avoid site-specific IDs for posts/terms

### 2. Test Before Committing

- Import locally first
- Verify layout renders correctly
- Check mobile responsiveness
- Test with different content

### 3. Document Complex Templates

Add comments in commit messages:
```bash
git commit -m "feat(templates): add adventure search form

- Includes Month, Duration, Difficulty filters
- Integrates with nd-travel taxonomies
- Mobile-optimized layout
- Requires Elementor Pro"
```

### 4. Version Control

- Commit one template per commit (easier to revert)
- Use descriptive filenames
- Keep templates organized in correct directories

### 5. Client Training

Templates appear in Elementor as:
- **Sections:** Insert via **Plus icon → My Templates**
- **Pages:** Select when creating new page

Train clients to:
1. Never edit master templates directly
2. Insert template, then customize copy
3. Save customizations as new templates if reusable

## Production Deployment

When deploying to production:

1. **Ensure all files committed:**
   ```bash
   git status elementor-templates/
   ```

2. **Pull on production server:**
   ```bash
   cd /path/to/wp-content/themes/lovetravel-child
   git pull origin main
   ```

3. **Activate theme:**
   - Via WP Admin: Appearance → Themes → Activate
   - Via WP-CLI: `wp theme activate lovetravel-child`

4. **Verify imports:**
   - Check Appearance → Child Theme
   - Check Elementor → My Templates

5. **Update image references:**
   - Edit each template in Elementor
   - Replace localhost images with production images
   - Save templates

## Architecture

**Classes:**
- `LoveTravelChildElementorTemplateImporter` — Handles import logic
- `LoveTravelChildAdminNotices` — Displays import status

**Hooks:**
- `after_switch_theme` — Triggers auto-import
- `admin_notices` — Displays import results

**Storage:**
- Templates: `/elementor-templates/`
- Tracking: `wp_options` → `lovetravel_child_imported_templates`
- Elementor Library: `wp_posts` → `post_type=elementor_library`

## Support

For issues or questions:
1. Check `/wp-content/debug.log`
2. Verify dependencies active
3. Check template JSON syntax
4. Review commit history for changes

---

**Last Updated:** October 20, 2025  
**WordPress Version:** 6.8.3  
**Elementor Version:** 3.x  
**Theme Version:** 2.0.0
