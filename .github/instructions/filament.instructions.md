---
applyTo: "app/Filament/**"
description: "Filament 4 Resources, Actions, Forms, Tables, SDUI patterns, AdminPanel configuration, and Authorization for ICTServe"
---

# Filament 4 — ICTServe Admin Panel Standards

## Purpose & Scope

Provides Filament 4 specific conventions for ICTServe admin panel. Covers Resources (CRUD), Actions, Forms, Tables, Widgets, Pages, SDUI (Server-Driven UI) patterns, and authorization integration.

**Applies To**: Filament admin components (`app/Filament/**`)

**Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide), Filament admin specifications

---

## Core Principles

1. **Server-Driven UI (SDUI)**: Define UI in PHP, rendered by Filament
2. **Artisan-First**: Use `php artisan make:filament-*` commands
3. **Resource Pattern**: One Resource per Eloquent Model for CRUD
4. **Policy Integration**: Use Laravel Policies for authorization
5. **Discovery-Based**: Files in `app/Filament/` auto-discovered

---

## Filament 4 Key Changes

**From Filament 3**:
- ✅ **File visibility** is `private` by default
- ✅ **Deferred filters** are default (users must click "Apply" button)
- ✅ **Grid/Section/Fieldset** no longer span all columns by default
- ✅ **No `all` pagination** option by default
- ✅ **All actions** extend `Filament\Actions\Action` (no `Filament\Tables\Actions`)
- ✅ **Layout components** moved to `Filament\Schemas\Components` (Grid, Section, Fieldset, Tabs, Wizard)
- ✅ **New Repeater component** for Forms
- ✅ **Icons** use `Filament\Support\Icons\Heroicon` Enum

---

## Resource Structure

### Creating Resources

```bash
# Basic Resource
php artisan make:filament-resource Asset --no-interaction

# Resource with soft deletes
php artisan make:filament-resource Asset --soft-deletes --no-interaction

# Resource with custom panel
php artisan make:filament-resource Asset --panel=admin --no-interaction

# Resource with view page
php artisan make:filament-resource Asset --view --no-interaction

# Resource with all pages (simple)
php artisan make:filament-resource Asset --simple --no-interaction
```

---

### Basic Resource Example

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource

    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Aset';

    protected static ?string $navigationGroup = 'Pengurusan Aset';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form

        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Aset')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Aset')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('asset_tag')
                            ->label('Kod Aset')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'available' => 'Tersedia',
                                'borrowed' => 'Dipinjam',
                                'maintenance' => 'Penyelenggaraan',
                                'retired' => 'Dilupuskan',
                          )
                            ->required()
                            ->default('available'),

                        Forms\Components\DatePicker::make('acquired_date')
                            ->label('Tarikh Pemerolehan')
                            ->required()
                            ->maxDate(now()),
                  )
                    ->columns(2),
          );


    public static function table(Table $table): Table

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->label('Kod Aset')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'borrowed',
                        'info' => 'maintenance',
                        'danger' => 'retired',
                  ),

                Tables\Columns\TextColumn::make('acquired_date')
                    ->label('Tarikh Pemerolehan')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
          )
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'borrowed' => 'Dipinjam',
                        'maintenance' => 'Penyelenggaraan',
                        'retired' => 'Dilupuskan',
                  ),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                Tables\Filters\TrashedFilter::make(),
          )
            ->deferFilters() // Apply filters only when user clicks button (Filament 4 default)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
          )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
              ),
          );


    public static function getPages(): array

        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/record/edit'),
      ;


```

---

## Form Components

### Text Inputs

```php
Forms\Components\TextInput::make('name')
    ->label('Nama Aset')
    ->required()
    ->maxLength(255)
    ->placeholder('Masukkan nama aset')
    ->helperText('Nama penuh aset')
    ->autofocus()
    ->columnSpan(2),
