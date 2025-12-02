#!/bin/sh
set -eu

# Wait-for-DB script used by the app container entrypoint.
# It attempts to connect to the configured DB until successful or timeout.

DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
MAX_RETRIES=${WAIT_MAX_RETRIES:-30}
SLEEP_SEC=${WAIT_SLEEP_SEC:-2}

echo "Waiting for database at ${DB_HOST}:${DB_PORT}..."

i=0
until nc -z "${DB_HOST}" "${DB_PORT}" > /dev/null 2>&1; do
  i=$((i + 1))
  if [ "$i" -ge "$MAX_RETRIES" ]; then
    echo "Timed out waiting for DB after ${MAX_RETRIES} attempts."
    exit 1
  fi
  sleep ${SLEEP_SEC}
done

echo "Database is available — running CMD"

# Execute wrapped command (e.g. php artisan serve)
exec "$@"
