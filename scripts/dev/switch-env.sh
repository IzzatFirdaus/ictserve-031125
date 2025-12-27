#!/usr/bin/env bash
# Cross-platform helper to switch environment files (docker, example, restore)
# Usage: ./scripts/switch-env.sh docker

set -euo pipefail

ENV=${1-}
if [ -z "$ENV" ]; then
  echo "Usage: $0 <docker|example|local|restore>" >&2
  exit 2
fi

backup_env() {
  if [ -f .env ]; then
    ts=$(date +%Y%m%d_%H%M%S)
    cp .env ".env.backup.$ts"
    echo "Backed up current .env to .env.backup.$ts"
  else
    echo "No existing .env to back up"
  fi
}

case "$ENV" in
  docker)
    if [ ! -f .env.docker ]; then echo ".env.docker not found" >&2; exit 1; fi
    backup_env
    cp .env.docker .env
    echo "Copied .env.docker -> .env (Docker config applied)"
    ;;
  local)
    if [ ! -f .env.local ]; then echo ".env.local not found" >&2; exit 1; fi
    backup_env
    cp .env.local .env
    echo "Copied .env.local -> .env (Local development config applied)"
    ;;
  example)
    if [ ! -f .env.example ]; then echo ".env.example not found" >&2; exit 1; fi
    backup_env
    cp .env.example .env
    echo "Copied .env.example -> .env (Example config applied)"
    ;;
  restore)
    latest=$(ls -t .env.backup.* 2>/dev/null | head -n 1 || true)
    if [ -z "$latest" ]; then echo "No .env.backup.* files found to restore" >&2; exit 1; fi
    cp "$latest" .env
    echo "Restored $latest -> .env"
    ;;
  *)
    echo "Unknown environment: $ENV" >&2
    echo "Usage: $0 <docker|example|local|restore>" >&2
    exit 2
    ;;
esac

echo "If APP_KEY is empty: php artisan key:generate"
echo "Run: php artisan config:clear && php artisan cache:clear to reload settings if needed."
