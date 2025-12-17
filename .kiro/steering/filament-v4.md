# Filament v4.x Guidelines

## Overview

Filament v4.x is a major release with significant breaking changes and new features. This steering file provides comprehensive guidelines for working with Filament v4.1.10 in the ICTServe Laravel 12 application.

**Key Changes from v3**:

- File visibility is now `private` by default
- `deferFilters` is now default behavior for tables
- All action classes extend `Filament\Actions\Action`
- Schema components moved to `Filament\Schemas\Components`
- New `Repeater` component for forms
- Grid, Section, and Fieldset nnger span all columns by default

## Core Architecture

### Server-Driven UI (SDUI) Pattern

Filament v4 follows a Server-Driven UI approach where:

- UI is defined in PHP using structured configuration objects
- Components are rendered server-side with Livewire
- Alpine.js provides client-side interactivity
- Tailwind CSS handles styling

### Component Hierarchy

```text
Filament\Schemas\Schema
├── Forms\Components\*           # Form fields (input, validation)
├── Infolists\Components\*       # Read-only display components
├── Schemas\Components\*         # Layout components (Grid, Section, Tabs)
├── Actions\*                    # Interactive buttons with modals
└── Tables\*                     # Data tables with filtering/sorting
```

## Breaking Changes from v3

### File Visibility

**v4 Default**: File visibility is now `private` by default.

```php
// v3 (public by default)
FileUpload::make('document')

// v4 (private by default)
FileUpload::make('document')
    ->visibility('public') // Explicitly set if needed
```

### Table Filters

**v4 Default**: `deferFilters` is now default behavior.

```php
// v3 (filters applied immediately)
public function table(Table $table): Table
{
    return $table->filters([...]);
}

// v4 (filters deferred by default, require "Apply" button)
public function table(Table $table): Table
{
    return $table
        ->filters([...])
        ->deferFilters(false); // Disable if immediate filtering needed
}
```

### Action Classes

**v4 Change**: All actions extend `Filament\Actions\Action`.

```php
// v3
use Filament\Tables\Actions\Action;

// v4
use Filament\Actions\Action;
```

### Schema Components

**v4 Change**: Layout components moved to `Filament\Schemas\Components`.

```php
// v3
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

// v4
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
```

### Column Spanning

**v4 Change**: Grid, Section, and Fieldset no longer span all columns by default.

```php
// v3 (auto-span)
Section::make('Details')

// v4 (explicit spanning)
Section::make('Details')
    ->columnSpanFull() // Explicitly span full width
```

## Resource Development Patterns

### Resource Structure

```php
<?php
declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ExampleResource extends Resource
{
    protected static ?string $model = Example::class;
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    // ...
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                // ...
            ])
            ->filters([
                // Filters are deferred by default in v4
            ])
            ->deferFilters(false); // Disable if immediate filtering needed
    }
}
```

### ICTServe Hybrid Architecture Integration

**Guest + Authenticated + Admin Pattern**:

```php
class HelpdeskTicketResource extends Resource
{
    protected static ?string $model = HelpdeskTicket::class;
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Ticket Information')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    
                    Textarea::make('description')
                        ->required()
                        ->rows(4),
                    
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required(),
                    
                    // Hybrid architecture: nullable user_id for guest submissions
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->nullable()
                        ->visible(fn () => auth()->check()),
                ])
                ->columns(2)
                ->columnSpanFull(),
                
            Section::make('Contact Information')
                ->schema([
                    TextInput::make('contact_name')
                        ->required()
                        ->maxLength(255),
                    
                    TextInput::make('contact_email')
                        ->email()
                        ->required(),
                ])
                ->columns(2)
                ->visible(fn () => !auth()->check()), // Guest-only fields
        ]);
    }
}
```

## Form Components

### New v4 Components

