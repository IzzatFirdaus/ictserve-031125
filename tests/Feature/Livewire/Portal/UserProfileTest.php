<?php

use App\Livewire\Portal\UserProfile;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
	$this->user = User::factory()->create([
		'name' => 'Test User',
		'email' => 'test@example.com',
		'phone' => '0123456789',
	]);
	$this->actingAs($this->user);
});

it('renders successfully', function () {
	Livewire::test(UserProfile::class)
		->assertOk();
});

it('mounts with user data', function () {
	Livewire::test(UserProfile::class)
		->assertSet('name', 'Test User')
		->assertSet('phone', '0123456789');
});

it('has computed profileCompleteness property', function () {
	$component = Livewire::test(UserProfile::class);

	// User has name, email, and phone - should be 100%
	expect($component->profileCompleteness)->toBe(100);
});

it('calculates profile completeness correctly when phone is missing', function () {
	$this->user->update(['phone' => null]);

	$component = Livewire::test(UserProfile::class);

	// Without phone: 2/3 = 66%
	expect($component->profileCompleteness)->toBe(66);
});

it('updates profile successfully', function () {
	Livewire::test(UserProfile::class)
		->set('name', 'Updated Name')
		->set('phone', '0198765432')
		->call('updateProfile')
		->assertDispatched('profile-updated');

	expect($this->user->fresh()->name)->toBe('Updated Name');
	expect($this->user->fresh()->phone)->toBe('0198765432');
});

it('validates phone format', function () {
	Livewire::test(UserProfile::class)
		->set('phone', 'invalid')
		->call('updateProfile')
		->assertHasErrors(['phone']);
});
