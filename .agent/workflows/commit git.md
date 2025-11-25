--- 
description: Commit grouped files into a specified branch (defaults to develop) with interactive grouping and messages. 
---

# Git commit workflow — interactive grouping + commit messages

Purpose: Run `git status`, interactively group changed files into logical commits, then run `git add` + `git commit` for each group and push to the target branch (defaults to `develop`). This workflow is intended for *local development only* and will ask for confirmations before running destructive or remote operations.

Notes & Safety:

- Local/dev only. Never enable // turbo-all for production without manual review.
- This workflow will not force-push or overwrite remote history.

Steps:

1. Run `git status` and list staged / unstaged changes in human-readable form. Ask user to confirm continuation.

2. Ask target branch (default `develop`). If different from current branch, prompt to switch or create the branch.

3. Show changed files and prompt user to make groups. For each group:
   - Ask a group name and optional scope.
   - Ask which files to include in the group.
   - Ask for a short commit message following project conventions.

4. Repeat until all chosen files are grouped or user stops.

5. Preview planned commits (branch, files per commit, messages) and ask for confirmation.

6. For each confirmed group run `git add` and `git commit -m "<message>"`. Report commit SHA or failure.

7. After commits complete, ask whether to push. If yes, run `git push origin <branch>` (no force) and report results.

8. Provide summary and optional next steps (open PR, run tests, update changelog).

Optional flags (require explicit confirmation):

- `--no-push` to skip pushing.
- Per-step `// turbo` only for safe steps (status, add, commit) if user enables it explicitly.

Safety reminder: Ask before branch switching, push, or any destructive action.
