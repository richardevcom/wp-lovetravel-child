# Elementor Templates (lovetravel-child)

This folder holds reusable Elementor templates/sections shipped with the child theme.

- Store JSON exports here (filename: slugified, e.g. `adventure-entry.json`, `include-exclude-info-section.json`).
- Do not embed JSON inside PHP. Import via WP Admin or helper.

## How to import (WP Admin)
1. Go to `Templates → Saved Templates`.
2. Click `Import Templates` and select the JSON file from this folder.
3. Insert it in Elementor from the Library under `My Templates`.

## Theme helper / WP-CLI (optional)
Use the theme helper to import programmatically without overwriting existing templates.

- WP-CLI:
  ```bash
  wp lovetravel import-elementor-template include-exclude-info-section.json
  ```
- PHP (within theme code or mu-plugin):
  ```php
  lovetravel_child_import_elementor_template('include-exclude-info-section.json');
  ```

Both methods check if a template with the same title already exists and skip if found.

## Notes
- Files are not auto-imported to avoid clobbering user content.
- Update templates by changing the JSON and re-importing (manual step).
- Keep each JSON self-contained (do not rely on post IDs).
