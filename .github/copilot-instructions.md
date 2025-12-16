---
applyTo: "**"
description: "ICTServe Laravel coding standards, architecture patterns, and developer workflows for AI agents"
---

# Copilot Instructions — ICTServe

**Purpose**: Actionable guide for AI agents to be immediately productive in this Laravel + Livewire + Filament application.

**Source of Truth**: D00–D17 documents (requirements, design, traceability), codebase conventions, docs/ folder.

## Core Architecture

**ICTServe** is a government (MOTAC) internal system for helpdesk tickets and asset loans with True Hybrid AI, bilingual support (Malay/English), and WCAG 2.2 AA compliance.

**Stack**: Laravel • Livewire (server-driven UI) • Filament (admin panel) • Tailwind • Vite • PHPUnit • Redis • MySQL

**Key Integration Points**:
- **AI Routing**: `ModelRouter` class intelligently routes between AWS Bedrock (cloud) and Ollama (local AI) based on prompt size, data classification, and user consent.
- **RAG Pipeline**: `RagService` implements Retrieval-Augmented Generation with vector embeddings, context management, and conversation history.
- **Approval Workflows**: Multi-step approval matrix with email notifications, SLA tracking, and audit trails (`ApprovalService`, `EmailNotificationService`).
- **Accessibility**: WCAG 2.2 AA compliance with bilingual UI, high-contrast support, and semantic HTML (Livewire components use `#[Layout]`, `#[Validate]` attributes).
- **Audit & Compliance**: Field-level change tracking (`OwenIt\Auditing`), activity logs (`Spatie ActivityLog`), PDPA compliance checks.

**Entry Points** (know these first):
- `bootstrap/app.php` — routing (`routes/*.php`), middleware stack (see alias list), exception handling.
- `bootstrap/providers.php` — service providers (auto-discovery in Laravel).
- `app/Filament/` — admin panel resources, pages, widgets (auto-registered at `admin` route).
- `routes/web.php` — guest & public routes (guest helpdesk, loan apps); authenticated routes in separate files.
- `routes/api.php` — API endpoints (Sanctum-protected with configurable abilities).

**Model Patterns** (all in `app/Models/`):
- **Mandatory traits**: `HasFactory`, `SoftDeletes` (unless explicitly not needed), `Auditable` (if tracked).
- **Casts**: Use `protected function casts(): array` method (NOT `$casts` property) for Eloquent attribute casting.
- **Relationships**: Explicit return types; use `belongsTo`, `hasMany`, `hasManyThrough` for data integrity.
- **Scopes**: Use `#[Scope]` attribute for local scopes, `#[ScopedBy]` for global scopes.
- **Observers**: Track lifecycle hooks with `#[ObservedBy]` attribute; implement `saving()`, `created()`, `updated()`.

## Livewire Patterns (Server-Driven UI)

**All interactive features use Livewire**; Vue/React reserved for future expansion.

**Component Structure** (`app/Livewire/` + `resources/views/livewire/`):
- **State Management**: Server-side state with reactive properties; mutations via `#[Computed]`, `#[Validate]` attributes.
- **Lifecycle**: `mount()` for initialization (runs once), `updatedPropertyName()` for reactive updates, `#[Validate]` for form validation.
- **Real-time Updates**: Use `wire:model.live.debounce.300ms` for search/filter; `wire:model` defaults to deferred (on blur).
- **Performance**: Use `OptimizedLivewireComponent` and `OptimizedFormPerformance` traits to prevent N+1 queries, cache computed properties.
- **Testing**: Use `Livewire::test(ComponentClass::class)` for assertions; verify lifecycle hooks, state changes, emitted events.

**Critical**: Do NOT use Volt (single-file components) unless explicitly requested—existing components use class-based Livewire.

## Filament Patterns (Admin Panel)

**All admin features in `app/Filament/Resources/`**; Filament auto-discovers at `/admin` route.

**Resource Structure** (SDUI—Server-Driven UI):
- **Resources**: One resource per Eloquent model; define forms, tables, pages.
- **Forms**: Use schema components (`TextInput::make()`, `Select::make()`, etc.); validation rules in `rules()` method.
- **Tables**: Paginated, sortable, filterable; use `Columns\TextColumn`, `IconColumn`, `SelectColumn` for display.
- **Actions**: Unified UI for all operations (create, update, delete, custom); modals handle validation and confirmation.
- **Authorization**: Policies in `app/Policies/` (check `submit()`, `view()`, `update()`, `delete()`); Filament respects policies automatically.

## Service Layer (Business Logic)

**All business logic in `app/Services/`** (80+ services); controllers delegate to services.

