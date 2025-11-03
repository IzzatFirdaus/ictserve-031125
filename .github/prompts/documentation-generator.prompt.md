---
mode: agent
---

# Documentation Generation Workflow

You are an expert technical writer generating comprehensive PHPDoc, README, and API documentation for ICTServe Laravel 12 application.

## Context

**ICTServe Documentation Standards:**
- PHPDoc for all public classes/methods
- README for each module/feature
- API documentation (if applicable)
- Inline comments for complex logic
- D00-D15 traceability

## Documentation Generation Steps

### 1. Scope Definition

**Task:** Identify what to document

**Ask User:**
- What files/modules need documentation?
- Is this PHPDoc, README, or API docs?
- Existing documentation to update?

**Output:** List of files/modules to document

---

### 2. PHPDoc Generation

**Task:** Add PHPDoc to classes and methods

**Class Documentation:**
```php
/**
 * Manages asset borrowing operations in ICTServe.
 *
 * Handles asset checkout, return, and approval workflows.
 * Implements PDPA 2010 data retention policies.
 *
 * @package App\Services
 * @author ICTServe Development Team
 * @since 1.0.0
 * @see \App\Models\Borrowing
 * @see \App\Policies\BorrowingPolicy
 */
class BorrowingService

    // ...

```

**Method Documentation:**
```php
/**
 * Create a new asset borrowing record.
 *
 * Validates user permissions, asset availability, and
 * creates borrowing record with audit trail.
 *
 * @param  \App\Models\Asset  $asset  Asset to borrow
 * @param  \App\Models\User  $user  User borrowing asset
 * @param  \Carbon\Carbon  $returnDate  Expected return date
 * @return \App\Models\Borrowing  Created borrowing record
 * 
 * @throws \Illuminate\Auth\Access\AuthorizationException
 * @throws \App\Exceptions\AssetUnavailableException
 */
public function createBorrowing(Asset $asset, User $user, Carbon $returnDate): Borrowing

    // ...

```

**Property Documentation:**
```php
/**
 * Maximum borrowing duration in days.
 *
 * @var int
 */
private const MAX_BORROWING_DAYS = 30;

/**
 * Borrowing repository instance.
 *
 * @var \App\Repositories\BorrowingRepository
 */
private BorrowingRepository $repository;
```

**Checklist:**
- [ ] All public classes have PHPDoc
- [ ] All public methods have PHPDoc
- [ ] Parameter types documented with `@param`
- [ ] Return types documented with `@return`
- [ ] Exceptions documented with `@throws`
- [ ] Complex properties documented
- [ ] Package/namespace documented with `@package`

---

### 3. README Generation (Module/Feature)

**Task:** Create comprehensive README for feature

**Template:**

```markdown
# [Feature Name] — ICTServe

## Purpose & Scope

[Brief description of what this feature does and why it exists]

**Traceability**: D03 (SRS-FR-XXX), D04 (Section X.X)

---

## Features

- Feature 1 description
- Feature 2 description
- Feature 3 description

---

## Installation / Setup

### Database Migration

```bash
php artisan migrate
```

### Seeding Sample Data

```bash
php artisan db:seed --class=[FeatureName]Seeder
```

### Configuration

Edit `config/[feature].php`:

```php
return [
    'option1' => env('FEATURE_OPTION1', 'default'),
    'option2' => env('FEATURE_OPTION2', true),
];
```

---

## Usage

### Basic Example

```php
use App\Services\FeatureService;

