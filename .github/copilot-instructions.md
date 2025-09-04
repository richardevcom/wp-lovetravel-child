## Copilot Instructions (Theme)

Scope: Operations limited to this submodule (`lovetravel-child`). Root-level changes require root instructions.

Pre-Check (Theme Local):
1. `git status --porcelain`
2. `git rev-parse --abbrev-ref HEAD` (expect `chore/wp-lovetravel-child/init` or feature branch)
3. `grep -n "--primary-color" style.css || true`

Workflow:
1. Propose PLAN (3–5 bullets) for any edit.
2. Await "approve".
3. Make minimal edits; show diff summary or `grep -n` evidence.
4. Commit with template: `chore(theme): <summary> — refs wp-lovetravel-child`.
5. Push & report commit hash.

Guardrails:
- Do not alter licensing/readme of parent theme.
- Preserve CSS variable tokens / translation domain strings.
- No large file dumps.

Rollback: standard `git checkout <previous_sha>` then recommit.

Outputs: PLAN, EVIDENCE, summary line.

Success: Clean tree after commit; style assets intact.

