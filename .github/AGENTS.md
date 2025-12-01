# AGENTS.md — Organization / Repository Default

trace: SRS-FR-012; D04 §4.2; D11 §6; author: ai-agent

This is a short, human friendly summary for repository maintainers and contributors. For full agent policy, see `.agents/AGENTS.md`.

Purpose

- Make agent rules discoverable when performing contribution tasks.

Quick rules

- Memory-first: query the project's memory (`openmemory.md`) before making changes.
- No secrets in memory: redact tokens and credentials.
- Tool whitelist: use only approved tools listed in `.agents/AGENTS.md`.
- Human approval required for writes: DB, emails, or other side-effectful actions require explicit human confirmation.

How to change the rules

- Open a PR against `develop` and add a `trace:` header to your commit message. Link to requirements from D03/D04.

Contacts

- Security: `security@motac.gov.my`
- DevOps: `devops@motac.gov.my`

For the full policy and examples, see `.agents/AGENTS.md`.