**Repeater Component**:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('items')
    ->schema([
        TextInput::make('name')->required(),
        TextInput::make('quantity')->numeric(),
    ])
    ->minItems(1)
    ->maxItems(10)
    ->addActionLabel('Add Item')
    ->deleteAction(
        fn (Action $action) => $action->requiresConfirmation()
    )
```

### Form Validation Patterns

```php
use Filament\Forms\Components\TextInput;

TextInput::make('email')
    ->email()
    ->required()
    ->unique(User::class, 'email', ignoreRecord: true)
    ->validationMessages([
        'unique' => 'Alamat e-mel ini telah digunakan.', // Bahasa Melayu
    ])
```

### Relationship Handling

```php
use Filament\Forms\Components\Select;

Select::make('user_id')
    ->relationship('user', 'name')
    ->searchable()
    ->preload()
    ->createOptionForm([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
    ])
    ->createOptionAction(
        fn (Action $action) => $action->modalWidth('lg')
    )
```

## Table Components

### Advanced Filtering

```php
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Schemas\Components\Section;

public function table(Table $table): Table
{
    return $table
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'open' => 'Terbuka',
                    'in_progress' => 'Dalam Proses',
                    'resolved' => 'Diselesaikan',
                ])
                ->multiple(),
                
            Filter::make('created_at')
                ->form([
                    DatePicker::make('from'),
                    DatePicker::make('until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                }),
        ])
        ->filtersFormSchema(fn (array $filters): array => [
            Section::make('Filter Tiket')
                ->schema([
                    $filters['status'],
                    $filters['created_at'],
                ])
                ->columns(2)
                ->columnSpanFull(),
        ])
        ->deferFilters(); // v4 default behavior
}
```

### Custom Column Formatting

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

TextColumn::make('status')
    ->formatStateUsing(function (string $state): HtmlString {
        $colors = [
            'open' => 'bg-red-100 text-red-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-green-100 text-green-800',
        ];
        
        return new HtmlString(
            "<span class=\"px-2 py-1 rounded-full text-xs font-medium {$colors[$state]}\">
                " . __("statuses.{$state}") . "
            </span>"
        );
    })
    ->html()
```

## Actions & Modals

### Action Configuration

```php
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

// Custom action with modal
Action::make('approve')
    ->label('Luluskan')
    ->icon('heroicon-o-check')
    ->color('success')
    ->requiresConfirmation()
    ->modalHeading('Luluskan Permohonan')
    ->modalDescription('Adakah anda pasti untuk meluluskan permohonan ini?')
    ->modalSubmitActionLabel('Ya, Luluskan')
    ->action(function (Model $record) {
        $record->update(['status' => 'approved']);
        
        Notification::make()
            ->title('Permohonan diluluskan')
            ->success()
            ->send();
    })
    ->visible(fn (Model $record) => $record->status === 'pending')

// Bulk action
DeleteAction::make()
    ->requiresConfirmation()
    ->modalHeading('Padam Rekod Terpilih')
    ->modalDescription('Adakah anda pasti untuk memadam rekod yang dipilih?')
    ->successNotificationTitle('Rekod berjaya dipadam')
```

### Action Authorization

```php
Action::make('edit')
    ->authorize('update', $this->record)
    ->visible(fn () => auth()->user()->can('update', $this->record))
```

## Widgets & Dashboard

### Stats Widget

```php
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class HelpdeskStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;
    
    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        
        return [
            StatsOverviewWidget\Stat::make(
                label: 'Jumlah Tiket',
                value: HelpdeskTicket::query()
                    ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
                    ->count()
            )
            ->description('Tiket yang diterima')
            ->descriptionIcon('heroicon-m-ticket')
            ->color('primary'),
        ];
    }
}
```

### Chart Widget

```php
use Filament\Widgets\ChartWidget;

class TicketTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Tiket Bulanan';
    
    protected function getData(): array
    {
        $data = HelpdeskTicket::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
            
        return [
            'datasets' => [
                [
                    'label' => 'Tiket Diterima',
                    'data' => array_values($data),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
}
```

## Authorization & Policies

