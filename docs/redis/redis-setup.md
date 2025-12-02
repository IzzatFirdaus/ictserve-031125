# Redis Setup for ICTServe

This document explains how Redis is configured and can be run for ICTServe in two common development workflows:

- Docker Compose (recommended for development)
- Local (Laragon / WSL / Windows host Redis)

It also contains verification and troubleshooting steps.

---

## Overview

ICTServe uses Redis for the following features:

- Laravel cache store
- Laravel session store (optional)
- Laravel queue connection

By default the repo is configured to use:

- Docker: `REDIS_HOST=redis` inside `app` container via `.env.docker` (see `docker-compose.yml` / `compose.yaml`)
- Local host / Laragon: `REDIS_HOST=127.0.0.1` via `.env`

Redis is required when the app uses `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`, or `CACHE_STORE=redis`.

---

## Files modified / created by the team

- `compose.yaml`: A `redis` service was added and the `app` service `depends_on` entry now includes `redis`.
- `Dockerfile`: `phpredis` is installed via `pecl` and enabled. Rebuild is required for PHP image to include the extension.
- `.env.docker`: Docker environment file now has `REDIS_HOST=redis` and `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` set to `redis` (so the container uses Redis for cache / session / queue).

> Note: The project `compose.yaml` mounts `.env.docker` as `env_file` for the `app` service; `docker-compose` + `.env.docker` is used in the Docker-based dev workflow.

---

## Docker usage (recommended for development)

1) Build and run Docker compose (this picks up `phpredis` install via `Dockerfile` as the app container must be rebuilt):

```powershell
# From repository root
docker compose -f compose.yaml up -d --build
```

2) Check the redis container is ready:

```powershell
# Check container status
docker compose -f compose.yaml ps

# Check redis health (if healthcheck present)
docker compose -f compose.yaml logs redis --tail 100

# Ping the redis server
docker exec -it ictserve-redis redis-cli ping
# PONG indicates Redis is available
```

3) Verify PHP extension `redis` is available in the `app` container:

```powershell
docker exec -it ictserve-app php -m | Select-String redis
# or
docker exec -it ictserve-app php -i | Select-String "redis"
```

4) Verify Laravel can use Redis (in Docker setup `.env.docker` has been configured):

```powershell
# Run inside container
docker exec -it ictserve-app php artisan cache:clear
# Should print: "Application cache cleared!"

# To test queue, run a single worker
docker exec -it ictserve-app php artisan queue:work --once
```

5) If you rebuild your Docker image and the app container doesn't connect to Redis, verify the `app` environment contains `REDIS_HOST=redis` (due to `.env.docker` or `compose.yaml` environment entries) and check `docker logs ictserve-app` for errors.

### Docker-network notes

- `compose.yaml` creates a Docker network where services can talk to each other by the service name `redis`.
- If you need to use a different port to avoid conflicts on your host machine, change the port mapping in `compose.yaml` (e.g. "6380:6379") and update `REDIS_PORT` in `.env.docker`.

---

## Local development (Laragon / Windows / WSL)

If you prefer to run Redis on your host machine (Windows), these are recommended approaches:

### Option A — Laragon or local Windows Redis installation

1) Install Redis for Windows if your development environment requires a native Redis server (Laragon may include Redis or you can use a Windows distribution / zip). Start the service and configure it to listen on `127.0.0.1:6379`.

2) Ensure local `.env` has the correct values:

```dotenv
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

3) Confirm an existing Redis instance is reachable:

```powershell
redis-cli ping
# PONG -> ok
```

4) Verify Laravel can connect to host Redis (run locally, not inside Docker):

```powershell
php artisan cache:clear
php artisan queue:work --once
```

> If your local PHP runs inside a different environment (e.g., WSL), make sure `127.0.0.1` resolves correctly and the Redis server is accessible from that environment. If using WSL, install redis-server in WSL or connect to the host's IP.

### Option B — WSL (recommended for Windows dev comfort)

From WSL:

```bash
sudo apt update
sudo apt install -y redis-server
sudo service redis-server start
redis-cli ping # PONG
# Then run your PHP commands from within WSL
php artisan cache:clear
```

### Option C — Docker (standalone, if not using `compose.yaml`)

```powershell
docker run -d --name ictserve-redis -p 6379:6379 redis:7
# Check
docker exec -it ictserve-redis redis-cli ping

# You can run a quick Redis CLI check from your host
redis-cli -h 127.0.0.1 -p 6379 ping
```

---

## Verifying you are using Redis in Laravel

- `php artisan env` to confirm your environment.
- Confirm `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` are `redis` (or set as needed in `.env` or `.env.docker`).

Check Laravel config:

```php
// config/database.php -> redis.connections.default
// config/cache.php -> 'redis' store
```

Turple test

```powershell
# Clear config cache, run config:cache if necessary
php artisan config:clear
php artisan config:cache

# Clear cache via laravel
php artisan cache:clear
```

Check the Laravel log for any Redis connection errors in `storage/logs/laravel.log`.

---

## Troubleshooting

- “Connection refused” or socket errors:
  - Ensure Redis is started and listening on the configured host/port.
  - For Docker usage, use `docker compose -f compose.yaml ps`, `docker logs ictserve-redis`, and `docker exec -it ictserve-redis redis-cli ping`.
  - For host usage, use `redis-cli ping` locally.

- Conflict on port 6379:
  - If your host already has Redis on 6379 and you run the Docker compose (which maps `6379:6379`), the compose will fail to start Redis container due to port conflict. Update `compose.yaml` to use a different host port (e.g. `"6380:6379"`) and update `.env.docker` `REDIS_PORT` accordingly.

- `phpredis` extension missing:
  - We added `pecl install redis` to the `Dockerfile`. If you updated `Dockerfile`, re-run:

```powershell
docker compose -f compose.yaml up -d --build
```

- Session or cache still using `file` / `database`:
  - Check `.env` or `.env.docker` for `CACHE_STORE`, `SESSION_DRIVER`.
  - `php artisan config:cache` and `php artisan config:clear` may be needed to re-read environment changes.

- Incompatibilities with Predis vs phpredis:
  - `phpredis` is preferred and we use `phpredis` (`REDIS_CLIENT=phpredis`) in this repo.
  - Some libraries or Symfony components allow `predis` — the repo supports both if needed, but the Dockerfile uses `phpredis`.

---

## Notes

- Rebuilding the Docker `app` container is required after `Dockerfile` changes (e.g., installing `phpredis`) so `php -m` displays `redis` as an installed extension.
- In multi-developer environments, developers may prefer using Docker for consistent Redis behavior.
- If using remote Redis (hosted) for staging / testing: set `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD` as appropriate.

---

## Support & Follow-ups

If you want, I can also do the following:

- Add a small artisan command that quickly verifies the Redis connection in-app and returns helpful diagnostics.
- Update `scripts/laragon/setup-laragon.ps1` to optionally install Redis automatically (Windows-only: note that automatic server installs require admin privileges and are optional).

If you want me to add a test route or diagnostic command, say so and I’ll add it.