**Critical Services**:
- `ModelRouter`: Routes text generation between Bedrock (cloud) and Ollama (local). Logic: prompt size > 10k → Ollama; disabled Bedrock → Ollama; data classification requires consent but user declined → Ollama.
- `RagService`: RAG pipeline with vector embeddings, document chunk retrieval, context management, and conversation history.
- `ApprovalService`: Multi-step approval workflow; tracks approver responses, sends notifications, manages SLA deadlines.
- `EmailNotificationService`: Queue-based email with templates, localization support, retry logic.
- `BedrockService`: AWS Bedrock integration with rate limiting, error handling, token cost tracking.
- `OllamaClient`: Local LLM (Ollama) with graceful degradation if unavailable.

**Service Injection**: Use constructor promotion (`public function __construct(private ModelRouter $router) {}`); request from container in controllers/Livewire.

## Form Requests & Validation

**Always use Form Request classes** (in `app/Http/Requests/`); never inline validation in controllers.

**Example**:
```php
class StoreTicketRequest extends FormRequest {
    public function authorize(): bool { return true; } // or policy check
    public function rules(): array {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:ticket_categories,id'],
        ];
    }
    public function messages(): array {
        return ['category_id.exists' => 'Category tidak sah.'];
    }
}
```

**In Controller**: `public function store(StoreTicketRequest $request) { $data = $request->validated(); }`

## Traceability & Requirements Mapping

**Every major class must include a `// trace:` comment** linking to D03–D17 documentation.

**Format**:
```php
/**
 * Description of what this does
 * 
 * trace: D03-FR-001.1, D04-§6.1, D10-§7 (Requirements 1.1, 11.1–11.7)
 */
```

**Why**: Government (MOTAC) requires full traceability from requirements → design → code for compliance audits, WCAG 2.2 AA verification, and OWASP security checks.

**Key Document References**:
- **D03**: Software Requirements Specification (SRS) — functional requirements (FR), non-functional requirements (NFR).
- **D04**: Architecture Design — system design, component interaction, API contracts.
- **D10**: Source Code Documentation — code standards, patterns, API docs.
- **D11**: Data Design & Database Schema — ER diagrams, normalization, audit trails.
- **D12**: UI/UX Design — component specifications, localization, accessibility rules.

## Testing Standards (PHPUnit)

**All features require automated tests**; run tests before commits.

**Commands**:
```bash
php artisan test                                    # Run all
php artisan test --filter=TicketTest               # Specific class
php artisan test tests/Feature/Helpdesk/           # Specific dir
php artisan test --filter=test_guest_submit        # Specific test
```

**Test Structure** (use factories, avoid hard-coded data):
```php
#[Test]
public function guest_can_submit_ticket(): void {
    $category = TicketCategory::factory()->create();
    $response = $this->post(route('helpdesk.submit'), [
        'title' => 'Network down',
        'category_id' => $category->id,
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('submissions', ['title' => 'Network down']);
}
```

**Coverage**: Test happy path, validation failures, authorization checks, and edge cases.

## Database Migrations

**Always reversible** — include proper `down()` method:
```php
public function up(): void {
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->string('ticket_number')->unique();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down(): void { Schema::dropIfExists('submissions'); }
```

**Column Modifications**: Always include ALL previous attributes (e.g., `->unique()`, `->nullable()`) or they'll be lost.

## Middleware & Authorization

**Middleware** (defined in `bootstrap/app.php`):
- `guest.ratelimit`: Rate limiter for public helpdesk/loan forms.
- `staff`: Ensures user has staff role (Filament admin access).
- `approver`: Ensures user is in approval matrix.
- `two-factor`: Enforces 2FA for admin users.
- `recaptcha`: Validates reCAPTCHA for guest submissions.

**Authorization** (Policies in `app/Policies/`):
```php
class SubmissionPolicy {
    public function view(User $user, Submission $submission): bool {
        return $user->hasRole('staff') || $user->id === $submission->created_by;
    }
}
```

**In Controllers/Livewire**: `$this->authorize('view', $submission);`

## Email & Queue Jobs

**All email is async** (queued) via `ShouldQueue` interface:
```php
class SendApprovalNotification implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function __construct(public Submission $submission) {}
    
    public function handle(): void {
        Mail::send(new ApprovalMail($this->submission));
    }
}
```

**Queue Worker**: `php artisan queue:work` (uses Redis by default; see `config/queue.php`).

**Email Templates**: Blade templates in `resources/views/mail/`; use localization keys for multi-language support.

## Localization (i18n)

**Languages**: English (en) and Bahasa Melayu (ms).

**Keys Location**: `resources/lang/{en|ms}/` (e.g., `resources/lang/en/messages.php`).

**Usage in Blade**: `{{ __('messages.welcome') }}` or `__('messages.welcome', ['name' => 'Ali'])`.

**Usage in PHP**: `trans('messages.welcome')` or `app('translator')->get('messages.welcome')`.

