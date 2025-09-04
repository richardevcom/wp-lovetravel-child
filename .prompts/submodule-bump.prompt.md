## Purpose
Update child theme submodule pointer in root repo after release.

## Context Required
- Current child theme HEAD SHA.
- Target commit/tag.

## Inputs Required
1. Target tag/commit.
2. Changelog summary.

## Output Contract
- PLAN with commands (in root): `git submodule update --remote --merge` or manual checkout; commit message template.
- Old vs new SHA line.
- Verification commands & expected outputs.
- Rollback pointer instructions.

## Guardrails
- Do not modify code during pointer bump.
- Confirm clean trees in both repos.

## Verification Steps
1. Record current SHA.
2. Perform bump.
3. Show `git submodule status` diff.
4. Commit hash reported.