### Resource Authorization

```php
class HelpdeskTicketResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->can('viewAny', HelpdeskTicket::class);
    }
    
    public static function canCreate(): bool
    {
        return auth()->user()->can('create', HelpdeskTicket::class);
    }
    
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('update', $record);
    }
    
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete', $record);
    }
}
```

### Policy Integration

```php
// app/Policies/HelpdeskTicketPolicy.php
class HelpdeskTicketPolicy
{
    public function viewAny(?User $user): bool
    {
        // Allow guests to view (for hybrid architecture)
        return true;
    }
    
    public function view(?User $user, HelpdeskTicket $ticket): bool
    {
        // Guests can view their own tickets via token
        if (!$user) {
            return request()->hasValidSignature();
        }
        
        // Staff can view tickets assigned to them or all if admin
        return $user->hasRole(['admin', 'superuser']) || 
               $ticket->assigned_to === $user->id;
    }
    
    public function create(?User $user): bool
    {
        // Allow both guests and authenticated users
        return true;
    }
    
    public function update(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->hasRole(['admin', 'superuser']) || 
               $ticket->assigned_to === $user->id;
    }
}
```

## Testing Patterns

### Resource Testing

```php
use PHPUnit\Framework\Attributes\Test;
use Filament\Livewire\Resources\Pages\CreateRecord;
use Filament\Livewire\Resources\Pages\ListRecords;

class HelpdeskTicketResourceTest extends TestCase
{
    #[Test]
    public function it_can_create_ticket(): void
    {
        $user = User::factory()->create();
        
        livewire(CreateRecord::class, [
            'resource' => HelpdeskTicketResource::class,
        ])
        ->actingAs($user)
        ->fillForm([
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'category_id' => Category::factory()->create()->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();
        
        $this->assertDatabaseHas('helpdesk_tickets', [
            'title' => 'Test Ticket',
            'user_id' => $user->id,
        ]);
    }
    
    #[Test]
    public function it_can_list_tickets(): void
    {
        $tickets = HelpdeskTicket::factory()->count(3)->create();
        
        livewire(ListRecords::class, [
            'resource' => HelpdeskTicketResource::class,
        ])
        ->assertCanSeeTableRecords($tickets);
    }
    
    #[Test]
    public function it_can_filter_tickets_by_status(): void
    {
        $openTickets = HelpdeskTicket::factory()->count(2)->create(['status' => 'open']);
        $closedTickets = HelpdeskTicket::factory()->count(3)->create(['status' => 'closed']);
        
        livewire(ListRecords::class, [
            'resource' => HelpdeskTicketResource::class,
        ])
        ->filterTable('status', 'open')
        ->assertCanSeeTableRecords($openTickets)
        ->assertCanNotSeeTableRecords($closedTickets);
    }
}
```

### Action Testing

```php
#[Test]
public function it_can_approve_ticket(): void
{
    $ticket = HelpdeskTicket::factory()->create(['status' => 'pending']);
    $user = User::factory()->create();
    
    livewire(EditRecord::class, [
        'resource' => HelpdeskTicketResource::class,
        'record' => $ticket->getRouteKey(),
    ])
    ->actingAs($user)
    ->callAction('approve')
    ->assertNotified();
    
    $this->assertEquals('approved', $ticket->fresh()->status);
}
```

## Performance Optimization

### Eager Loading

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['user', 'category', 'comments.user'])
        ->withCount(['comments', 'attachments']);
}
```

### Bulk Actions Performance

```php
use Filament\Actions\DeleteBulkAction;

DeleteBulkAction::make()
    ->chunkSelectedRecords(250) // Process in chunks
    ->fetchSelectedRecords(false) // Skip loading into memory for better performance
```

### Query Optimization

```php
TextColumn::make('user.name')
    ->searchable(query: function (Builder $query, string $search): Builder {
        return $query->whereHas('user', function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        });
    })