**Guideline**: UI text prefers Bahasa Melayu primary (in blade files), English secondary (fallback).

## Project-Specific Rules

- **No new top-level directories**: Place all code in existing folders (`app/`, `resources/`, `database/`, `scripts/`, `docs/`, `.github/`).
- **Traces on major changes**: Every new class, migration, service, or policy must include `// trace:` comment.
- **Filament path discovery**: Resources/pages/widgets MUST live under `app/Filament/` (auto-discovery depends on this).
- **No inline validation**: Always use Form Request classes.
- **Casts method, not property**: All Eloquent models use `protected function casts(): array` (not `protected $casts`).
- **Markdown files**: Only create if explicitly requested; prefer updating docs/ or inline chat responses.
- **Laravel Boost**: Use built-in tools (tinker, search-docs, database-query, browser-logs) for debugging and research.

## Developer Workflow (Step-by-Step)

1. **Setup**: `composer install --no-interaction --prefer-dist && npm ci`
2. **Develop**: 
   - Run `php artisan serve` (terminal 1)
   - Run `npm run dev` (terminal 2) — on Windows: `npm run dev:win`
   - Edit code in `app/`, `resources/`, `database/`
3. **Quality Checks**:
   ```bash
   vendor/bin/pint --dirty        # Format PHP
   vendor/bin/phpstan analyse     # Static analysis
   php artisan test --filter=YourTestName  # Run focused tests
   ```
4. **Commit & Push**:
   ```bash
   git checkout -b feature/description
   git commit -m "feat: description"
   git push origin feature/description
   ```
5. **Pull Request**: Fill PR template (references trace IDs), request review.
6. **CI**: GitHub Actions runs phpstan, pint, tests automatically.

## Common Debugging

- **Livewire state issue**: Use `#[Computed]` for derived data; avoid multiple updates in same request.
- **N+1 queries**: Use `with(['relation'])` in queries; check Telescope (admin panel → Pulse).
- **Email not sent**: Check queue worker running; inspect `jobs` table and `failed_jobs`.
- **Locale not switching**: Verify `SetLocaleMiddleware` in `bootstrap/app.php`; check session.
- **Filament resource not appearing**: Check resource extends `Resource`, is in `app/Filament/Resources/`, and has `getPages()` returning proper CRUD routes.

## CI/CD Expectations

**GitHub Actions** (`.github/workflows/`):
- Runs `phpstan analyse` (Level 9 strictness)
- Runs `vendor/bin/pint --test` (PSR-12 format check)
- Runs `php artisan test` (all tests)
- Security scanning for secrets, dependencies

**Before Pushing**: Run all checks locally to avoid CI failures.

## External Dependencies & API Keys

**Config Files** (never commit `.env`):
- `config/bedrock.php` — AWS Bedrock endpoint, model IDs, rate limits
- `config/ollama.php` — Ollama local endpoint, model name
- `config/mail.php` — Email driver (SMTP, Mailgun, etc.)
- `config/queue.php` — Queue driver (Redis recommended)

**Secrets**: Use `.env` for local development; GitHub Secrets for CI/CD.

## Quick Reference

| Task | Command |
|------|---------|
| Create model + factory | `php artisan make:model Ticket --all --no-interaction` |
| Create service | `php artisan make:class Services/TicketService --no-interaction` |
| Create Form Request | `php artisan make:request StoreTicketRequest --no-interaction` |
| Create migration | `php artisan make:migration create_tickets_table --no-interaction` |
| Create Policy | `php artisan make:policy TicketPolicy --model=Ticket --no-interaction` |
| Run migrations | `php artisan migrate` |
| Fresh DB + seed | `php artisan migrate:fresh --seed` |
| Cache clear | `php artisan optimize:clear` |

---

## PHP Standards

- Always use strict typing at the head of a `.php` file: `declare(strict_types=1);`.
- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

### Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

### Enums
- Keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

---

## Filament Standards

- Filament is a Server-Driven UI (SDUI) framework for Laravel. It allows developers to define user interfaces in PHP using structured configuration objects. It is built on top of Livewire, Alpine.js, and Tailwind CSS.
- Use the `search-docs` tool to get information from the official Filament documentation when needed.
- Utilize static `make()` methods for consistent component initialization.

### Artisan
- Use the Filament specific Artisan commands to create new files or components for Filament.
- Inspect the required options, always pass `--no-interaction`, and valid arguments for other options when applicable.

### Core Features
- **Actions**: Handle doing something within the application, often with a button or link.
- **Forms**: Dynamic forms rendered within other features, such as resources, action modals, table filters.
- **Infolists**: Read-only lists of data.
- **Notifications**: Flash notifications displayed to users within the application.
- **Panels**: The top-level container that can include all other features.
- **Resources**: Static classes that are used to build CRUD interfaces for Eloquent models.
- **Tables**: Interactive tables with filtering, sorting, pagination.
- **Widgets**: Small components included within dashboards for displaying data.

