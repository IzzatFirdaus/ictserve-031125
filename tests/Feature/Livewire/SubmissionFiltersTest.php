<?php

use App\Livewire\SubmissionFilters;
use Livewire\Livewire;

it('renders successfully', function () {
	Livewire::test(SubmissionFilters::class)
		->assertOk();
});

it('has computed hasActiveFilters property', function () {
	$component = Livewire::test(SubmissionFilters::class);

	// Initially no active filters
	expect($component->hasActiveFilters)->toBeFalse();

	// Set a status filter
	$component->set('selectedStatuses', ['open']);
	expect($component->hasActiveFilters)->toBeTrue();
});

it('has computed activeFilterCount property', function () {
	$component = Livewire::test(SubmissionFilters::class);

	expect($component->activeFilterCount)->toBe(0);

	// Add filters
	$component->set('selectedStatuses', ['open', 'closed']);
	$component->set('dateFrom', '2025-01-01');

	expect($component->activeFilterCount)->toBe(2);
});

it('toggles status correctly', function () {
	Livewire::test(SubmissionFilters::class)
		->call('toggleStatus', 'open')
		->assertSet('selectedStatuses', ['open'])
		->call('toggleStatus', 'open')
		->assertSet('selectedStatuses', []);
});

it('selects all statuses', function () {
	$component = Livewire::test(SubmissionFilters::class);

<?php

use App\Livewire\SubmissionFilters;
use Livewire\Livewire;

it('renders successfully', function () {
	Livewire::test(SubmissionFilters::class)
		->assertOk();
});

it('has computed hasActiveFilters property', function () {
	$component = Livewire::test(SubmissionFilters::class);

	// Initially no active filters
	expect($component->hasActiveFilters)->toBeFalse();

	// Set a status filter
	$component->set('selectedStatuses', ['open']);
	expect($component->hasActiveFilters)->toBeTrue();
});

it('has computed activeFilterCount property', function () {
	$component = Livewire::test(SubmissionFilters::class);

	expect($component->activeFilterCount)->toBe(0);

	// Add filters
	$component->set('selectedStatuses', ['open', 'closed']);
	$component->set('dateFrom', '2025-01-01');

	expect($component->activeFilterCount)->toBe(2);
});

it('toggles status correctly', function () {
	Livewire::test(SubmissionFilters::class)
		->call('toggleStatus', 'open')
		->assertSet('selectedStatuses', ['open'])
		->call('toggleStatus', 'open')
		->assertSet('selectedStatuses', []);
});

it('selects all statuses', function () {
	$component = Livewire::test(SubmissionFilters::class);

	$component->call('selectAllStatuses');

	expect($component->selectedStatuses)->not->toBeEmpty();
});

it('clears filters', function () {
	Livewire::test(SubmissionFilters::class)
		->set('selectedStatuses', ['open'])
		->set('dateFrom', '2025-01-01')
		->call('clearFilters')
		->assertSet('selectedStatuses', [])
		->assertSet('dateFrom', null);
});
