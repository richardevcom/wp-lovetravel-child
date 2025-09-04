## Purpose
Diagnose and resolve a reported issue (PHP error, layout bug, performance regression).

## Context Required
- Error message or reproduction steps.
- Environment info (PHP/WP version if available).

## Inputs Required
1. Issue summary.
2. Repro steps.
3. Expected vs actual result.

## Output Contract
- PLAN (instrumentation steps) + approval pause.
- Hypotheses list (tag sources).
- Minimal patch suggestion (paths + rationale) with citations (3 WP docs when hook/theme related).
- Verification checklist (cache clear, reload, log check).
- Rollback steps.

## Guardrails
- Avoid broad search-replace.
- No editing vendor/plugin code.
- Mark assumptions `[Unverified]`.

## Verification Steps
1. Pre-check status & branch.
2. Add temporary instrumentation (if needed) then remove before commit.
3. Show diff & commit hash.
4. Reflect on residual risks.
