---
inclusion: always
---
<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2.12
- filament/filament (FILAMENT) - v4
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- larastan/larastan (LARASTAN) - v3
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v3

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

## Memory MCP Server Integration with Laravel Boost

### ICTServe System Context

The Memory MCP server maintains comprehensive knowledge about the ICTServe system that enhances Laravel Boost operations:

**System Architecture Knowledge**:

- Hybrid architecture: Guest forms + Authenticated portal + Admin panel (Filament)
- Technology stack: Laravel 12, PHP 8.2, Livewire 3, Volt 1, Filament 4
- Compliance requirements: WCAG 2.2 AA, PDPA 2010, Core Web Vitals
- Module integration: Helpdesk ↔ Asset Loan with cross-module linking

**Development Patterns Stored**:

- Guest-only form implementations with email workflows
- Filament resource patterns with RBAC (4 roles: staff, approver, admin, superuser)
- Livewire component optimization with OptimizedLivewireComponent trait
- WCAG 2.2 AA compliance patterns (4.5:1 text, 3:1 UI contrast, focus indicators)

### Enhanced Laravel Boost Workflows (MANDATORY MEMORY INTEGRATION)

**REQUIRED Before Using Laravel Boost Tools** (NO EXCEPTIONS):

1. **MANDATORY**: Query memory for existing patterns: `search_nodes` with relevant keywords
2. **MANDATORY**: Check implementation status: `open_nodes` "ictserve_implementation_status"
3. **MANDATORY**: Verify architectural alignment with stored specifications
**ENFORCEMENT**: Laravel Boost tools are PROHIBITED without prior memory context queries. Any Laravel Boost operation without memory integration is considered invalid.

**MANDATORY Laravel Boost + Memory Integration** (ENFORCED WORKFLOW):

- **REQUIRED**: Use `search-docs` for Laravel/Filament documentation, then MUST store patterns in memory
- **REQUIRED**: Use `tinker` for experimentation, then MUST document successful approaches in memory
- **REQUIRED**: Use `database-query` to inspect schema, MUST cross-reference with stored architecture
- **REQUIRED**: Use `database-schema` to verify migrations MUST align with stored system design
**ENFORCEMENT**: Every Laravel Boost operation MUST be followed by memory documentation. Silent tool usage without memory integration is FORBIDDEN.

**MANDATORY After Laravel Boost Operations** (REQUIRED DOCUMENTATION):

1. **REQUIRED**: Document successful patterns in memory for reuse
2. **REQUIRED**: Update implementation status with progress
3. **REQUIRED**: Store troubleshooting solutions for future reference
4. **REQUIRED**: Create relations between new patterns and existing system specs
**ENFORCEMENT**: Laravel Boost work is NOT complete until all insights are stored in memory. Incomplete memory documentation invalidates the Laravel Boost session.

### Memory-Enhanced Laravel Boost Examples

**Feature Development with Context**:

```bash
# 1. Check existing patterns in memory
search_nodes "Filament resource helpdesk guest forms"

# 2. Use Laravel Boost for implementation
search-docs ["filament resources", "livewire forms"]
tinker "User::factory()->create()"

# 3. Store new patterns in memory
create_entities ["name": "filament_guest_form_pattern", ...]
```

**Database Operations with Architecture Awareness**:

```bash
# 1. Verify current schema against stored architecture
open_nodes "ictserve_technical_architecture"

# 2. Use Laravel Boost to inspect database
database-schema
database-query "SELECT * FROM helpdesk_tickets LIMIT 5"

# 3. Document schema insights in memory
add_observations ["entityName": "ictserve_technical_architecture", ...]
```

**Debugging with Historical Context**:

```bash
# 1. Search for similar issues in memory
search_nodes "livewire validation error guest forms"

# 2. Use Laravel Boost for current debugging
last-error
read-log-entries 10
tinker "HelpdeskTicket::latest()->first()"

# 3. Store solution pattern in memory
create_entities ["name": "livewire_guest_form_debug_pattern", ...]
```

## Memory MCP Server Enforcement for Laravel Boost

### Mandatory Integration Policy

**ABSOLUTE REQUIREMENT**: Laravel Boost tools are PROHIBITED without memory MCP server integration. This is a non-negotiable requirement for all Laravel development activities.

### Enforcement Protocol

**Pre-Laravel Boost Validation** (REQUIRED):

