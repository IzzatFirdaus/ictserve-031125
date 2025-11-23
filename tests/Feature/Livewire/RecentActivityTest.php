<?php

use App\Livewire\RecentActivity;
use App\Models\PortalActivity;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
	$this->user = User::factory()->create();
	$this->actingAs($this->user);
});

it('renders successfully', function () {
	Livewire::test(RecentActivity::class)
		->assertOk();
});

it('has computed activities property', function () {
	// Create test activities
	PortalActivity::factory()->count(5)->create([
		'user_id' => $this->user->id,
	]);

	$component = Livewire::test(RecentActivity::class);

	expect($component->activities)->toBeInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);
	expect($component->activities->count())->toBe(5);
});

it('filters activities by type', function () {
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
});

it('clears filters', function () {
	Livewire::test(RecentActivity::class)
		->set('activityType', 'login')
		->set('search', 'test')
		->call('clearFilters')
		->assertSet('activityType', 'all')
		->assertSet('search', '');
});

it('computed availableActivityTypes returns array', function () {
	$component = Livewire::test(RecentActivity::class);

	expect($component->availableActivityTypes)->toBeArray();
	expect($component->availableActivityTypes)->toHaveKey('all');
});
