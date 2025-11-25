---
description: Local environment setup for this Laravel 12 + Livewire 3 + Filament 4 repository — auto-install dependencies, guide environment variables, and perform a safe local launch checklist.
---

1. Confirm you want to run the Local Environment Setup now (environment: local only).

   - Ask: "Proceed with local environment setup? (yes/no)".
   - If no → stop with instructions for manual setup.

1. Detect and show system prerequisites (do not change anything yet).

   - Check and report installed versions for: PHP (>=8.2), Composer, Node (>=18), npm/pnpm, Git, MySQL or SQLite.
   - If any requirement missing → show installation hints and stop.

1. Create the env file (interactive) — never copy secrets blindly.

   - If .env is missing: copy .env.example to .env.
   - Ask user to confirm which database to use (sqlite/mysql) and prompt for DB credentials (host, port, username, password) — remind the user to avoid sharing secrets in public chat.
   - If user opts for SQLite, create database.sqlite (or the repo's preferred path).

1. Install PHP dependencies (composer). // turbo

   - Run: composer install --no-interaction --prefer-dist
   - If composer is not available → show next steps to install composer locally.

1. Install Node dependencies (npm / pnpm) // turbo

   - Detect package manager (prefer npm ci if package-lock.json exists, or pnpm install if using pnpm).
   - Run the appropriate command, e.g., npm ci --prefer-offline or pnpm install.

1. Generate Laravel application key and config caches (safe, idempotent). // turbo

   - Run: php artisan key:generate
   - Recommend running (optional): php artisan config:clear and php artisan cache:clear to ensure fresh config.

1. Run local database migrations (ask first — non-destructive on local).

   - Ask: "Run migrations + seed now? (yes/no)" (Explain this will modify local DB)
   - If yes → run php artisan migrate --seed (do NOT use --force unless the user explicitly asks for production-style behavior)
   - If using SQLite, create file permissions and confirm path.

1. Compile / start frontend dev assets (choose dev or build). // turbo (dev recommended)

   - Common choices:
     - Dev: npm run dev or composer run dev (keeps watcher active)
     - Build: npm run build for production build
   - Ask which to run and run accordingly.

1. Start required background processes (optional) — ask user if they want these launched now:

   - Queue worker (local testing): php artisan queue:work (ask for background/foreground, recommend supervised run).
   - Schedule runner (if project uses scheduled tasks): php artisan schedule:work.

1. Run tests & quick verification (recommended). // turbo for the test command only if user agrees

   - Run unit + feature tests: php artisan test or phpunit.
   - If any tests fail, present failing tests with hints for next steps.

1. Code quality checks (suggested; run automatically if user confirms). // turbo optional

   - Format: vendor/bin/pint --dirty
   - Static analysis: vendor/bin/phpstan analyse (reporting level suggested by project)
   - Frontend lint/type check: npm run lint / npm run type-check

1. Final local launch checklist (present result summary and URLs)

   - Confirm local server up and reachable, e.g. <http://localhost:8000> or the configured dev server URL.
   - DB connection verified and seeded (if applicable).
   - Dev assets watch/build running.
   - Queue worker running (if needed).
   - Tests passing (or list failures).
   - Provide quick commands to stop or reset (e.g., stop dev server, rebuild assets).

1. Optional: save this session to project memory (work_session) with a short summary: files touched, commands run, tunnel/port used, any outstanding failures or manual steps required.

- Ask permission before saving session context.

Safety & best-practices notes (must be shown before automation):

- This workflow may run system commands. Confirm that this is for a "local/dev" environment — never enable automatic run (turbo-all) for shared or production environments without manual review.
- Per-step turbo is recommended only for low-risk steps (install deps, generate key); ask explicitly before migrations or destructive commands.
- Never write secrets to project files in non-secure channels; prompt to set secrets in .env and keep them out of chat history.

Suggested conversational prompts for antigravity:

- "Setup my local environment (dev) — run dependencies and Dev server."
- "Prepare dev environment using SQLite and run unit tests only."
- "Install dependencies but do not run migrations."