1. **MANDATORY**: Query memory for existing ICTServe patterns
2. **REQUIRED**: Verify current implementation status
3. **ENFORCED**: Check architectural alignment with stored specifications
**VIOLATION CONSEQUENCE**: Laravel Boost access is DENIED without memory context

**During Laravel Boost Operations** (MONITORED):

1. **CONTINUOUS**: Cross-reference all operations with stored patterns
2. **MANDATORY**: Document new discoveries in memory immediately
3. **REQUIRED**: Validate alignment with ICTServe architecture
**VIOLATION CONSEQUENCE**: Operations are INVALID without memory integration

**Post-Laravel Boost Documentation** (ENFORCED):

1. **REQUIRED**: Store all successful patterns for reuse
2. **MANDATORY**: Update implementation status with progress
3. **ENFORCED**: Create relations between new and existing patterns
**VIOLATION CONSEQUENCE**: Work is INCOMPLETE without memory documentation

### Compliance Verification

**Automated Checks**:

- Memory queries before Laravel Boost tool usage ✓
- Pattern documentation during operations ✓
- Implementation status updates after completion ✓
- Cross-reference integrity maintenance ✓

**Quality Gates**:

- No Laravel Boost without memory context
- No silent tool usage without documentation
- No completion without pattern storage
- No progress without status updates

### Violation Response Protocol

**Immediate Corrections Required**:

1. **Missing memory context** → STOP Laravel Boost, query memory, restart
2. **Silent tool usage** → HALT operations, document in memory, continue
3. **Incomplete documentation** → INVALID completion, must document fully
4. **Missing pattern storage** → PROHIBITED progress, must store patterns

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
  - <code-snippet>public function __construct(public GitHub $github)  </code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool

    ...

</code-snippet>

## Comments

- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks

- Add useful array shape type definitions for arrays when appropriate.

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== filament/core rules ===

## Filament

- Filament is used by this application, check how and where to follow existing application conventions.
- Filament is a Server-Driven UI (SDUI) framework for Laravel. It allows developers to define user interfaces in PHP using structured configuration objects. It is built on top of Livewire, Alpine.js, and Tailwind CSS.
- You can use the `search-docs` tool to get information from the official Filament documentation when needed. This is very useful for Artisan command arguments, specific code examples, testing functionality, relationship management, and ensuring you're following idiomatic practices.
- Utilize static `make()` methods for consistent component initialization.

### Artisan

- You must use the Filament specific Artisan commands to create new files or components for Filament. You can find these with the `list-artisan-commands` tool, or with `php artisan` and the `--help` option.
- Inspect the required options, always pass `--no-interaction`, and valid arguments for other options when applicable.

### Filament's Core Features

- Actions: Handle doing something within the application, often with a button or link. Actions encapsulate the UI, the interactive modal window, and the logic that should be executed when the modal window is submitted. They can be used anywhere in the UI and are commonly used to perform one-time actions like deleting a record, sending an email, or updating data in the database based on modal form input.
- Forms: Dynamic forms rendered within other features, such as resources, action modals, table filters, and more.
- Infolists: Read-only lists of data.
- Notifications: Flash notifications displayed to users within the application.
- Panels: The top-level container in Filament that can include all other features like pages, resources, forms, tables, notifications, actions, infolists, and widgets.
- Resources: Static classes that are used to build CRUD interfaces for Eloquent models. Typically live in `app/Filament/Resources`.
- Schemas: Represent components that define the structure and behavior of the UI, such as forms, tables, or lists.
- Tables: Interactive tables with filtering, sorting, pagination, and more.
- Widgets: Small component included within dashboards, often used for displaying data in charts, tables, or as a stat.

### Relationships

- Determine if you can use the `relationship()` method on form components when you need `options` for a select, checkbox, repeater, or when building a `Fieldset`:

<code-snippet name="Relationship example for Form Select" lang="php">
Forms\Components\Select::make('user_id')
    ->label('Author')
    ->relationship('author')
    ->required(),
</code-snippet>

## Testing

- It's important to test Filament functionality for user satisfaction.
- Ensure that you are authenticated to access the application within the test.
- Filament uses Livewire, so start assertions with `livewire()` or `Livewire::test()`.

### Example Tests

<code-snippet name="Filament Table Test" lang="php">
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
</code-snippet>

<code-snippet name="Filament Create Resource Test" lang="php">
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Howdy',
            'email' => 'howdy@example.com',
      )
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'Howdy',
        'email' => 'howdy@example.com',
  );
