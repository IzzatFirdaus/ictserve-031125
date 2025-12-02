---
applyTo: "app/Filament/**"
description: "Filament 4 standards: Resources, SDUI schemas, unified actions, and authorization patterns for ICTServe"
---

# Filament 4 Admin Instructions

**Purpose**
Defines mandatory standards for the ICTServe Admin Panel using Filament 4. Ensures consistency in Server-Driven UI (SDUI), authorization, and component architecture.

**Scope**
Applies to `app/Filament`, `app/Providers/Filament`, and all Filament Resources/Pages/Widgets.

## 1. Filament 4 Key Standards

- **SDUI Architecture**: UI layout is defined in PHP using `Filament\Schemas`.
- **Unified Actions**: All actions extend `Filament\Actions\Action`.
- **Icons**: Must use the `Filament\Support\Icons\Heroicon` Enum.
- **Visibility**: Files are `private` by default.
- **Filtering**: Filters are deferred (require "Apply" click) by default.

## 2. Resource Structure

Resources must strictly follow the **Model-Resource-Page** pattern.

### Standard Resource Template
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\Asset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;
use Filament\Schemas\Components\Section; // Filament 4 Layout Namespace
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $navigationIcon = Heroicon::OutlinedRectangleStack->value;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Asset Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'draft' => 'Draft',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'draft' => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->deferFilters(); // Explicit preference
    }
}
````

## 3\. Schema & Layouts (Filament 4)

Layout components in Filament 4 have moved to the `Filament\Schemas` namespace.

**Correct Usage**:

```php
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;

// Implementation
Section::make('Identity')
    ->schema([ ... ])
    ->collapsible();
```

**Incorrect Usage (Legacy v3)**:

```php
// ❌ Do not use Forms\Components\Section
use Filament\Forms\Components\Section; 
```

## 4\. Authorization & Policies

**Never** bypass policies. Every resource must enforce:

```php
// app/Policies/AssetPolicy.php
public function viewAny(User $user): bool
{
    return $user->hasPermissionTo('view_assets');
}

// app/Filament/Resources/AssetResource.php
public static function canViewAny(): bool
{
    return auth()->user()->can('view_assets');
}
```

## 5\. Actions & Modals

Use the Unified Action pattern for custom business logic.

```php
use Filament\Actions\Action;

Action::make('approve')
    ->icon(Heroicon::OutlinedCheck->value)
    ->requiresConfirmation()
    ->action(function (Asset $record) {
        // Business logic via Service
        app(AssetService::class)->approve($record);
        
        Notification::make()
            ->title('Approved successfully')
            ->success()
            ->send();
    });
```

## 6\. Performance Guidelines

1.  **Eager Loading**: Use `$query->with(['category', 'user'])` in `getEloquentQuery()` or relation managers to prevent N+1 issues.
2.  **Indexing**: Ensure search columns in `TextColumn::make('email')->searchable()` are indexed in the database.
3.  **Deferral**: Keep `->deferFilters()` enabled for heavy tables.

## 7\. Traceability

All new Resources must map to a Requirement ID.

  * **Example**: `// Trace: SRS-FR-015 (Asset Management)` at top of file.
