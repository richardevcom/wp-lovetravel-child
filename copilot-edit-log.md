## Copilot Edit Log (Theme)

### 2025-09-04 Initialization
- [Verified] Created instructions & processes.
- [Verified] Added release prompt.
- [Inference] No build tooling yet; future lint/test placeholders.

### Rollback Commands
````bash
git submodule deinit -f wp-content/themes/lovetravel-child
git rm -f wp-content/themes/lovetravel-child
git add .gitmodules && git commit -m "revert: remove submodule"
cp -a /tmp/lovetravel-child-backup-* wp-content/themes/lovetravel-child
````
