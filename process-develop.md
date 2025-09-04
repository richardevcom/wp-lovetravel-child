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

