## Purpose
Run and validate tests (generic WP + manual until tooling added) including content/i18n checks.

## Context Required
- Active branch clean state.
- List of modified files.

## Inputs Required
1. Test scope (feature, regression, release).
2. Critical user journeys.

## Output Contract
- PLAN listing manual test steps + any WP-CLI commands (`wp theme list`, `wp post list` for smoke) [Inference].
- Accessibility checklist (landmarks, alt text existence), SEO basic (title tag, meta description presence), i18n (no untranslated strings).
- Evidence summary table (pass/fail counts).

## Guardrails
- Do not fabricate test results.
- Tag unavailable commands `[Unverified]`.

## Verification Steps
1. Pre-check git clean.
2. Run smoke commands (if WP-CLI available) else mark `[Unverified]`.
3. Provide structured results + follow-ups.
