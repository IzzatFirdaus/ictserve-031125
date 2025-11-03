---
applyTo: '**'
---

# Filament Admin Instructions

Purpose  
Defines setup, standards, traceability, security, accessibility and operational rules for the Filament admin panel, its resources, pages, and widgets used by ICTServe. This file is normative for developers, maintainers, testers and DevOps who add or modify admin functionality. Refer to D00 System Overview, D03 Requirements, D04 Design, D10 Source Code, D11 Technical Design and D12–D14 UI/UX documents for source-of-truth requirements and designs.

Scope  
Applies to all Filament-related code and configuration in this repository, including:
Mandatory rules (summary)
- Filament configuration and panel provider must live at `app/Providers/Filament/AdminPanelProvider.php`. Use that provider to configure branding, navigation, pages and middleware. See: https://filamentphp.com/docs/4.x/getting-started/
- Filament discovery expects files under `app/Filament/Resources`, `app/Filament/Pages`, `app/Filament/Widgets`. Place resources/pages/widgets there — do not add new top-level directories for Filament items. See: https://filamentphp.com/docs/4.x/resources/overview/
- Use amber as the primary Filament color and enforce private file visibility by default (refer D14).
- Filament Actions should extend `Filament\Actions\Action` and be registered via resource/page methods (`getActions()` / `form()` / `table()`). See: https://filamentphp.com/docs/4.x/tables/actions/
- For relational fields prefer `->relationship('relationName')` and explicit Eloquent relationship return types in models. See: https://filamentphp.com/docs/4.x/components/select
- Add unit/feature tests for Filament resources using Livewire or Volt test helpers. Tests must exercise authorization and typical interactions (create/edit/delete, relation selects, file uploads).
- Ensure all admin UI changes meet WCAG 2.2 AA and MOTAC style guide (D12–D14). Include accessibility attributes (`aria-*`) and keyboard operability.
- Protect admin routes with appropriate middleware and policies. Prefer Policies and Gates over inline checks.
- Secrets (API keys, tokens) must not be committed — use GitHub Secrets or Vault and refer to them in workflows.
- All admin automation (workflows, scripts) must be auditable and logged per automation.instructions.md.

### Resources
- Resources are static classes that build CRUD interfaces for Eloquent models. They automatically generate List, Create, Edit, View pages with forms and tables.
- Define resources in `app/Filament/Resources/` with methods like `form()`, `table()`, `getRelations()`, `getPages()`.
- Use `php artisan make:filament-resource ResourceName` to scaffold resources.
- Resources support relationships, actions, filters, and bulk operations.

### Forms
- Forms are dynamic form components rendered within resources, actions, table filters, and more.
- Use form fields like `TextInput`, `Select`, `Textarea`, `FileUpload`, `DatePicker`, `Toggle`, `Repeater`.
- Add validation with methods like `required()`, `email()`, `maxLength()`, `unique()`.
- Forms support reactivity with `live()` for real-time updates and `afterStateUpdated()` for dynamic behavior.
- Use `->relationship()` for select fields that populate from related models.

### Tables
- Tables provide interactive data display with filtering, sorting, pagination, and bulk actions.
- Define table columns using `TextColumn`, `ImageColumn`, `BooleanColumn`, `SelectColumn`, etc.
- Add filters with `Filter` classes and actions with `Action` classes.
- Support for bulk actions, searchable columns, and sortable columns.
- Tables can be customized with custom queries and conditional visibility.

### Actions
- Actions handle doing something within the application, often with buttons or links.
- Actions encapsulate UI (modal windows), logic, and form submissions.
- Use `Action::make('name')` with methods like `label()`, `icon()`, `color()`, `action()`, `requiresConfirmation()`.
- Actions can be used in tables, forms, pages, and resource headers.
- Support for bulk actions and conditional visibility.

