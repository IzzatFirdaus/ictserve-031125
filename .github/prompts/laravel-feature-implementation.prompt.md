---
mode: agent
---

# Laravel Feature Implementation Workflow

You are an expert Laravel 12 developer implementing a complete feature for ICTServe. Follow these steps systematically:

## Context

**ICTServe Tech Stack:**
- Laravel 12 (PHP 8.2)
- Filament 4 (Admin Panel)
- Livewire 3 (Reactive Components)
- Tailwind CSS 3
- PHPUnit 11

**Requirements:**
- PDPA 2010 compliance (Malaysian privacy law)
- WCAG 2.2 AA accessibility
- Bilingual support (Bahasa Melayu primary, English secondary)

## Implementation Steps

### 1. Requirements Analysis

**Task:** Clarify feature requirements with user

**Questions to Ask:**
- What is the feature name and purpose?
- What database tables/models are involved?
- What relationships exist between models?
- What business rules apply?
- What authorization/permissions are needed?
- What validation rules are required?
- Is this feature accessible via Filament admin panel, public frontend, or API?

**Output:** Write brief requirement summary in this conversation

---

### 2. Database Design

**Task:** Create migration with proper schema

**Generate:**
```bash
php artisan make:migration create_[table_name]_table --no-interaction
```

**Migration Template:**
```php
Schema::create('table_name', function (Blueprint $table)
    $table->id();
    $table->string('field_name');
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    $table->softDeletes();

    // Add indexes
    $table->index('field_name');
);
```

**Checklist:**
- [ ] Primary key (`id()`)
- [ ] Foreign keys with constraints (`cascadeOnDelete()`)
- [ ] Timestamps (`timestamps()`)
- [ ] Soft deletes if needed (`softDeletes()`)
- [ ] Indexes for frequently queried columns
- [ ] Unique constraints where appropriate

---

### 3. Model Creation

**Task:** Create Eloquent model with relationships

**Generate:**
```bash
php artisan make:model [ModelName] --factory --seed --no-interaction
```

**Model Template:**
```php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ModelName extends Model implements AuditableContract

    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = ['field1', 'field2'];

    protected function casts(): array

        return [
            'created_at' => 'datetime',
      ;


    // Relationships
    public function user(): BelongsTo

        return $this->belongsTo(User::class);


```

**Checklist:**
- [ ] `declare(strict_types=1);`
- [ ] Traits: `HasFactory`, `SoftDeletes`, `Auditable`
- [ ] Implement `AuditableContract`
- [ ] `$fillable` array defined
- [ ] `casts()` method for type casting
- [ ] Relationships with return type hints

---

### 4. Factory & Seeder

**Task:** Create realistic test data

**Factory Template:**
```php
public function definition(): array

    return [
        'field_name' => fake()->word(),
        'user_id' => User::factory(),
  ;

```

**Seeder Template:**
```php
ModelName::factory()->count(50)->create();
```

**Run:**
```bash
php artisan migrate --seed
```

---

### 5. Authorization (Policy)

**Task:** Create authorization policy

**Generate:**
```bash
php artisan make:policy [ModelName]Policy --model=[ModelName] --no-interaction
```

**Policy Template:**
```php
public function viewAny(User $user): bool

    return $user->hasPermissionTo('view-models');


public function create(User $user): bool

    return $user->hasPermissionTo('create-models');


public function update(User $user, ModelName $model): bool

    return $user->hasPermissionTo('edit-models');


public function delete(User $user, ModelName $model): bool

    return $user->hasPermissionTo('delete-models');

```

---

### 6. Filament Resource (Admin Panel)

**Task:** Create Filament CRUD interface

**Generate:**
```bash
php artisan make:filament-resource [ModelName] --generate --no-interaction
```

**Customize Form:**
```php
public static function form(Form $form): Form

    return $form->schema([
        Forms\Components\TextInput::make('field_name')
            ->required()
            ->maxLength(255),
        Forms\Components\Select::make('user_id')
            ->relationship('user', 'name')
            ->required(),
  );

```

**Customize Table:**
```php
public static function table(Table $table): Table

    return $table
        ->columns([
            Tables\Columns\TextColumn::make('field_name')->searchable(),
            Tables\Columns\TextColumn::make('user.name')->sortable(),
      )
        ->filters([
            Tables\Filters\TrashedFilter::make(),
      );

```