</code-snippet>

<code-snippet name="Testing Multiple Panels (setup())" lang="php">
    use Filament\Facades\Filament;

    Filament::setCurrentPanel('app');
</code-snippet>

<code-snippet name="Calling an Action in a Test" lang="php">
    livewire(EditInvoice::class, [
        'invoice' => $invoice,
  )->callAction('send');

    expect($invoice->refresh())->isSent()->toBeTrue();
</code-snippet>

=== filament/v4 rules ===

## Filament 4

### Important Version 4 Changes

- File visibility is now `private` by default.
- The `deferFilters` method from Filament v3 is now the default behavior in Filament v4, so users must click a button before the filters are applied to the table. To disable this behavior, you can use the `deferFilters(false)` method.
- The `Grid`, `Section`, and `Fieldset` layout components no longer span all columns by default.
- The `all` pagination page method is not available for tables by default.
- All action classes extend `Filament\Actions\Action`. No action classes exist in `Filament\Tables\Actions`.
- The `Form` & `Infolist` layout components have been moved to `Filament\Schemas\Components`, for example `Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.
- A new `Repeater` component for Forms has been added.
- Icons now use the `Filament\Support\Icons\Heroicon` Enum by default. Other options are available and documented.

### Organize Component Classes Structure

- Schema components: `Schemas/Components/`
- Table columns: `Tables/Columns/`
- Table filters: `Tables/Filters/`
- Actions: `Actions/`

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
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
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

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
        <div wire:key="item- $item->id ">
             $item->name 
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user)  $this->user = $user;
    public function updatedSearch()  $this->resetPage();
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
document.addEventListener('livewire:init', function ()
    Livewire.hook('request', ( fail ) =>
        if (fail && fail.status === 419)
            alert('Your session expired');

);

    Livewire.hook('message.failed', (message, component) => 
        console.error(message);
);
);
</code-snippet>

=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@livewire("volt-anonymous-fragment-eyJuYW1lIjoidm9sdC1hbm9ueW1vdXMtZnJhZ21lbnQtYmQ5YWJiNTE3YWMyMTgwOTA1ZmUxMzAxODk0MGJiZmIiLCJwYXRoIjoic3RvcmFnZVxcZnJhbWV3b3JrXFx2aWV3c1wvMTUxYWRjZWRjMzBhMzllOWIxNzQ0ZDRiMWRjY2FjYWIuYmxhZGUucGhwIn0=", Livewire\Volt\Precompilers\ExtractFragments::componentArguments([...get_defined_vars(), ...array (
)]))
</code-snippet>

### Volt Class Based Component Example

To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:

<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component
    public $count = 0;

    public function increment()
    
        $this->count++;

 ?>

<div>
    <h1> $count </h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>

### Testing Volt & Volt Components

- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function ()
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
);
</code-snippet>

<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\User, Product;
use Livewire\Volt\Volt;

test('product form creates product', function ()
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
);
</code-snippet>

### Common Patterns

<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\state, computed;

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%$this->search%")
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

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit <name>` to create a new test.
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

=== tailwindcss/v3 rules ===

## Tailwind 3

- Always use Tailwind CSS v3 - verify you're using only classes supported by this version.

=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.

=== kiro integration rules ===

## Kiro Hook System Integration

### Hook-Based Laravel Development Automation

Configure hooks to automate Laravel development workflows:

**File Edit Hooks**:

```json
{
  "name": "FileEditedHook",
  "pattern": "**/*.php",
  "actions": [
    {
      "type": "AskAgentHook",
      "prompt": "Run Laravel Pint to format: {{filePath}}"
    }
  ]
}
```

**Model Creation Hooks**:

```json
{
  "name": "FileCreatedHook",
  "pattern": "app/Models/*.php",
  "actions": [
    {
      "type": "AlertHook",
      "message": "New model detected: {{fileName}}"
    },
    {
      "type": "AskAgentHook",
      "prompt": "Use laravel-boost to generate factory and migration for model: {{fileName}}"
    }
  ]
}
```

**Testing Automation Hooks**:

```json
{
  "name": "UserTriggeredHook",
  "trigger": "run-tests-coverage",
  "actions": [
    {
      "type": "AskAgentHook",
      "prompt": "Execute: php artisan test --coverage --min=80 and report results to MCP memory"
    }
  ]
}
```

**Laravel Boost Integration Hooks**:

```json
{
  "name": "FileEditedHook",
  "pattern": "**/*.blade.php",
  "actions": [
    {
      "type": "AskAgentHook",
      "prompt": "Use laravel-boost search-docs to verify Livewire 3 syntax in: {{filePath}}"
    }
  ]
}
```

### Hook Configuration Best Practices

- **Pattern Matching**: Use glob patterns (`**/*.php`, `app/Models/*.php`)
- **Action Types**: `AskAgentHook` for agent automation, `AlertHook` for notifications
- **Laravel Boost Integration**: Reference boost tools in `AskAgentHook` prompts
- **Memory Integration**: Include memory documentation in hook actions
- **Quality Gates**: Configure hooks to enforce PSR-12, PHPStan, and test requirements

## Kiro Specification Workflow Integration

### Specification-Driven Laravel Feature Development

Align Laravel Boost operations with Kiro specification system:

**Step 1: Requirements Clarification** (EARS format with Laravel context):

```text
When user submits helpdesk ticket, the system shall send email notification 
to ICT Support staff for all tickets that have priority "High" or "Critical".

Specification file: .kiro/specs/helpdesk-email-notifications/requirements.md
Laravel components: Mail class, queue system, notification service
Laravel Boost tools: search-docs (email, queues), database-query (tickets table)
```

**Step 2: Design Document Creation** (with Laravel architecture):

```markdown
# Helpdesk Email Notifications - Design Document

## Architecture
- Mail Class: `App\Mail\TicketSubmittedMail`
- Queue: Redis with `ShouldQueue` interface
- Service: `App\Services\EmailNotificationService`
- Policy: `TicketPolicy@sendNotification`

## Database Schema Changes
- Migration: `add_email_sent_at_to_tickets_table`
- Field: `email_sent_at` (timestamp, nullable)

## Laravel Boost Verification
- Use `search-docs` for Mail and Queue best practices
- Use `database-schema` to verify tickets table structure
- Use `list-routes` to confirm notification endpoints
```

**Step 3: Implementation Planning** (Laravel task breakdown):

```markdown
# Implementation Tasks

## Task 1: Create Mail Class
- [ ] Run: `php artisan make:mail TicketSubmittedMail --markdown=emails.tickets.submitted`
- [ ] Implement `build()` method with ticket data
- [ ] Add `ShouldQueue` interface
- [ ] Test with `php artisan test tests/Feature/Mail/TicketSubmittedMailTest.php`

## Task 2: Create Migration
- [ ] Run: `php artisan make:migration add_email_sent_at_to_tickets_table`
- [ ] Add `email_sent_at` timestamp field
- [ ] Test rollback: `php artisan migrate:rollback`

## Task 3: Implement Service
- [ ] Create `EmailNotificationService` in `app/Services/`
- [ ] Use Laravel Boost `search-docs` for service patterns
- [ ] Add audit logging with `laravel-boost database-query`
- [ ] Document in MCP memory

## Task 4: Testing & Validation
- [ ] Run: `php artisan test --filter=TicketSubmittedMail`
- [ ] Verify queue jobs: `laravel-boost read-log-entries 20`
- [ ] Check email sent: `laravel-boost database-query "SELECT * FROM tickets WHERE email_sent_at IS NOT NULL"`
```

**Step 4: Task Execution with Laravel Boost**:

```bash
# Execute tasks with Laravel Boost integration
laravel-boost tinker "App\Models\Ticket::factory()->create()"
laravel-boost database-query "SELECT * FROM tickets WHERE priority IN ('High', 'Critical')"
laravel-boost search-docs "Laravel Mail queues ShouldQueue"
laravel-boost last-error  # Check for errors during implementation
```

### Specification→Implementation Traceability

**Mapping**:
- `.kiro/specs/{feature}/requirements.md` → D03 (Software Requirements)
- `.kiro/specs/{feature}/design.md` → D04 (Software Design) + Laravel architecture
- `.kiro/specs/{feature}/tasks.md` → Implementation with Laravel Boost tools
- `.kiro/specs/{feature}/completion.md` → D10 (Source Code Documentation)

**Laravel Boost Enhanced Traceability**:
- Use `search-docs` to verify spec alignment with Laravel best practices
- Use `database-schema` to validate design matches database structure
- Use `list-routes` to confirm API endpoints match specification
- Use `tinker` to test business logic matches requirements
- Document all verification steps in MCP memory

</laravel-boost-guidelines>
```