Standards & References (mandatory)
- Official Filament Documentation (https://filamentphp.com/docs/4.x/)
- D00–D14 documentation set (traceability to requirements/design)
- WCAG 2.2 Level AA (accessibility)
- PSR-12, Laravel 12 best practices
- ISO/IEC/IEEE 12207, 15288, 29148, ISO 9001 (governance)
- BPM/MOTAC internal policies (security, change management)

Traceability requirements
- Every new admin feature (resource, page, widget, custom action) MUST include trace links to:
  - Requirement IDs (D03) — e.g., SRS-FR-012
  - Design references (D04/D11) — section/diagram IDs
  - Test plan / integration spec (D07/D08) when relevant
- Include a `trace` metadata block or comment at the top of new/modified files with these references and an author.
  - Example header comment for PHP/YAML:
    ```php
    // name: PositionResource
    // description: Filament resource for managing positions
    // author: dev-team@motac.gov.my
    // trace: SRS-FR-014; D04 §4.3; D11 §7
    // last-updated: 2025-10-21
    ```

## Installation and Setup
- Install Filament via Composer: `composer require filament/filament`
- Publish configuration: `php artisan filament:install --panels`
- Create admin panel provider: `php artisan make:filament-panel admin`
- Configure panel in `app/Providers/Filament/AdminPanelProvider.php` with branding, navigation, middleware
- Set up authentication: Filament uses Laravel's built-in auth with customizable login/logout routes
- Database: Ensure models have proper relationships and policies for authorization
- File uploads: Configure disk storage and visibility settings (private by default in v4)
- See: https://filamentphp.com/docs/4.x/introduction/installation/

## Key Features
- **Server-Driven UI (SDUI)**: Define UIs entirely in PHP using structured configuration objects
- **Built on Livewire + Alpine.js + Tailwind CSS**: Reactive components without custom JavaScript
- **Modular Packages**: Core packages include forms, tables, infolists, actions, notifications, widgets
- **Auto-generated CRUD**: Resources automatically generate List, Create, Edit pages with forms and tables
- **Extensible**: Hundreds of community plugins and official integrations
- **Testing Support**: Built-in testing utilities for PHPUnit/Pest
- **Accessibility**: WCAG 2.2 AA compliant with keyboard navigation and screen reader support
- **Multi-panel Support**: Multiple admin panels in single application
- **Real-time Updates**: Livewire-powered reactive interfaces
- **File Management**: Integrated file upload handling with validation and storage

File & documentation requirements
- Each Filament resource/page/widget file MUST contain a top metadata block (comment) with:
  - name, description, author/team, trace refs, last-updated
- Document complex resources/pages in `docs/filament/` with:
  - purpose, fields, required permissions, required secrets, sample screenshots, rollback steps
- Update CONTRIBUTING / PR templates to require: trace IDs, accessibility checklist, tests added, and reviewer assignments for security and UI/UX.

Step-by-step workflow for adding/updating Filament admin features
1. Review D03/D04/D11 and update RTM (requirements traceability) if new or changed requirement applies.
2. Scaffold resource/page/widget in `app/Filament/...` (follow naming & discovery conventions).
3. Implement model changes (add `$fillable`, `protected function casts(): array`, SoftDeletes/Auditable traits) and ensure relationships are typed.
4. Add top metadata `trace` comment to new files.
5. Add UI with accessible labels, error handling and keyboard focus management (refer D12–D14).
6. Add Livewire/Volt tests exercising UI and authorization with `->actingAs($user)` where relevant.
7. Run local checks: `vendor/bin/phpstan`, `vendor/bin/pint --test`, `php artisan test`, `npm run build` (if frontend changes).
8. Create PR:
   - Conventional commit-style title
   - Fill PR template with traceability IDs, tests, docs and required reviewers (Dev + Security + Accessibility)
9. After merge:
   - Update RTM/D03 and documentation under `docs/filament/`
   - Monitor initial admin usage and logs for 48 hours; capture any runtime exceptions

Testing & validation
- Tests:
  - Use `Livewire::test(ResourcePage::class)` or `Volt::test()` for components where applicable.
  - Tests must cover authorization (policy/gate), validation errors, relation selection, and file uploads.
- Accessibility:
  - Run axe/Lighthouse checks on admin pages during CI; fix any errors marked high or critical.
  - Manual keyboard navigation and screen-reader smoke tests for new screens (NVDA/VoiceOver).
- CI:
  - Add jobs in `.github/workflows/ci.yml` to run phpstan, pint, phpunit and accessibility scans on PRs.

Examples

1) Minimal Filament Resource skeleton (app/Filament/Resources/PositionResource.php). See: https://filamentphp.com/docs/4.x/resources/overview/
```php
<?php
// name: PositionResource
// description: Manage positions used by users and loans
// author: dev-team@motac.gov.my
// trace: SRS-FR-014; D04 §4.3; D11 §7
// last-updated: 2025-10-21

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\Position;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->label('Nama Jawatan'),
            TextInput::make('grade')->label('Gred'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama Jawatan')->searchable(),
            TextColumn::make('grade')->label('Gred'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
        ];
    }
}
```

2) Register admin panel provider (app/Providers/Filament/AdminPanelProvider.php) — key points. See: https://filamentphp.com/docs/4.x/getting-started/
- Ensure `->login()` is set, theme/primary color is amber, discovery is used.
- Set file visibility defaults where needed.

