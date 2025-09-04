## Purpose
Safely update documentation (instructions, processes, prompts) minimizing churn.

## Context Required
- Current docs list & intended change.

## Inputs Required
1. Doc file(s) to modify.
2. Change rationale.

## Output Contract
- PLAN enumerating target lines/sections.
- Diff summary (filenames + line ranges only).
- Citations for any new WP guidelines referenced.
- Rollback instructions.

## Guardrails
- No removal of citation blocks without replacement.
- Keep file <400 words unless justified.

## Verification Steps
1. Pre-check status.
2. Apply minimal edits.
3. Show diff names + commit hash.
4. Reflect risks.
