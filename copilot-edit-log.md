## Copilot Edit Log (Theme)

### 2025-09-04 Initialization
- [Verified] Created instructions & processes.
- [Verified] Added release prompt.
- [Inference] No build tooling yet; future lint/test placeholders.
### 2025-09-04 Instructions & Prompts Expansion
- [Verified] Branch created: chore/wp-lovetravel-child/instructions
- [Verified] Updated copilot instructions commit d192e6b
- [Verified] Added prompt set commit e61c449
- [Verified] Added process-release & develop enhancement commit 65fe589
- [Inference] No package/composer tooling; prompts use WP-CLI placeholders
- Reflection: Next step could add automated test harness (PHPUnit) before complex refactors

### 2025-09-08 Admin tools asset externalization
- [Verified] Branch `refactor/wp-lovetravel-child/cleanup`
- [Verified] Externalized Mailchimp exporter JS to `assets/js/admin-mailchimp-export.js`
- [Verified] Moved inline styles to shared `assets/css/admin-tools.css`
- [Verified] Enqueued assets conditionally and localized AJAX config
- [Verified] Kept nonce and capability checks intact
- Commit: ac9e3aa feat(admin): externalize Mailchimp exporter assets
- Verification commands:
	- `git status --porcelain` → clean after commit
	- `git rev-parse --abbrev-ref HEAD` → refactor/wp-lovetravel-child/cleanup
	- `grep -n "Theme Name:" style.css | head -n1` → confirms child theme
	- `grep -R -n "add_action" inc | head -n5` → shows hooks registered
	- Optional WP-CLI: `wp theme list` [Unverified]


### Rollback Commands
````bash
git submodule deinit -f wp-content/themes/lovetravel-child
git rm -f wp-content/themes/lovetravel-child
git add .gitmodules && git commit -m "revert: remove submodule"
cp -a /tmp/lovetravel-child-backup-* wp-content/themes/lovetravel-child
````

### 2025-09-09 Adventures Import Tool
- [Verified] Branch `refactor/wp-lovetravel-child/cleanup`
- [Verified] Added Adventures Import tool:
	- PHP controller `inc/tools/payload-adventures-import.php`
	- Admin page `inc/tools/payload-adventures-import.page.php`
	- JS `assets/js/admin-adventures-import.js`
	- Submenu under CPT `nd_travel_cpt_1`
	- AJAX: stats, paged import, ensure month terms
	- Mappings: Duration, Difficulty, Month; media sideload; overwrite + dry-run
- Commit: d4d7a32 feat(theme): add Adventures Import tool (Payload→WP)
- Verification commands:
	- `git status --porcelain` → staged/clean after commit
	- `git rev-parse --abbrev-ref HEAD` → refactor/wp-lovetravel-child/cleanup
	- `grep -n "Theme Name:" style.css | head -n1` → confirms child theme
	- `grep -R -n "add_action" inc | head -n5` → shows hooks registered
	- `git log -1 --pretty=oneline` → shows latest commit hash

### 2025-09-09 Elementor meta mapping alignment
- [Verified] Importer sets parent theme Elementor keys:
  - `nd_travel_meta_box_show_price`, `nd_travel_meta_box_price`, `nd_travel_meta_box_new_price`
  - `nd_travel_meta_box_promotion_price`, `nd_travel_meta_box_promo_price`
  - `nd_travel_meta_box_availability_from`, `nd_travel_meta_box_availability_to`
  - Also persists custom fields: `reservation_price`, `full_price_existing`, `full_price_new`, `discount_price`, `discount_until`, `date_from`, `date_to`, `length_days`
- [Verified] Compute `length_days` from `date_from`/`date_to` if missing
- [Inference] Elementor widgets read these keys per theme demo export
- Verification commands:
	- `git --no-pager status --porcelain`
	- `git --no-pager diff --name-only --cached`
	- `git --no-pager log -1 --pretty=oneline`

