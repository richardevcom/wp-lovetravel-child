## theme-release.prompt

Purpose: Standardize child theme release tagging & communication.

Inputs:
1. Target version (semver or date-tag).
2. Current HEAD SHA.
3. Changelog summary (bullet list).

Procedure:
1. Pre-check: `git status --porcelain`, `git rev-parse --abbrev-ref HEAD`.
2. Generate `CHANGELOG` section (manual summary if no tooling).
3. Tag: `git tag -a v<version> -m "release: v<version> <summary>"`.
4. Push: `git push origin v<version>`.
5. Update `copilot-edit-log.md` with version + SHA.

Output Contract:
- PLAN, EVIDENCE, summary line `released vX.Y.Z (<shortsha>)`.

Guardrails:
- No force-push of existing tags.
- Skip if dirty tree.

Rollback:
`git tag -d v<version> && git push origin :refs/tags/v<version>` (only if mistake, before adoption).

Success: Tag exists on remote; log updated.

