---
agent: dev_deploy_agent
---
# Defaults: branch=develop, commit_to_default_branch=true, push_to_origin=true

# Commit grouped files into branch — interactive Git assistant

Purpose: Help the developer create logically grouped Git commits from a working tree and commit them to a target branch (defaults to `develop`). This prompt is designed for interactive use in VS Code or other chat-based assistant integrations.

Context: This repository follows Conventional Commits and standard PR-based workflows. Commits should be focused, descriptive, and pushed only after confirmation.

Assistant Instructions

- Begin by running `git status` and present a concise human-friendly summary of staged and unstaged changes.
- Ask the user to confirm they want to proceed with grouping and committing changes (local/dev context only).
- Ask which branch to use (default `develop`). If the branch is different from the current branch, ask whether to switch to it or create it.
- Present the list of changed files and ask the user to create one or more commit groups. For each group, ask:
  1. Group name or short scope (e.g. `fix-auth`, `feat:language-switcher`).
  2. Which files to include in the group (allow ranges, comma-separated lists, or the option `all remaining`).
  3. A short, conventional commit message describing the change.
- Show a clear preview of all planned commits (branch, files in each commit, commit messages) and request final confirmation before making any Git calls.
- For each confirmed commit group: run `git add` for the group's files and `git commit -m "<message>"`. Report the commit SHA and success/failure status for each commit.
- After creating all commits, ask whether to push the branch to the remote. If user agrees, run `git push origin <branch>` (do not use `--force`).

Constraints & Safety

- This workflow is explicitly for local/dev usage. Do not run in production environment or with // turbo-all enabled without an explicit human review.
- Always ask for explicit confirmation before switching branches, creating branches, pushing to remote, or performing destructive actions.
- Support the flag `--no-push` to create commits locally but not push them (ask to confirm this behaviour).

Examples (short prompts to the assistant)

- "Run git status. Group files into logical commits and commit to develop."
- "Run git status. Create grouped commits for UI and backend changes and push to feature/my-branch."

End of prompt
```