3) Example action using Filament Action. See: https://filamentphp.com/docs/4.x/tables/actions/
```php
Action::make('export')
    ->label('Export CSV')
    ->action(fn () => dispatch(new ExportPositionsJob()))
    ->requiresConfirmation()
    ->color('secondary');
```

4) Form with various field types and validation. See: https://filamentphp.com/docs/4.x/forms/overview/
```php
public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->label('Name'),
        TextInput::make('email')
            ->email()
            ->unique(ignoreRecord: true)
            ->label('Email'),
        Select::make('department_id')
            ->relationship('department', 'name')
            ->required()
            ->label('Department'),
        DatePicker::make('birth_date')
            ->maxDate(now())
            ->label('Birth Date'),
        FileUpload::make('avatar')
            ->image()
            ->directory('avatars')
            ->visibility('private')
            ->label('Avatar'),
        Toggle::make('is_active')
            ->default(true)
            ->label('Active'),
    ]);
}
```

5) Table with filters, actions, and bulk operations. See: https://filamentphp.com/docs/4.x/tables/overview/
```php
public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('name')
            ->searchable()
            ->sortable()
            ->label('Name'),
        TextColumn::make('email')
            ->searchable()
            ->label('Email'),
        TextColumn::make('department.name')
            ->label('Department'),
        BooleanColumn::make('is_active')
            ->label('Active'),
    ])
    ->filters([
        Filter::make('active')
            ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
    ])
    ->actions([
        EditAction::make(),
        DeleteAction::make(),
    ])
    ->bulkActions([
        BulkActionGroup::make([
            DeleteBulkAction::make(),
        ]),
    ]);
}
```

2) Register admin panel provider (app/Providers/Filament/AdminPanelProvider.php) — key points. See: https://filamentphp.com/docs/4.x/getting-started/
- Ensure `->login()` is set, theme/primary color is amber, discovery is used.
- Set file visibility defaults where needed.

3) Example action using Filament Action. See: https://filamentphp.com/docs/4.x/tables/actions/
```php
Action::make('export')
    ->label('Export CSV')
    ->action(fn () => dispatch(new ExportPositionsJob()))
    ->requiresConfirmation()
    ->color('secondary');
```

Security & deployment
- Filament admin must only be accessible on intranet or via secure VPN; enforce middleware and IP restrictions if required.
- Use environment-based protective configuration: enable debug only in non-prod.
- Protect Filament sensitive actions with policies and require approvals for production-critical jobs.
- For production deployments, follow D11/D01 change-management and update RTM for automation changes.

Accessibility & UI/UX
- Follow D12–D14: accessible labels, focus management, color contrast, keyboard navigation, and ARIA attributes.
- Filament forms and tables must have clear labels; avoid icon-only action buttons without accessible text or `aria-label`.
- Provide Bahasa Melayu primary UI text with English secondary where required (see D15_LANGUAGE_MS_EN.md). For content in admin, default to Bahasa Melayu, include English hint text where beneficial for technical users.

PR & release checklist (add to PR description)
- [ ] Does this change add/modify Filament admin resources/pages/widgets?
  - [ ] Added `trace` metadata to files
  - [ ] Updated docs in `docs/filament/<feature>.md`
  - [ ] Tests added and passing (phpunit + Livewire/Volt)
  - [ ] Accessibility (axe/Lighthouse) scan results attached
  - [ ] Security review requested (if secrets/permissions/privileged actions)
  - [ ] Rollback steps documented (if DB migrations or prod-impacting)
  - [ ] RTM (D03) updated with mapping
  - [ ] Reviewers: @devops, @security, @accessibility (as applicable)
- [ ] CI checks (phpstan, pint, tests) passed

Operational notes
- Audit: Filament admin actions that change data must be visible in `audit_logs`. Ensure models use OwenIt\Auditing\Auditable where required.
- Logging: Important admin actions should emit structured logs for incident tracing.
- Backups: Ensure database and storage backup policies include admin data and uploaded files.
- Monitoring: Add alerting for repeated admin errors (Sentry / NewRelic).

Contacts & owners
- Filament / Admin owner: admin@motac.gov.my
- DevOps / Automation: devops@motac.gov.my
- Security / Compliance: security@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my

