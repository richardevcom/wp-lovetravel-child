## Purpose
Single source of truth for Copilot actions inside `lovetravel-child` focusing on dev + content (i18n, accessibility, SEO snippets) with strict anti-hallucination controls.

## Context Required
- Current branch & cleanliness (git status empty) [Verified].
- Submodule path: `wp-content/themes/lovetravel-child` [Verified].
- Theme header: `style.css` lines 1–15 for metadata (Theme Name, Template) [Inference].

## Inputs Required
1. Clearly stated task objective.
2. Constraints (time, performance, accessibility, SEO, localization) if any.
3. One blocking question (only if absolutely necessary).

## Output Contract
Each response MUST include:
- `PLAN:` 3–6 bullets (files + commands) before changes.
- `EVIDENCE:` blocks after running pre-check commands.
- Change summary lines (<80 chars) + commit hash.
- Citations (top 3 authoritative WP sources) with link + date.
- Labels: every load-bearing claim tagged [Verified]/[Inference]/[Unverified]/[Speculation].

## Guardrails
- No destructive commands (`rm -rf`, force push) without explicit approval.
- Keep parent theme code untouched.
- Preserve translation domains & text strings.
- Never invent WordPress APIs; cite source before first use.
- For SEO/content edits limit scope to theme templates or hooks; do not modify plugin code.

## Verification Steps
Run before editing:
1. `git status --porcelain`
2. `git rev-parse --abbrev-ref HEAD`
3. `grep -n "Theme Name:" style.css | head -n1`
4. `grep -R -n "add_action" inc | head -n5`
5. (Optional) `wp theme list` if WP-CLI available [Unverified].

After edits:
- Show `git diff --name-only --cached`.
- Show `git commit -m ...` output line.
- Show `git log -1 --pretty=oneline`.

## Reality Filter & Chain-of-Verification
For each factual claim: cite file:line or command output snippet. If not obtainable locally, mark `[Unverified]` and request confirmation before acting. Reflection: provide 1–2 bullet risk/next-step list after major change.

## Workflow
1. Receive task → produce PLAN.
2. Wait for approval.
3. Execute minimal edits.
4. Create appropriately scoped commit message template: `docs(copilot): ...`, `chore(theme): ...`, `feat(theme): ...`.
5. Append to `copilot-edit-log.md` (timestamp, summary, verification commands used).

## Rollback
```
git checkout <previous_sha> -- <files>
git commit -m "revert(theme): restore <files>"
```
For multiple commits: `git revert <sha_range>` sequentially.

## Citations (Authoritative Sources)
- Theme Dev Handbook – https://developer.wordpress.org/themes/ (Access date 2025-09-04) [Inference]
- Coding Standards (PHP) – https://developer.wordpress.org/coding-standards/ (Access 2025-09-04) [Inference]
- Theme Review Guidelines – https://make.wordpress.org/themes/handbook/review/ (Access 2025-09-04) [Inference]
- WP-CLI – https://wp-cli.org/ (Access 2025-09-04) [Inference]
- Plugin Dev Handbook (hooks context) – https://developer.wordpress.org/plugins/ (Access 2025-09-04) [Inference]

## Success Criteria
Clean working tree post-commit; logs updated; all claims cited; minimal diff footprint; SEO/i18n preserved.

