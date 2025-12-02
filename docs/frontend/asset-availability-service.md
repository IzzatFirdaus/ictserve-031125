# Asset Availability Service Documentation

## Overview

The Asset Availability Service provides real-time asset availability checking for loan applications. It handles date-based conflict detection, category-based availability summaries, and calendar visualization data.

## Architecture

### Service Location

```
app/Services/AssetAvailabilityService.php
```

### Dependencies

- `App\Models\Asset` - Asset model
- `App\Models\AssetCategory` - Category model
- `Illuminate\Support\Facades\Cache` - Redis caching
- `Illuminate\Support\Facades\DB` - Database queries

### Traceability

- **D03-FR-042**: Asset Loan Application
- **D04 §5.2**: Loan Module Design

## Features

### 1. Category Availability Check

Check if a category has sufficient available assets for a date range.

```php
use App\Services\AssetAvailabilityService;

$service = app(AssetAvailabilityService::class);

$result = $service->checkCategoryAvailability(
    categoryId: 1,
    startDate: '2025-12-01',
    endDate: '2025-12-07',
    quantity: 2
);

// Returns:
// [
//     'available' => true,
//     'count' => 5,
//     'message' => '5 units available'
// ]
```

### 2. Available Asset Count

Get the count of available assets for a category in a date range.

```php
$count = $service->getAvailableAssetCount(
    categoryId: 1,
    startDate: '2025-12-01',
    endDate: '2025-12-07'
);
// Returns: 5
```

### 3. Category Availability Summary

Get availability summary for all active categories.

```php
$summary = $service->getCategoryAvailabilitySummary(
    startDate: '2025-12-01',
    endDate: '2025-12-07'
);

// Returns Collection:
// [
//     ['id' => 1, 'name' => 'Projector', 'available' => 5, 'total' => 10, 'percentage' => 50],
//     ['id' => 2, 'name' => 'Laptop', 'available' => 8, 'total' => 15, 'percentage' => 53],
//     ...
// ]
```

### 4. Find Available Asset

Find a specific available asset for a category and date range.

```php
$asset = $service->findAvailableAsset(
    categoryId: 1,
    startDate: '2025-12-01',
    endDate: '2025-12-07'
);

// Returns: Asset model or null
```

### 5. Specific Asset Availability

Check if a specific asset is available with conflict details.

```php
$result = $service->checkAssetAvailability(
    assetId: 42,
    startDate: '2025-12-01',
    endDate: '2025-12-07',
    excludeApplicationId: 100 // Optional: exclude from conflict check
);

// Returns:
// [
//     'available' => false,
//     'conflicts' => [
//         [
//             'application_number' => 'LA2025000001',
//             'loan_start_date' => '2025-11-30',
//             'loan_end_date' => '2025-12-05',
//             'status' => 'approved'
//         ]
//     ],
//     'message' => 'Asset is already booked'
// ]
```

### 6. Calendar Availability

Get daily availability data for calendar visualization.

```php
$calendar = $service->getCalendarAvailability(
    categoryId: 1,
    month: '2025-12'
);

// Returns:
// [
//     '2025-12-01' => ['available' => 5, 'total' => 10, 'percentage' => 50],
//     '2025-12-02' => ['available' => 4, 'total' => 10, 'percentage' => 40],
//     ...
// ]
```

## Caching Strategy

### Cache Configuration

```php
private const CACHE_TTL = 300; // 5 minutes
```

### Cache Keys

```
asset_availability:{categoryId}:{startDate}:{endDate}
```

### Cache Invalidation

```php
// Clear cache for a category
$service->clearCategoryCache($categoryId);
```

## Database Queries

### Booked Assets Query

The service identifies booked assets by checking:

1. Assets in the specified category
2. Linked to loan applications with status: `submitted`, `approved`, `in_use`
3. Date ranges that overlap with requested dates

```sql
SELECT DISTINCT loan_items.asset_id
FROM loan_items
JOIN loan_applications ON loan_items.loan_application_id = loan_applications.id
JOIN assets ON loan_items.asset_id = assets.id
WHERE assets.category_id = ?
  AND loan_applications.status IN ('submitted', 'approved', 'in_use')
  AND loan_applications.loan_start_date <= ?
  AND loan_applications.loan_end_date >= ?
```

### Available Assets Query

```sql
SELECT COUNT(*)
FROM assets
WHERE category_id = ?
  AND status = 'available'
  AND condition != 'damaged'
  AND id NOT IN (booked_asset_ids)
```

## Integration with Livewire

### Real-Time Updates

```php
// In GuestLoanApplication component
public function checkEquipmentAvailability(int $index): void
{
    $item = $this->form['equipment_items'][$index] ?? null;

    if (!$item || empty($item['equipment_type'])) {
        unset($this->equipmentAvailability[$index]);
        return;
    }

    $availabilityService = app(AssetAvailabilityService::class);
    $this->equipmentAvailability[$index] = $availabilityService->checkCategoryAvailability(
        (int) $item['equipment_type'],
        $this->form['loan_start_date'],
        $this->form['expected_return_date'],
        (int) ($item['quantity'] ?? 1)
    );
}
```