Appendices
- Official Filament Documentation References:
  - ### Introduction
    - Overview: https://filamentphp.com/docs/4.x/introduction/overview/ - Introduction to Filament as a Server-Driven UI framework built on Livewire, Alpine.js, and Tailwind CSS
    - Installation: https://filamentphp.com/docs/4.x/introduction/installation/ - Step-by-step installation guide for Filament packages and panel setup
    - Upgrade Guide: https://filamentphp.com/docs/4.x/upgrade-guide/ - Migration guides for upgrading between Filament versions
  - ### Getting Started
    - Getting Started: https://filamentphp.com/docs/4.x/getting-started/ - Basic setup and configuration for Filament admin panels
    - Resources: https://filamentphp.com/docs/4.x/getting-started/resources - Creating and configuring resources for CRUD operations
    - Widgets: https://filamentphp.com/docs/4.x/getting-started/widgets - Adding dashboard widgets and statistics
    - Custom Pages: https://filamentphp.com/docs/4.x/getting-started/custom-pages - Building custom admin pages beyond standard CRUD
  - ### Resources
    - Overview: https://filamentphp.com/docs/4.x/resources/overview/ - Complete guide to Filament resources for building admin interfaces
    - Forms: https://filamentphp.com/docs/4.x/resources/forms - Configuring forms within resources for data input
    - Tables: https://filamentphp.com/docs/4.x/resources/tables - Setting up tables for data display and management
    - Relations: https://filamentphp.com/docs/4.x/resources/relations - Managing relationships between resources
  - ### Schemas
    - Overview: https://filamentphp.com/docs/4.x/schemas/overview/ - Understanding schema components for building UIs
    - Layouts: https://filamentphp.com/docs/4.x/schemas/layouts/ - Layout components like Grid, Section, and Fieldset
    - Sections: https://filamentphp.com/docs/4.x/schemas/sections/ - Organizing content with section components
  - ### Components
    - Overview: https://filamentphp.com/docs/4.x/components/overview/ - Comprehensive component library reference
    - Select component: https://filamentphp.com/docs/4.x/components/select - Dropdown and relationship selection components
    - Input component: https://filamentphp.com/docs/4.x/components/input - Text input and form field components
    - Widget component: https://filamentphp.com/docs/4.x/components/widget/ - Dashboard widget components
  - ### Tables
    - Overview: https://filamentphp.com/docs/4.x/tables/overview/ - Interactive table components and configuration
    - Columns overview: https://filamentphp.com/docs/4.x/tables/columns/overview/ - Available column types and customization
    - Layout: https://filamentphp.com/docs/4.x/tables/layout/ - Table layout and responsive design options
    - Actions: https://filamentphp.com/docs/4.x/tables/actions/ - Row and bulk actions for tables
    - Summaries: https://filamentphp.com/docs/4.x/tables/summaries/ - Table summary and aggregation features
    - Custom data: https://filamentphp.com/docs/4.x/tables/custom-data/ - Using custom queries and data sources
    - Filter query builder: https://filamentphp.com/docs/4.x/tables/filters/query-builder/ - Advanced filtering with query builders
  - ### Navigation
    - Clusters: https://filamentphp.com/docs/4.x/navigation/clusters/ - Grouping related resources and pages
- Testing Examples:
  - ### Filament Table Test
    ```php
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
    ```
  - ### Filament Create Resource Test
    ```php
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Howdy',
            'email' => 'howdy@example.com',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'Howdy',
        'email' => 'howdy@example.com',
    ]);
    ```
  - ### Testing Multiple Panels (setup())
    ```php
    use Filament\Facades\Filament;

    Filament::setCurrentPanel('app');
    ```
  - ### Calling an Action in a Test
    ```php
    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])->callAction('send');

    expect($invoice->refresh())->isSent()->toBeTrue();
    ```
- Version 4 Changes Summary:
  - File visibility is now `private` by default.
  - The `deferFilters` method from Filament v3 is now the default behavior in Filament v4, so users must click a button before the filters are applied to the table. To disable this behavior, you can use the `deferFilters(false)` method.
  - The `Grid`, `Section`, and `Fieldset` layout components no longer span all columns by default.
  - The `all` pagination page method is not available for tables by default.
  - All action classes extend `Filament\Actions\Action`. No action classes exist in `Filament\Tables\Actions`.
  - The `Form` & `Infolist` layout components have been moved to `Filament\Schemas\Components`, for example `Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.
  - A new `Repeater` component for Forms has been added.
  - Icons now use the `Filament\Support\Icons\Heroicon` Enum by default. Other options are available and documented.
- See D11 Technical Design, D12 UI/UX Guide and D14 Style Guide for detailed UI, accessibility and design rules.
- Place resource-specific documentation under `docs/filament/<resource-name>.md` and add links to this file.

Notes
- This document is normative for Filament admin development in this repository. Any deviation that impacts security, privacy, or traceability requires formal approval through the change management process (D01 §9.3) and must be recorded in the RTM.
