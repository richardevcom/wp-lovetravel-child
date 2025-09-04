## Purpose
Implement a new feature (code + content considerations) safely in the child theme.

## Context Required
- Task summary & acceptance criteria.
- Affected template or hook targets.
- Current branch & clean status.

## Inputs Required
1. Feature description.
2. Related issue/URL.
3. Constraints: performance, SEO, i18n.

## Output Contract
- PLAN (files + commands) → await approval.
- Proposed diff paths (no full large file dumps).
- List of hooks/APIs with citations (3 sources) link+date.
- Test checklist (functional, SEO meta, i18n strings, accessibility landmarks).
- Rollback commands.

## Guardrails
- No DB-destructive operations.
- Preserve translation domains.
- Cite WP source before using uncommon hook.
- Tag uncertainties `[Unverified]`.

## Verification Steps
1. Pre-check commands (status, branch, grep target file).
2. After edit: `git diff --name-only`, `grep -n` for inserted hooks.
3. Commit & show hash.
4. Reflect: 1–2 risks / next steps.
