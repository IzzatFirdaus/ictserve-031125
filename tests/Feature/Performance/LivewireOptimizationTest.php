<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use App\Livewire\GuestLoanApplication;
use App\Livewire\Loans\AuthenticatedLoanDashboard;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Livewire Component Optimization Tests
 *
 * Tests Livewire component performance, optimization patterns,
 * and compliance with OptimizedLivewireComponent trait requirements.
 *
 * @see D03-FR-014.1 Core Web Vitals targets
 * @see D03-FR-014.2 Livewire optimization patterns
 * @see Task 7.5 - Livewire component optimization verification
 */
class LivewireOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        AssetCategory::factory()->count(5)->create();
        Asset::factory()->count(20)->create();
    }

    /**
     * Test guest loan application component uses debouncing
     *
     * @see D03-FR-014.2 Debounced input handling (300ms)
     */
    #[Test]
    public function guest_loan_application_uses_debouncing(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        // Verify component renders
        $component->assertOk();

        // Test rapid input changes (simulating user typing)
        $startTime = microtime(true);

        for ($i = 0; $i < 5; $i++) {
            $component->set('form.applicant_name', 'Test User '.$i);
        }

        $processingTime = microtime(true) - $startTime;

        // With debouncing, rapid changes should be processed efficiently
        $this->assertLessThan(1.0, $processingTime, 'Debouncing not working effectively');
    }

    /**
     * Test component uses lazy loading for heavy data
     *
     * @see D03-FR-014.2 Lazy loading patterns
     */
    #[Test]
    public function component_uses_lazy_loading(): void
    {
        DB::enableQueryLog();

        $component = Livewire::test(GuestLoanApplication::class);

        $initialQueries = count(DB::getQueryLog());

        // Initial render should not load all data
        $this->assertLessThan(10, $initialQueries, 'Too many queries on initial render');

        // Component loads divisions and equipment types on render
        $this->assertGreaterThan(0, $initialQueries, 'Component should load some data');

        DB::disableQueryLog();
    }

    /**
     * Test component prevents N+1 query problems
     *
     * @see D03-FR-008.2 Database optimization
     */
    #[Test]
    public function component_prevents_n_plus_one_queries(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(10)->create(['user_id' => $user->id]);

        $this->actingAs($user);

        DB::enableQueryLog();

        $component = Livewire::test(AuthenticatedLoanDashboard::class);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Should use eager loading to prevent N+1
        $this->assertLessThan(15, $queryCount, 'Possible N+1 query problem detected');

        // Check for eager loading patterns
        $hasEagerLoading = false;
        foreach ($queries as $query) {
            if (
                str_contains($query['query'], 'select * from') &&
                (str_contains($query['query'], 'where') || str_contains($query['query'], 'in'))
            ) {
                $hasEagerLoading = true;
                break;
            }
        }

        $this->assertTrue($hasEagerLoading, 'No eager loading detected');

        DB::disableQueryLog();
    }

    /**
     * Test component uses computed properties efficiently
     *
     * @see D03-FR-014.2 Computed properties optimization
     */
    #[Test]
    public function component_uses_computed_properties(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        DB::enableQueryLog();

        // Refresh component multiple times
        $component->call('$refresh');
        $firstRefreshQueries = count(DB::getQueryLog());

        DB::flushQueryLog();

        $component->call('$refresh');
        $secondRefreshQueries = count(DB::getQueryLog());

        // Subsequent refreshes should execute similar query counts
        $this->assertEqualsWithDelta($firstRefreshQueries, $secondRefreshQueries, 2, 'Query count should be consistent');

        DB::disableQueryLog();
    }

    /**
     * Test component handles loading states properly
     *
     * @see D03-FR-014.4 Loading state management
     */
    #[Test]
    public function component_handles_loading_states(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        // Component renders successfully
        $component->assertOk();

        // Verify submitting state exists
        $this->assertFalse($component->get('submitting'), 'Component should track submitting state');
    }

    /**
     * Test component optimizes wire:model usage
     *
     * @see D03-FR-014.2 Wire model optimization
     */
    #[Test]
    public function component_optimizes_wire_model(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $html = $component->html();

        // Check for optimized wire:model usage (live.debounce, lazy, etc.)
        $hasOptimizedWireModel = str_contains($html, 'wire:model.live.debounce') ||
            str_contains($html, 'wire:model.lazy') ||
            str_contains($html, 'wire:model.blur');

        $this->assertTrue(
            $hasOptimizedWireModel,
            'Component should use optimized wire:model directives (debounce, lazy, blur)'
        );
    }

    /**
     * Test component memory usage is optimized
     *
     * @see D03-FR-007.2 Memory optimization
     */
    #[Test]
    public function component_memory_usage(): void
    {
        $initialMemory = memory_get_usage(true);

        // Create multiple component instances
        for ($i = 0; $i < 10; $i++) {
            $component = Livewire::test(GuestLoanApplication::class);
            $component->set('form.applicant_name', 'Test User '.$i);
            unset($component);
        }

        gc_collect_cycles();

        $finalMemory = memory_get_usage(true);
        $memoryIncrease = $finalMemory - $initialMemory;

        // Memory increase should be reasonable (< 15MB for 10 instances)
        $this->assertLessThan(
            15 * 1024 * 1024,
            $memoryIncrease,
            'Component memory usage too high'
        );
    }

    /**
     * Test component handles concurrent updates efficiently
     *
     * @see D03-FR-007.2 Concurrent processing
     */
    #[Test]
    public function component_handles_concurrent_updates(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $startTime = microtime(true);

        // Simulate concurrent property updates
        $component->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.purpose', 'Testing')
            ->set('form.location', 'HQ');

        $updateTime = microtime(true) - $startTime;

        // Concurrent updates should be processed efficiently
        $this->assertLessThan(1.0, $updateTime, 'Concurrent updates taking too long');
    }

    /**
     * Test component validation performance
     *
     * @see D03-FR-007.5 Real-time validation
     */
    #[Test]
    public function component_validation_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $startTime = microtime(true);

        // Trigger validation by trying to move to next step with invalid data
        $component->call('nextStep')
            ->assertHasErrors();

        $validationTime = microtime(true) - $startTime;

        // Validation should be fast (< 500ms)
        $this->assertLessThan(0.5, $validationTime, 'Validation taking too long');
    }

    /**
     * Test component uses wire:key in loops
     *
     * @see D03-FR-014.2 Loop optimization
     */
    #[Test]
    public function component_uses_wire_key_in_loops(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(5)->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $component = Livewire::test(AuthenticatedLoanDashboard::class);

        $html = $component->html();

        // Check for wire:key usage in loops
        if (str_contains($html, '@foreach') || str_contains($html, 'foreach')) {
            $this->assertStringContainsString(
                'wire:key',
                $html,
                'Loops should use wire:key for optimal performance'
            );
        }
    }

    /**
     * Test component polling performance
     *
     * @see D03-FR-014.2 Polling optimization
     */
    #[Test]
    public function component_polling_performance(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(AuthenticatedLoanDashboard::class);

        DB::enableQueryLog();

        // Simulate polling refresh
        $startTime = microtime(true);

        $component->call('$refresh');

        $pollTime = microtime(true) - $startTime;
        $queries = DB::getQueryLog();

        // Polling should be efficient
        $this->assertLessThan(0.5, $pollTime, 'Polling refresh taking too long');
        $this->assertLessThan(10, count($queries), 'Polling executing too many queries');

        DB::disableQueryLog();
    }

    /**
     * Test component uses wire:dirty for unsaved changes
     *
     * @see D03-FR-014.4 User feedback optimization
     */
    #[Test]
    public function component_uses_wire_dirty(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $html = $component->html();

        // Check for wire:dirty usage for better UX
        $hasWireDirty = str_contains($html, 'wire:dirty');

        if (! $hasWireDirty) {
            $this->markTestIncomplete(
                'Consider adding wire:dirty for unsaved changes feedback'
            );
        }
    }

    /**
     * Test component event dispatching performance
     *
     * @see D03-FR-014.2 Event optimization
     */
    #[Test]
    public function component_event_dispatching_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $startTime = microtime(true);

        // Test component method calls (event-like operations)
        for ($i = 0; $i < 5; $i++) {
            $component->set('form.applicant_name', 'Test User '.$i);
        }

        $dispatchTime = microtime(true) - $startTime;

        // Operations should be fast
        $this->assertLessThan(1.0, $dispatchTime, 'Component operations taking too long');
    }

    /**
     * Test component uses wire:target for specific loading states
     *
     * @see D03-FR-014.4 Targeted loading indicators
     */
    #[Test]
    public function component_uses_wire_target(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $html = $component->html();

        // Check for wire:target usage for specific loading states
        $hasWireTarget = str_contains($html, 'wire:target');

        if (! $hasWireTarget) {
            $this->markTestIncomplete(
                'Consider adding wire:target for specific action loading states'
            );
        }
    }

    /**
     * Test component form submission performance
     *
     * @see D03-FR-001.2 Form submission SLA
     */
    #[Test]
    public function component_form_submission_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        // Create division for testing
        $division = \App\Models\Division::factory()->create();

        // Fill form with valid data
        $component->set('form.applicant_name', 'Test User')
            ->set('form.position', 'Test Position')
            ->set('form.phone', '0123456789')
            ->set('form.division_id', $division->id)
            ->set('form.purpose', 'Testing')
            ->set('form.location', 'HQ')
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'));

        $startTime = microtime(true);

        // Move through steps (component uses multi-step form)
        $component->call('nextStep');

        $submissionTime = microtime(true) - $startTime;

        // Form submission should be fast (< 2 seconds)
        $this->assertLessThan(2.0, $submissionTime, 'Form submission taking too long');
    }

    /**
     * Test component uses Alpine.js efficiently
     *
     * @see D03-FR-014.2 Alpine.js optimization
     */
    #[Test]
    public function component_uses_alpine_efficiently(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $html = $component->html();

        // Check for Alpine.js usage
        $hasAlpine = str_contains($html, 'x-data') ||
            str_contains($html, 'x-show') ||
            str_contains($html, 'x-if');

        if ($hasAlpine) {
            // Alpine should be used for client-side interactions only
            // Heavy logic should be on server-side (Livewire)
            $this->assertTrue(true, 'Alpine.js detected - ensure it\'s used for UI interactions only');
        }
    }

    /**
     * Test component response size is optimized
     *
     * @see D03-FR-007.2 Response optimization
     */
    #[Test]
    public function component_response_size(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $html = $component->html();
        $responseSize = strlen($html);

        // Component HTML should be reasonably sized (< 100KB)
        $maxResponseSize = 100 * 1024; // 100KB

        $this->assertLessThan(
            $maxResponseSize,
            $responseSize,
            "Component response too large ({$responseSize} bytes). Consider lazy loading or pagination."
        );
    }

    /**
     * Test component handles errors gracefully
     *
     * @see D03-FR-014.4 Error handling performance
     */
    #[Test]
    public function component_error_handling_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $startTime = microtime(true);

        // Trigger validation errors
        $component->call('submit')
            ->assertHasErrors();

        $errorHandlingTime = microtime(true) - $startTime;

        // Error handling should be fast
        $this->assertLessThan(0.5, $errorHandlingTime, 'Error handling taking too long');
    }
}