```

---

### Select & Relationship

**Basic Select**:
```php
Forms\Components\Select::make('status')
    ->label('Status')
    ->options([
        'available' => 'Tersedia',
        'borrowed' => 'Dipinjam',
  )
    ->required()
    ->searchable()
    ->default('available'),
```

**Relationship Select** (Recommended):
```php
Forms\Components\Select::make('category_id')
    ->label('Kategori')
    ->relationship('category', 'name') // Uses relationship method
    ->required()
    ->searchable()
    ->preload() // Load options immediately
    ->createOptionForm([ // Allow creating new category inline
        Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255),
  ),
```

---

### Date & Time Pickers

```php
Forms\Components\DatePicker::make('acquired_date')
    ->label('Tarikh Pemerolehan')
    ->required()
    ->maxDate(now())
    ->displayFormat('d/m/Y')
    ->format('Y-m-d'),

Forms\Components\DateTimePicker::make('borrowed_at')
    ->label('Tarikh Dipinjam')
    ->seconds(false)
    ->displayFormat('d/m/Y H:i'),

Forms\Components\TimePicker::make('return_time')
    ->label('Waktu Pulangan')
    ->seconds(false),
```

---

### Textarea & Rich Editor

```php
Forms\Components\Textarea::make('description')
    ->label('Keterangan')
    ->rows(3)
    ->maxLength(1000)
    ->columnSpanFull(),

Forms\Components\RichEditor::make('notes')
    ->label('Nota')
    ->toolbarButtons([
        'bold',
        'italic',
        'bulletList',
        'orderedList',
        'link',
  )
    ->columnSpanFull(),
```

---

### File Upload

```php
Forms\Components\FileUpload::make('image')
    ->label('Gambar Aset')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatios([
        '16:9',
        '4:3',
        '1:1',
  )
    ->maxSize(2048) // 2MB
    ->directory('assets/images')
    ->visibility('private') // Filament 4 default
    ->downloadable()
    ->previewable(),
```

---

### Checkbox & Toggle

```php
Forms\Components\Checkbox::make('is_active')
    ->label('Aktif')
    ->default(true),

Forms\Components\Toggle::make('is_available')
    ->label('Tersedia untuk Dipinjam')
    ->onColor('success')
    ->offColor('danger'),
```

---

### Repeater (New in Filament 4)

```php
Forms\Components\Repeater::make('specifications')
    ->label('Spesifikasi')
    ->schema([
        Forms\Components\TextInput::make('key')
            ->label('Nama')
            ->required(),
        Forms\Components\TextInput::make('value')
            ->label('Nilai')
            ->required(),
  )
    ->columns(2)
    ->addActionLabel('Tambah Spesifikasi')
    ->collapsible()
    ->itemLabel(fn (array $state): ?string => $state['key'] ?? null),
```

---

### Layout Components (Filament 4: Schemas\Components)

**Section**:
```php
use Filament\Schemas\Components\Section;

Section::make('Maklumat Asas')
    ->description('Maklumat utama aset')
    ->schema([
        // Form fields
  )
    ->columns(2)
    ->collapsible()
    ->collapsed(false),
```

**Grid**:
```php
use Filament\Schemas\Components\Grid;

Grid::make(3) // 3 columns
    ->schema([
        Forms\Components\TextInput::make('name'),
        Forms\Components\TextInput::make('code'),
        Forms\Components\Select::make('status'),
  ),
```

**Tabs**:
```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        Tabs\Tab::make('Maklumat Asas')
            ->schema([
                // Fields
          ),
        Tabs\Tab::make('Spesifikasi')
            ->schema([
                // Fields
          ),
        Tabs\Tab::make('Sejarah')
            ->schema([
                // Fields
          ),
  ),
```

**Fieldset**:
```php
use Filament\Schemas\Components\Fieldset;

Fieldset::make('Alamat')
    ->schema([
        Forms\Components\TextInput::make('address_line_1'),
        Forms\Components\TextInput::make('address_line_2'),
        Forms\Components\TextInput::make('city'),
  )
    ->columns(2),
```

---

## Table Columns

### Text Column

```php
Tables\Columns\TextColumn::make('name')
    ->label('Nama')
    ->searchable()
    ->sortable()
    ->toggleable()
    ->copyable()
    ->wrap(),

Tables\Columns\TextColumn::make('created_at')
    ->label('Dicipta')
    ->dateTime('d/m/Y H:i')
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true),
```

---

### Badge Column

```php
Tables\Columns\BadgeColumn::make('status')
    ->label('Status')
    ->colors([
        'success' => 'available',
        'warning' => 'borrowed',
        'danger' => 'retired',
  )
    ->icons([
        'heroicon-o-check-circle' => 'available',
        'heroicon-o-clock' => 'borrowed',
        'heroicon-o-x-circle' => 'retired',
  ),
```

---

### Image Column

```php
Tables\Columns\ImageColumn::make('image')
    ->label('Gambar')
    ->circular()
    ->size(40),
```

---

### Boolean Column

```php
Tables\Columns\IconColumn::make('is_active')
    ->label('Aktif')
    ->boolean()
    ->trueIcon('heroicon-o-check-badge')
    ->falseIcon('heroicon-o-x-mark')
    ->trueColor('success')
    ->falseColor('danger'),
```

---

## Table Filters

### Select Filter

```php
Tables\Filters\SelectFilter::make('status')
    ->label('Status')
    ->options([
        'available' => 'Tersedia',
        'borrowed' => 'Dipinjam',
  )
    ->multiple(),

Tables\Filters\SelectFilter::make('category')
    ->label('Kategori')
    ->relationship('category', 'name'),
```

---

### Date Filter

```php
Tables\Filters\Filter::make('acquired_date')
    ->form([
        Forms\Components\DatePicker::make('acquired_from')
            ->label('Dari'),
        Forms\Components\DatePicker::make('acquired_until')
            ->label('Hingga'),
  )
    ->query(function (Builder $query, array $data): Builder
        return $query
            ->when(
                $data['acquired_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('acquired_date', '>=', $date),
            )
            ->when(
                $data['acquired_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('acquired_date', '<=', $date),
            );
),
```

---

### Trashed Filter (Soft Deletes)

```php
Tables\Filters\TrashedFilter::make(),
```

---

### Defer Filters (Filament 4 Default)

```php
->deferFilters() // Users must click "Apply" button
```

**Disable Defer** (apply filters immediately):
```php
->deferFilters(false)
```

---

## Actions

### Table Actions (Row-Level)

```php
Tables\Actions\ViewAction::make(),

Tables\Actions\EditAction::make(),

Tables\Actions\DeleteAction::make()
    ->requiresConfirmation()
    ->modalHeading('Padam Aset')
    ->modalDescription('Adakah anda pasti mahu memadam aset ini?')
    ->modalSubmitActionLabel('Ya, Padam'),

Tables\Actions\RestoreAction::make(),

Tables\Actions\ForceDeleteAction::make(),
```

---

### Custom Action

```php
Tables\Actions\Action::make('borrow')
    ->label('Pinjam')
    ->icon('heroicon-o-arrow-right-circle')
    ->color('success')
    ->requiresConfirmation()
    ->form([
        Forms\Components\DatePicker::make('return_by')
            ->label('Tarikh Pulangan')
            ->required()
            ->minDate(now()->addDay()),
        Forms\Components\Textarea::make('notes')
            ->label('Catatan'),
  )
    ->action(function (Asset $record, array $data): void
        Borrowing::create([
            'asset_id' => $record->id,
            'user_id' => auth()->id(),
            'return_by' => $data['return_by'],
            'notes' => $data['notes'],
      );

        $record->update(['status' => 'borrowed']);

        Notification::make()
            ->title('Aset berjaya dipinjam')
            ->success()
            ->send();
)
    ->visible(fn (Asset $record): bool => $record->status === 'available'),
```

---

### Bulk Actions

```php
Tables\Actions\BulkActionGroup::make([
    Tables\Actions\DeleteBulkAction::make(),

    Tables\Actions\BulkAction::make('updateStatus')
        ->label('Kemaskini Status')
        ->icon('heroicon-o-pencil')
        ->form([
            Forms\Components\Select::make('status')
                ->label('Status Baru')
                ->options([
                    'available' => 'Tersedia',
                    'maintenance' => 'Penyelenggaraan',
              )
                ->required(),
      )
        ->action(function (Collection $records, array $data): void
            $records->each->update(['status' => $data['status']]);

            Notification::make()
                ->title('Status dikemaskini')
                ->success()
                ->send();
    ),
]),
```

---

## Authorization (Policies)

**Resource Authorization**:
```php
// app/Policies/AssetPolicy.php
public function viewAny(User $user): bool

    return $user->can('view_asset');


public function create(User $user): bool

    return $user->can('create_asset');


public function update(User $user, Asset $asset): bool

    return $user->can('update_asset');


public function delete(User $user, Asset $asset): bool

    return $user->can('delete_asset');

```

**Register Policy** (`app/Providers/AuthServiceProvider.php`):
```php
protected $policies = [
    Asset::class => AssetPolicy::class,
];
```

---

## Pages

### List Page

```php
<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords

    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array

        return [
            Actions\CreateAction::make(),
      ;


```

---

### Create Page

```php
<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord

    protected static string $resource = AssetResource::class;

    protected function getRedirectUrl(): string

        return $this->getResource()::getUrl('index');


    protected function mutateFormDataBeforeCreate(array $data): array

        $data['created_by'] = auth()->id();

        return $data;


```

---

### Edit Page

```php
<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord

    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array

        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
      ;


    protected function getRedirectUrl(): string

        return $this->getResource()::getUrl('index');


```

---

## Widgets

### Stats Widget

```bash
php artisan make:filament-widget AssetStatsOverview --stats --no-interaction
```

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends BaseWidget

    protected function getStats(): array

        return [
            Stat::make('Jumlah Aset', Asset::count())
                ->description('Semua aset dalam sistem')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('success'),

            Stat::make('Aset Tersedia', Asset::where('status', 'available')->count())
                ->description('Boleh dipinjam')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Aset Dipinjam', Asset::where('status', 'borrowed')->count())
                ->description('Sedang dipinjam')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
      ;


```

---

## Testing Filament

### Resource Test

```php
<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\AssetResource;
use App\Filament\Resources\AssetResource\Pages\ListAssets;
use App\Filament\Resources\AssetResource\Pages\CreateAsset;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssetResourceTest extends TestCase

    use RefreshDatabase;

    public function test_can_list_assets(): void

        $user = User::factory()->create();
        $assets = Asset::factory()->count(10)->create();

        Livewire::test(ListAssets::class)
            ->actingAs($user)
            ->assertCanSeeTableRecords($assets);


    public function test_can_search_assets(): void

        $user = User::factory()->create();
        $asset = Asset::factory()->create(['name' => 'Laptop Dell']);
        Asset::factory()->create(['name' => 'Mouse Logitech']);

        Livewire::test(ListAssets::class)
            ->actingAs($user)
            ->searchTable('Laptop')
            ->assertCanSeeTableRecords([$asset])
            ->assertCanNotSeeTableRecords(Asset::where('name', 'Mouse Logitech')->get());


    public function test_can_create_asset(): void

        $user = User::factory()->create();

        Livewire::test(CreateAsset::class)
            ->actingAs($user)
            ->fillForm([
                'name' => 'Laptop HP',
                'asset_tag' => 'LT-001',
                'status' => 'available',
          )
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('assets', [
            'name' => 'Laptop HP',
            'asset_tag' => 'LT-001',
      );


```

---

## References & Resources

- **Filament 4 Documentation**: https://filamentphp.com/docs
- **Filament Examples**: https://demo.filamentphp.com
- **ICTServe Traceability**: D13 (UI/UX Framework), D14 (UI/UX Design)

---

**Status**: ✅ Production-ready for ICTServe
**Last Updated**: 2025-11-01
**Maintained By**: Admin Panel Team (admin@motac.gov.my)