### Automatic Refresh

```php
// Refresh when dates change
public function updatedFormExpectedReturnDate($value): void
{
    $this->refreshAllEquipmentAvailability();
}

// Refresh when equipment type changes
public function updatedFormEquipmentItems($value, $key): void
{
    $parts = explode('.', $key);
    if (count($parts) >= 1) {
        $index = (int) $parts[0];
        $this->checkEquipmentAvailability($index);
    }
}
```

## Bilingual Messages

### Translation Keys

```php
// lang/en/loan.php
'availability' => [
    'available' => ':count units available',
    'insufficient' => 'Only :available units available, :requested requested',
    'asset_not_found' => 'Asset not found',
    'asset_unavailable' => 'Asset is not available for loan',
    'asset_available' => 'Asset is available',
    'asset_booked' => 'Asset is already booked for this period',
],

// lang/ms/loan.php
'availability' => [
    'available' => ':count unit tersedia',
    'insufficient' => 'Hanya :available unit tersedia, :requested diminta',
    'asset_not_found' => 'Aset tidak dijumpai',
    'asset_unavailable' => 'Aset tidak tersedia untuk pinjaman',
    'asset_available' => 'Aset tersedia',
    'asset_booked' => 'Aset sudah ditempah untuk tempoh ini',
],
```

## Performance Optimization

### Caching

- 5-minute cache TTL for availability checks
- Cache key includes category, start date, and end date
- Automatic invalidation on loan status changes

### Query Optimization

- Uses database indexes on `category_id`, `status`, `condition`
- Efficient date overlap detection
- Minimal joins for booked asset detection

### Recommendations

1. **Index Creation**:

```sql
CREATE INDEX idx_assets_category_status ON assets(category_id, status, condition);
CREATE INDEX idx_loan_apps_dates_status ON loan_applications(loan_start_date, loan_end_date, status);
```

2. **Cache Warming**: Pre-warm cache for popular categories during off-peak hours

3. **Batch Queries**: Use `getCategoryAvailabilitySummary()` instead of multiple individual checks

## Error Handling

### Asset Not Found

```php
if (!$asset) {
    return [
        'available' => false,
        'conflicts' => [],
        'message' => __('loan.availability.asset_not_found'),
    ];
}
```

### Asset Unavailable

```php
if ($asset->status !== 'available' || $asset->condition === 'damaged') {
    return [
        'available' => false,
        'conflicts' => [],
        'message' => __('loan.availability.asset_unavailable'),
    ];
}
```

## Testing

### Unit Tests

```php
public function test_category_availability_returns_correct_count()
{
    // Create 5 available assets in category
    Asset::factory()->count(5)->create([
        'category_id' => 1,
        'status' => 'available',
        'condition' => 'good',
    ]);

    $service = app(AssetAvailabilityService::class);
    $result = $service->checkCategoryAvailability(1, '2025-12-01', '2025-12-07', 3);

    $this->assertTrue($result['available']);
    $this->assertEquals(5, $result['count']);
}

public function test_detects_booking_conflicts()
{
    // Create asset and existing booking
    $asset = Asset::factory()->create(['category_id' => 1]);
    $application = LoanApplication::factory()->create([
        'status' => 'approved',
        'loan_start_date' => '2025-12-01',
        'loan_end_date' => '2025-12-07',
    ]);
    LoanItem::factory()->create([
        'loan_application_id' => $application->id,
        'asset_id' => $asset->id,
    ]);

    $service = app(AssetAvailabilityService::class);
    $result = $service->checkAssetAvailability($asset->id, '2025-12-03', '2025-12-10');

    $this->assertFalse($result['available']);
    $this->assertNotEmpty($result['conflicts']);
}
```

## API Reference

### Methods

| Method                           | Parameters                                                                   | Returns    |
| -------------------------------- | ---------------------------------------------------------------------------- | ---------- |
| `checkCategoryAvailability`      | int $categoryId, string $startDate, string $endDate, int $quantity           | array      |
| `getAvailableAssetCount`         | int $categoryId, string $startDate, string $endDate                          | int        |
| `getCategoryAvailabilitySummary` | string $startDate, string $endDate                                           | Collection |
| `findAvailableAsset`             | int $categoryId, string $startDate, string $endDate                          | ?Asset     |
| `checkAssetAvailability`         | int $assetId, string $startDate, string $endDate, ?int $excludeApplicationId | array      |
| `clearCategoryCache`             | int $categoryId                                                              | void       |
| `getCalendarAvailability`        | int $categoryId, string $month                                               | array      |

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-27  
**Author**: ICTServe Development Team  
**Status**: Production Ready