```

## WCAG 2.2 AA Compliance

### Accessibility Features

```php
TextInput::make('name')
    ->label('Nama Penuh')
    ->helperText('Masukkan nama penuh anda')
    ->placeholder('Contoh: Ahmad bin Ali')
    ->extraAttributes([
        'aria-describedby' => 'name-help',
        'aria-required' => 'true',
    ])
```

### Color Contrast

```php
// Ensure 4.5:1 contrast ratio for text
TextColumn::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'open' => 'danger',      // Red with sufficient contrast
        'in_progress' => 'warning', // Yellow with dark text
        'resolved' => 'success',    // Green with sufficient contrast
        default => 'gray',
    })
```

## Bahasa Melayu Integration

### Localization Patterns

```php
// Resource labels
protected static ?string $modelLabel = 'Tiket Helpdesk';
protected static ?string $pluralModelLabel = 'Tiket Helpdesk';
protected static ?string $navigationLabel = 'Tiket Helpdesk';

// Form labels
TextInput::make('title')
    ->label('Tajuk Tiket')
    ->placeholder('Masukkan tajuk tiket')
    ->helperText('Sila berikan tajuk yang jelas dan ringkas')

// Table columns
TextColumn::make('status')
    ->label('Status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))

// Actions
Action::make('approve')
    ->label('Luluskan')
    ->successNotificationTitle('Tiket berjaya diluluskan')
    ->modalHeading('Luluskan Tiket')
    ->modalDescription('Adakah anda pasti untuk meluluskan tiket ini?')
```

## Best Practices

### Code Organization

1. **Use static `make()` methods** for component initialization
2. **Leverage relationship() method** for form selects instead of manual options
3. **Implement proper authorization** at resource and action levels
4. **Use schema components** for consistent layouts
5. **Follow ICTServe hybrid architecture** with nullable `user_id` foreign keys

### Performance Guidelines

1. **Eager load relationships** in `getEloquentQuery()`
2. **Use chunking for bulk operations** with large datasets
3. **Implement proper caching** for expensive queries
4. **Optimize database queries** with proper indexing

### Security Considerations

1. **Always validate form input** with proper rules
2. **Implement authorization policies** for all resources
3. **Use signed URLs** for guest access tokens
4. **Sanitize HTML output** when using `html()` method

### Testing Requirements

1. **Test all CRUD operations** for resources
2. **Verify authorization rules** work correctly
3. **Test form validation** with various inputs
4. **Ensure accessibility compliance** in UI components

## Migration from v3

### Automated Upgrade

```bash
# Install upgrade script
composer require filament/upgrade:"^4.0" -W --dev

# Run automated upgrade
vendor/bin/filament-v4

# Follow the output instructions
composer require filament/filament:"^4.0" -W --no-update
composer update

# Optional: Migrate directory structure
php artisan filament:upgrade-directory-structure-to-v4 --dry-run
php artisan filament:upgrade-directory-structure-to-v4

# Clean up
composer remove filament/upgrade --dev
```

### Manual Changes Required

1. **Update action imports** to use `Filament\Actions\Action`
2. **Update schema component imports** to `Filament\Schemas\Components`
3. **Add explicit column spanning** where needed
4. **Review file visibility settings** (now private by default)
5. **Update filter behavior** if immediate filtering is required

## ICTServe Integration Points

### Dual Audit System

```php
// Ensure all Filament resources support audit logging
use App\Traits\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class HelpdeskTicket extends Model
{
    use Auditable, LogsActivity;
    
    protected $fillable = ['title', 'description', 'status', 'user_id'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status'])
            ->logOnlyDirty();
    }
}
```

### Real-time Updates

```php
// Integrate with Laravel Reverb for real-time updates
class HelpdeskTicketResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([...])
            ->live(); // Enable real-time updates
    }
}
```

This comprehensive Filament v4 steering file provides the foundation for building robust, accessible, and maintainable admin interfaces in the ICTServe application while following Laravel 12 and Filament v4 best practices.
