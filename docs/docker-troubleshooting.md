# Docker — Quick DB troubleshooting

This document collects the most common checks for "Connection refused" errors when running ICTServe with Docker Compose.

1) Confirm containers are running and healthy

   docker compose ps

2) Tail the DB logs to check for failures/restarts

   docker compose logs --tail 200 db

3) Check Laravel/APP effective environment inside the running app container

   docker compose exec app sh -lc "php -r 'echo getenv(\"DB_HOST\") . "\n"; echo getenv(\"DB_USERNAME\") . "\n"; echo getenv(\"DB_DATABASE\") . "\n";'"

4) From the app container, test a DB connection directly

   docker compose exec app sh -lc "mysql -h \"${DB_HOST:-db}\" -u \"${DB_USERNAME:-laravel}\" -p\"${DB_PASSWORD:-secret}\" -e 'select 1'"

5) If the DB is not reachable:
   - Inspect DB logs; check for startup errors or permission issues.
   - Ensure the correct credentials are in docker/.env (or in the service environment in `docker-compose.yml`).
   - Avoid mounting a host `.env` to the container unless you intentionally want to override the container's env. A host `.env` with `DB_HOST=127.0.0.1` will cause the app to try connecting to itself instead of the `db` service.

6) The app image includes a startup helper (scripts/docker/wait-for-db.sh) which will wait for MySQL before starting the Laravel server — you can tune WAIT_MAX_RETRIES / WAIT_SLEEP_SEC in `docker/.env`.