$service = new FeatureService();
$result = $service->performAction($data);
```

### Livewire Component

```blade
@livewire('feature-component', ['id' => $asset->id])
```

### Filament Resource

Access via admin panel: `/admin/feature-name`

---

## API Endpoints (if applicable)

### List Resources

```http
GET /api/resources
Authorization: Bearer token
```

**Response:**
```json

  "data": [
    
      "id": 1,
      "name": "Resource Name",
      "created_at": "2025-01-15T10:30:00Z"

,
  "meta": 
    "total": 150
  

```

### Create Resource

```http
POST /api/resources
Authorization: Bearer token
Content-Type: application/json


  "name": "New Resource",
  "field": "value"

```

---

## Testing

### Run Feature Tests

```bash
php artisan test --filter=[FeatureName]Test
```

### Test Coverage

```bash
php artisan test tests/Feature/[FeatureName]Test.php --coverage
```

Expected Coverage: ≥ 80%

---

## Security & Compliance

**Authorization:**
- Permissions: `view-feature`, `create-feature`, `edit-feature`, `delete-feature`
- Policies: `App\Policies\[FeatureName]Policy`

**Data Protection:**
- PDPA 2010 compliant
- Audit trail enabled (`Auditable` trait)
- Soft deletes enabled

**Accessibility:**
- WCAG 2.2 AA compliant
- Keyboard navigation supported
- Screen reader compatible

---

## Troubleshooting

### Common Issue 1

**Problem:** [Description]

**Solution:** [Step-by-step fix]

### Common Issue 2

**Problem:** [Description]

**Solution:** [Step-by-step fix]

---

## References

- [D03 (Software Requirements Specification)](../docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04 (Software Design Document)](../docs/D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [Laravel Documentation](https://laravel.com/docs/12.x)

---

**Maintainer**: ICTServe Development Team  
**Last Updated**: 2025-01-15  
**Version**: 1.0.0
```

**Checklist:**
- [ ] Purpose and scope defined
- [ ] Features listed
- [ ] Installation/setup instructions
- [ ] Usage examples (code snippets)
- [ ] API endpoints documented (if applicable)
- [ ] Testing instructions
- [ ] Security and compliance section
- [ ] Troubleshooting common issues
- [ ] References to D00-D15 documentation

---

### 4. API Documentation (OpenAPI/Swagger)

**Task:** Generate OpenAPI specification

**Install Laravel Scribe:**
```bash
composer require --dev knuckleswtf/scribe
php artisan vendor:publish --tag=scribe-config
```

**Configure** (`config/scribe.php`):
```php
return [
    'type' => 'laravel',
    'theme' => 'default',
    'title' => 'ICTServe API Documentation',
    'description' => 'RESTful API for ICTServe Asset Management System',
    'base_url' => env('APP_URL'),
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
          ,
      ,
  ,
];
```

**Annotate Controllers:**
```php
/**
 * @group Asset Management
 *
 * APIs for managing assets in ICTServe.
 */
class AssetController extends Controller

    /**
     * List all assets
     *
     * Retrieve paginated list of assets with optional filtering.
     *
     * @queryParam status string Filter by status (available, borrowed, maintenance). Example: available
     * @queryParam category_id int Filter by category ID. Example: 5
     * 
     * @response 200 
     *   "data": [
     *     
     *       "id": 1,
     *       "name": "Laptop Dell Latitude 5420",
     *       "asset_tag": "LAP-001",
     *       "status": "available"
     * 
     * ,
     *   "meta": 
     *     "total": 150,
     *     "per_page": 15
     *   
     * 
     */
    public function index(Request $request): JsonResponse
    
        // ...


```

**Generate Docs:**
```bash
php artisan scribe:generate
```

**Access:** `https://ictserve.local/docs`

---

### 5. Inline Comments (Complex Logic)

**Task:** Add explanatory comments

**When to Comment:**

**Business Logic:**
```php
// PDPA 2010: Auto-delete borrowing records older than 7 years
$cutoffDate = now()->subYears(7);
Borrowing::where('created_at', '<', $cutoffDate)->forceDelete();
```

**Workarounds:**
```php
// Workaround: Filament v4 requires manual authorization check
// See: https://github.com/filamentphp/filament/issues/12345
if (!auth()->user()->can('view', $asset)) 
    abort(403);

```

**Tricky Algorithms:**
```php
/**
 * Calculate optimal asset allocation using greedy algorithm.
 *
 * Time Complexity: O(n log n)
 * Space Complexity: O(n)
 */
private function allocateAssets(array $requests): array

    // Sort requests by priority (descending)
    usort($requests, fn($a, $b) => $b['priority'] <=> $a['priority']);
    
    // Greedy allocation: assign highest priority first
    foreach ($requests as $request) 
        // ...


```

**Checklist:**
- [ ] Complex business logic explained
- [ ] Workarounds documented with links
- [ ] Algorithms described (complexity analysis)
- [ ] No obvious/redundant comments

---

### 6. Database Schema Documentation

**Task:** Document database structure

**ERD Generation:**
```bash
# Install laravel-erd
composer require --dev beyondcode/laravel-er-diagram-generator

# Generate ERD
php artisan generate:erd
```

**Manual Documentation:**

**`docs/database/schema.md`:**
```markdown
# Database Schema — ICTServe

## Assets Table

**Table Name:** `assets`

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NO | - | Asset name |
| asset_tag | VARCHAR(100) | NO | - | Unique asset identifier |
| category_id | BIGINT UNSIGNED | NO | - | Foreign key to categories |
| status | ENUM | NO | 'available' | Asset status (available, borrowed, maintenance) |
| created_at | TIMESTAMP | YES | NULL | Creation timestamp |
| updated_at | TIMESTAMP | YES | NULL | Last update timestamp |
| deleted_at | TIMESTAMP | YES | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `asset_tag`
- INDEX: `status`
- FOREIGN KEY: `category_id` REFERENCES `categories(id)` ON DELETE CASCADE

**Relationships:**
- `belongsTo` Category
- `hasMany` Borrowing
```

---

### 7. Change Log (CHANGELOG.md)

**Task:** Maintain version history

**Template:**

```markdown
# Changelog — ICTServe

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- New feature X
- New API endpoint Y

### Changed
- Updated dependency Z to v2.0

### Fixed
- Bug fix for issue #123

## [1.0.0] - 2025-01-15

### Added
- Initial release
- Asset management system
- Borrowing workflow
- Filament admin panel

### Security
- PDPA 2010 compliance
- OWASP Top 10 compliance
- Audit trail implementation
```

---

### 8. Deployment Guide

**Task:** Document deployment steps

**`docs/deployment.md`:**
```markdown
# Deployment Guide — ICTServe

## Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer 2.x
- Apache/Nginx

## Deployment Steps

### 1. Clone Repository
```bash
git clone https://github.com/IzzatFirdaus/ictserve-091025.git
cd ictserve-091025
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=secret
```

### 4. Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Restart Services
```bash
sudo systemctl restart apache2
# or
sudo systemctl restart nginx
```

## Post-Deployment

### Verify Installation
Visit: `https://yourdomain.com`

### Run Tests
```bash
php artisan test
```

### Monitor Logs
```bash
tail -f storage/logs/laravel.log
```
```

---

## Documentation Checklist

**Before Marking Complete:**

- [ ] PHPDoc added to all public classes/methods
- [ ] README created for feature/module
- [ ] API documentation generated (if applicable)
- [ ] Complex logic has inline comments
- [ ] Database schema documented
- [ ] CHANGELOG.md updated
- [ ] Deployment guide created/updated
- [ ] Traceability to D00-D15 documentation
- [ ] Examples and code snippets included
- [ ] Security and compliance sections added

---

## References

- PHPDoc: https://docs.phpdoc.org/
- Laravel Scribe: https://scribe.knuckles.wtf/laravel/
- Keep a Changelog: https://keepachangelog.com/
- D10 (Source Code Documentation)
