## Purpose
Standardize child theme release & integration with root submodule.

## Context Required
- Clean working tree.
- Merged feature branches into `main` (or release branch).
- Changelog summary prepared.

## Inputs Required
1. Target version tag (semver or date).
2. Summary of changes (bullets).
3. Any migration steps.

## Output Contract
- PLAN (tag + push + notify root).
- Commands issued with evidence.
- Resulting tag + commit hash.
- Instructions to bump root submodule pointer.

## Guardrails
- Never force-push tags.
- Tag only fast-forward main.
- Ensure no uncommitted changes.

## Verification Steps
1. `git checkout main && git pull --ff-only`.
2. `git status --porcelain` (empty).
3. Update `CHANGELOG.md` (if exists) or append entry to `copilot-edit-log.md`.
4. `git tag -a v<version> -m "release: v<version> <summary>"`.
5. `git push origin v<version>`.
6. Root repo: run submodule bump prompt.

## Rollback
If incorrect: `git tag -d v<version> && git push origin :refs/tags/v<version>` (only before adoption).

## Citations
- Theme Dev Handbook (enqueue/versioning) – https://developer.wordpress.org/themes/ (2025-09-04) [Inference]
- WP-CLI (optional tagging via git still) – https://wp-cli.org/ (2025-09-04) [Inference]
- Coding Standards – https://developer.wordpress.org/coding-standards/ (2025-09-04) [Inference]

## Success Criteria
Published tag accessible on remote; log updated with version & SHA.