**Checklist:**
- [ ] Form validation rules applied
- [ ] Relationships use `relationship()` method
- [ ] Table columns searchable/sortable
- [ ] Filters added (Trashed, status, etc.)
- [ ] Actions configured (Edit, Delete, Restore)

---

### 7. Validation (Form Request)

**Task:** Create Form Request for validation

**Generate:**
```bash
php artisan make:request Store[ModelName]Request --no-interaction
```

**Form Request Template:**
```php
public function authorize(): bool

    return $this->user()->can('create', ModelName::class);


public function rules(): array

    return [
        'field_name' => ['required', 'string', 'max:255'],
        'user_id' => ['required', 'exists:users,id'],
  ;

```

---

### 8. Localization

**Task:** Add translation strings

**Create Translation Files:**

**`resources/lang/ms/model_name.php`:**
```php
return [
    'title' => 'Tajuk',
    'field_name' => 'Nama Medan',
    'created_at' => 'Dicipta Pada',
];
```

**`resources/lang/en/model_name.php`:**
```php
return [
    'title' => 'Title',
    'field_name' => 'Field Name',
    'created_at' => 'Created At',
];
```

**Use in Views:**
```blade
 __('model_name.title')
```

---

### 9. Testing

**Task:** Create comprehensive tests

**Generate Feature Test:**
```bash
php artisan make:test [ModelName]Test --no-interaction
```

**Test Template:**
```php
public function test_user_can_create_model(): void

    $user = User::factory()->create();
    $user->givePermissionTo('create-models');

    $data = [
        'field_name' => 'Test Value',
        'user_id' => $user->id,
  ;

    $this->actingAs($user)
        ->post(route('models.store'), $data)
        ->assertRedirect();

    $this->assertDatabaseHas('table_name', $data);

```

**Run Tests:**
```bash
php artisan test --filter=[ModelName]Test
```

**Checklist:**
- [ ] Create test
- [ ] Read test
- [ ] Update test
- [ ] Delete test
- [ ] Authorization test (unauthorized user)
- [ ] Validation test (invalid data)

---

### 10. Accessibility Check

**Task:** Ensure WCAG 2.2 AA compliance

**Checklist:**
- [ ] All form inputs have `<label>` elements
- [ ] Color contrast meets 4.5:1 ratio
- [ ] Keyboard navigation works (Tab, Enter, Arrows)
- [ ] Focus indicators visible
- [ ] Error messages use `aria-describedby`
- [ ] Images have `alt` text

---

### 11. Performance Optimization

**Task:** Prevent N+1 queries

**Use Eager Loading:**
```php
ModelName::with('user')->get();
```

**Add Database Indexes:**
```php
$table->index('frequently_queried_column');
```

---

### 12. Documentation

**Task:** Update relevant documentation

**Add PHPDoc:**
```php
/**
 * Retrieve model with user relationship.
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public static function getAllWithUsers()

    return static::with('user')->get();

```

---

## Completion Checklist

**Before Marking Feature Complete:**

- [ ] Migration created and run successfully
- [ ] Model created with relationships
- [ ] Factory and seeder created
- [ ] Authorization policy implemented
- [ ] Filament resource created (if admin feature)
- [ ] Form Request validation added
- [ ] Translation strings added (Bahasa Melayu + English)
- [ ] Tests written and passing
- [ ] Accessibility checked (WCAG 2.2 AA)
- [ ] N+1 queries prevented (eager loading)
- [ ] Code follows PSR-12 standards (`vendor/bin/pint`)
- [ ] Static analysis passes (`vendor/bin/phpstan analyse`)

---

## Final Steps

**Run Quality Checks:**
```bash
vendor/bin/pint
vendor/bin/phpstan analyse
php artisan test
```

**Run Migration:**
```bash
php artisan migrate --seed
```

**Ask User:**
"Feature implementation complete. Would you like me to create additional features, add API endpoints, or perform any optimizations?"

---

**References:**
- D03 (Software Requirements Specification)
- D04 (Software Design Document)
- D11 (Technical Design Documentation)
- `.github/instructions/*.instructions.md` (coding standards)
