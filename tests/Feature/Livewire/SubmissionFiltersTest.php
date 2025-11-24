<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionFilters;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmissionFiltersTest extends TestCase
{
    #[Test]
    public function renders_successfully(): void
    {
        Livewire::test(SubmissionFilters::class)
            ->assertOk();
    }

    #[Test]
    public function indicates_when_filters_are_active(): void
    {
        $component = Livewire::test(SubmissionFilters::class);

        $this->assertFalse($component->get('hasActiveFilters'));

        $component->set('selectedStatuses', ['open']);

        $this->assertTrue($component->get('hasActiveFilters'));
    }

    #[Test]
    public function counts_active_filters(): void
    {
        $component = Livewire::test(SubmissionFilters::class);

        $this->assertSame(0, $component->get('activeFilterCount'));

        $component->set('selectedStatuses', ['open', 'closed']);
        $component->set('dateFrom', '2025-01-01');

        $this->assertSame(2, $component->get('activeFilterCount'));
    }

    #[Test]
    public function toggles_status_correctly(): void
    {
        Livewire::test(SubmissionFilters::class)
            ->call('toggleStatus', 'open')
            ->assertSet('selectedStatuses', ['open'])
            ->call('toggleStatus', 'open')
            ->assertSet('selectedStatuses', []);
    }

    #[Test]
    public function selects_all_statuses(): void
    {
        $component = Livewire::test(SubmissionFilters::class);

        $component->call('selectAllStatuses');

        $this->assertNotEmpty($component->get('selectedStatuses'));
    }

    #[Test]
    public function clears_filters(): void
    {
        Livewire::test(SubmissionFilters::class)
            ->set('selectedStatuses', ['open'])
            ->set('dateFrom', '2025-01-01')
            ->call('clearFilters')
            ->assertSet('selectedStatuses', [])
            ->assertSet('dateFrom', null);
    }
}
