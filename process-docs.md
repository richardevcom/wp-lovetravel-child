## process-docs

Purpose: Keep only essential theme documentation.

Core Files:
- `.github/copilot-instructions.md`
- `process-develop.md`
- `process-docs.md`
- `copilot-edit-log.md`
- `.prompts/theme-release.prompt.md`

Update Triggers:
- New release workflow change.
- Added/removed build tooling.

Procedure:
1. Inspect current docs (grep version markers if any).
2. Propose PLAN of modifications.
3. After approval edit minimal lines.
4. Append edit summary to `copilot-edit-log.md`.

Pruning Criteria:
- Remove obsolete instructions superseded by automation.

Rollback: Restore previous file version via `git checkout <sha> -- <file>`.

Success: Docs <400 words each, no duplication with root.

