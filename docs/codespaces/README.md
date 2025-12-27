## GitHub Codespaces setup for ICTServe

This document explains how to use GitHub Codespaces with this repository and contains recommended devcontainer configuration, post-create steps, and notes to avoid common permission and toolchain issues.

## What this repo expects
- PHP 8.2+ (project targets PHP 8.2–8.4)
- Composer available in the container
- Node.js >= 20.19 (Vite requires Node 20+) — see `.nvmrc` / `.node-version` in repo
- A writable `storage/` and `bootstrap/cache` for Laravel

## Devcontainer guidance (example)
Place the following into your codespace devcontainer or ensure existing `.devcontainer/devcontainer.json` contains similar values.

Recommended keys (example snippet):

{
  "name": "ICTServe Codespace",
  "image": "mcr.microsoft.com/devcontainers/php:8.2",
  "features": {
    "ghcr.io/devcontainers/features/composer:1": {},
    "ghcr.io/devcontainers/features/node:1": { "version": "20.x" }
  },
  "postCreateCommand": "./scripts/codespaces-post-create.sh",
  "remoteUser": "vscode"
}

Create a `scripts/codespaces-post-create.sh` that runs safe install & fixup steps as the non-root `vscode` user:

#!/usr/bin/env bash
set -euo pipefail
php -v || true
composer install --no-interaction --prefer-dist --optimize-autoloader || true
npm ci --no-audit --no-fund || true
npm run build || true
mkdir -p storage/framework/{cache,data,sessions,views} storage/logs bootstrap/cache
chmod -R u+rwX storage bootstrap/cache
php artisan key:generate || true

Notes: the `postCreateCommand` runs as the `remoteUser` defined in the devcontainer. Avoid running `sudo` in Codespaces; use the container user and grant permissions to `vscode` via `postCreateCommand`.

## Permission notes and common issues
- If files in `/workspace` are owned by `root` (from previous runs), Codespaces may fail to install; the post-create script should ensure `storage` and `bootstrap/cache` are writable.
- Avoid using `sudo` for setup steps; instead adjust ownership with `chown` only when you control the host. If necessary, use a user-owned local clone (documented in root README).

## Node version & Vite
- Vite requires Node >= 20. If Codespaces base image uses Node 18, install Node 20 via the `node` feature or `nvm` in the devcontainer. The repo includes `.nvmrc` and `.node-version` to help alignment.

## Post-create checks (quick)
Run these in a Codespace terminal to verify environment:

```bash
php -v
composer --version
node --version
npm --version
composer install --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force  # only if DB is configured
```

## Helpful tips for CI / repeatability
- Cache Composer and npm in Codespaces using the built-in cache configuration to speed repeated starts.
- Keep `.env` templates (`.env.example`, `.env.docker`) up-to-date; Codespaces `postCreateCommand` can copy `.env.docker` to `.env` when appropriate.

## Where to add customizations
- `.devcontainer/devcontainer.json` — devcontainer configuration
- `scripts/codespaces-post-create.sh` — canonical post-create actions (committed, executable)
- `docs/codespaces/README.md` — this file

If you want, I can add a ready `scripts/codespaces-post-create.sh` and update `.devcontainer/devcontainer.json` with the suggested snippet.
