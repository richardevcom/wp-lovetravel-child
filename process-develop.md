## process-develop

Purpose: Consistent child theme feature development.

Steps:
1. Sync: `git fetch origin && git checkout main && git pull --ff-only`.
2. Branch: `git checkout -b feat/<slug>` (kebab-case, short).
3. Build/Preview (if tooling added later) else rely on WordPress runtime.
4. Edit minimal files; keep functions modular.
5. Lint (future placeholder) – if no tooling, manual review.
6. Commit: `feat(theme): <change summary> — refs wp-lovetravel-child`.
7. Push: `git push -u origin feat/<slug>`.
8. Open PR referencing root repo issue if applicable.

Evidence Logging: Append summary line + branch name to `copilot-edit-log.md`.

Edge Cases:
- Merge conflicts with root pointer update: rebase onto main, resolve, re-run tests (when exist).

Success Criteria: PR diff limited, no unrelated whitespace, translation textdomains unchanged.

---
Enhancements (dev + content scope):
- i18n: Use `__()`, `_e()`, `esc_html__()` with correct textdomain `lovetravel-child` [Inference].
- Accessibility: Ensure new templates contain proper landmark roles (`<main>`, `<nav>` labels) [Inference].
- SEO: Maintain `<title>` via core; add meta only via hooks (e.g., `wp_head`) NOT hard-coded duplicates [Inference].
- Performance: Prefer `wp_enqueue_script` with `in_footer` true and version param.

Pre-Commit Checklist:
1. `git diff --name-only` minimal.
2. Search stray debug: `grep -R "var_dump\|console.log" -n . || true` (should be empty).
3. i18n check: `grep -R "__(" -n . | head -n3` for usage spot-check.
4. Hooks validated with Theme Dev Handbook citation.

Rollback Strategy:
`git checkout main -- <file>` then recommit with `revert(theme):` prefix if feature abandoned.

Reflection Step: After merge, log lessons/risk in edit log.

