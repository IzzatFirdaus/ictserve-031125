--- 
description: Commit grouped files into a specified branch (defaults to develop) with interactive grouping and messages.
---

# Git commit workflow — interactive grouping + commit messages

Purpose: Run `git status`, interactively group changed files into logical commits, then run `git add` + `git commit` for each group and push to the target branch (defaults to `develop`). This workflow is intended for *local development only* and must always ask for confirmations before running destructive or remote operations.

Notes & Safety

- This workflow must only run in a local or development environment. Never enable // turbo-all for production without manual review.
- The workflow will ask for confirmation before changing branches or pushing. It will never force-push or auto-reset.

Steps

1. Run `git status` and present a concise summary of staged / unstaged changes as a human-readable list. Ask the user to confirm they want to continue.

2. Ask which branch to commit into (suggest default: `develop`). If the current branch is different from the chosen branch, ask whether to switch to the chosen branch or create it (if it doesn't exist). Example prompt: "Target branch [develop] (press enter to accept default or type another branch name):"

3. Show the list of changed files and prompt the user to create commit groups. For each group do:
   - Ask a short group name (e.g., "fix-login-errors" or "ui:language-switcher") and optionally a commit scope.
   - Ask the user to add one or more files from the changed list to this group (allow comma-separated input, ranges, or "all remaining").
   - Ask for a short commit message for this group (required). The commit message should follow the project's commit style (e.g., Conventional Commits).

4. Repeat step (3) until the user has grouped all changes or chooses to leave remaining files uncommitted.

5. Present a preview of all planned commits (branch, group names, files in each group, commit messages) and ask for final confirmation.

6. For each confirmed commit group:
   - Run `git add` for the group's files.
   - Run `git commit -m "<commit message>"` using the exact message provided.
   - Report the commit SHA for each commit and capture success/failure.

7. After creating all commits, ask whether to push the branch to remote. If yes, run `git push origin <branch>` and report success or detailed failure. Do not push with --force.

8. Finalize: provide a short summary of created commits, their SHAs, and the remote branch URL (if push performed). Offer optional next steps (open PR, run tests, create changelog entry).

Optional Flags (for advanced users only, require explicit confirmation):

- `--no-push` — create commits locally but do not push.
- `// turbo` can be used on safe steps (status, add, commit) only if user explicitly activates per-step turbo. Do NOT use turbo for branch switching or any operation that could overwrite history by default.

End of workflow

---
