<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\RecentActivity;
use App\Models\PortalActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecentActivityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function renders_successfully(): void
    {
        Livewire::test(RecentActivity::class)
            ->assertOk();
    }

    #[Test]
    public function exposes_paginated_activities(): void
    {
        PortalActivity::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(RecentActivity::class);
        $activities = $component->get('activities');

        $this->assertInstanceOf(LengthAwarePaginator::class, $activities);
        $this->assertCount(5, $activities);
    }

    #[Test]
    public function filters_activities_by_type(): void
    {
        PortalActivity::factory()->create([
            'user_id' => $this->user->id,
            'activity_type' => 'login',
        ]);

        PortalActivity::factory()->create([
            'user_id' => $this->user->id,
            'activity_type' => 'submission',
        ]);

        Livewire::test(RecentActivity::class)
            ->set('activityType', 'login')
            ->assertSee('login');
    }

    #[Test]
    public function clears_filters(): void
    {
        Livewire::test(RecentActivity::class)
            ->set('activityType', 'login')
            ->set('search', 'test')
            ->call('clearFilters')
            ->assertSet('activityType', 'all')
            ->assertSet('search', '');
    }

    #[Test]
    public function available_activity_types_are_exposed(): void
    {
        $component = Livewire::test(RecentActivity::class);
        $availableActivityTypes = $component->get('availableActivityTypes');

        $this->assertIsArray($availableActivityTypes);
        $this->assertArrayHasKey('all', $availableActivityTypes);
    }
}