### Testing
- Test Filament functionality for user satisfaction.
- Ensure that you are authenticated to access the application within the test.
- Filament uses Livewire, so start assertions with `livewire()` or `Livewire::test()`.

---

## Laravel Standards

### Do Things the Laravel Way
- Use `php artisan make:` commands to create new files.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input.

### Database
- Always use proper Eloquent relationship methods with return type hints.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`.
- Generate code that prevents N+1 query problems by using eager loading.

### Model Creation
- When creating new models, create useful factories and seeders for them too.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum).

### Configuration
- Use environment variables only in configuration files.
- Never use the `env()` function directly outside of config files.

### Laravel Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available.

### Database Migrations
- When modifying a column, the migration must include all of the attributes that were previously defined on the column.

---

## Livewire Standards

### Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops.
- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects.
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend. Always validate form data, and run authorization checks in Livewire actions.

### Key Changes From Previous Versions
- Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
- Components use the `App\Livewire` namespace (not `App\Http\Livewire`).
- Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

---

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages.
- Make new Volt components using `php artisan make:volt [name] [--test]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components.
- Components use the `@volt` directive.
- Check existing Volt components to determine if they're functional or class based.

---

## Laravel Pint Code Formatter

- Run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

---

## PHPUnit Standards

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes.
- Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName`.

---

## Tailwind Standards

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Extract repeated patterns into components that match the project's conventions.
- Think through class placement, order, priority, and defaults.
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

---

## Test Enforcement

- Every change must be programmatically tested.
- Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed.

---

## Laravel Boost

Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

### Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

### URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

### Tinker / Debugging
- Use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

### Reading Browser Logs
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

### Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches.
- This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start.
- Do not add package names to queries - package information is already shared.

### Available Search Syntax
1. Simple Word Searches with auto-stemming
2. Multiple Words (AND Logic)
3. Quoted Phrases (Exact Position)
4. Mixed Queries
5. Multiple Queries

---

## Conventions

- Follow all existing code conventions used in this application.
- When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods.
- Check for existing components to reuse before writing a new one.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- Only create documentation files if explicitly requested by the user.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2.29
- filament/filament (FILAMENT) - v4
- laravel/framework (LARAVEL) - v12
- laravel/mcp (MCP) - v0
- laravel/prompts (PROMPTS) - v0
- laravel/pulse (PULSE) - v1
- laravel/reverb (REVERB) - v1
- laravel/sanctum (SANCTUM) - v4
- laravel/socialite (SOCIALITE) - v5
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- larastan/larastan (LARASTAN) - v3
- laravel/breeze (BREEZE) - v2
- laravel/pint (PINT) - v1
- laravel/telescope (TELESCOPE) - v5
- phpunit/phpunit (PHPUNIT) - v11
- laravel-echo (ECHO) - v2
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use strict typing at the head of a `.php` file: `declare(strict_types=1);`.
- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== mcp/core rules ===

## Laravel MCP

- MCP (Model Context Protocol) is very new. You must use the `search-docs` tool to get documentation for how to write and test Laravel MCP servers, tools, resources, and prompts effectively.
- MCP servers need to be registered with a route or handle in `routes/ai.php`. Typically, they will be registered using `Mcp::web()` to register a HTTP streaming MCP server.
- Servers are very testable - use the `search-docs` tool to find testing instructions.
- Do not run `mcp:start`. This command hangs waiting for JSON RPC MCP requests.
- Some MCP clients use Node, which has its own certificate store. If a user tries to connect to their web MCP server locally using https://, it could fail due to this reason. They will need to switch to http:// during local development.


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@volt` directive.
- You must check existing Volt components to determine if they're functional or class based. If you can't detect that, ask the user which they prefer before writing a Volt component.

### Volt Functional Component Example

<code-snippet name="Volt Functional Component Example" lang="php">
@volt
<?php
use function Livewire\Volt\{state, computed};

state(['count' => 0]);

$increment = fn () => $this->count++;
$decrement = fn () => $this->count--;

$double = computed(fn () => $this->count * 2);
?>

<div>
    <h1>Count: {{ $count }}</h1>
    <h2>Double: {{ $this->double }}</h2>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
</div>
@endvolt
</code-snippet>


### Volt Class Based Component Example
To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:


<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component {
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
} ?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>


### Testing Volt & Volt Components
- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
</code-snippet>


<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});
</code-snippet>


### Common Patterns


<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<!-- HTML / UI Here -->
</code-snippet>

<code-snippet name="Real-Time Search With Volt" lang="php">
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search..."
    />
</code-snippet>

<code-snippet name="Loading States With Volt" lang="php">
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.
<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
