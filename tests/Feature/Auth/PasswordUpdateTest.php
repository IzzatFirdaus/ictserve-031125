<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Use compliant password: mixed case, numbers, symbols
        $newPassword = 'NewP@ssw0rd'.time();

        $component = Volt::test('profile.update-password-form')
            ->set('current_password', 'password')
            ->set('password', $newPassword)
            ->set('password_confirmation', $newPassword)
            ->call('updatePassword');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $this->assertTrue(Hash::check($newPassword, $user->refresh()->password));
    }

    #[Test]
    public function correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Use compliant password for new password field
        $newPassword = 'NewP@ssw0rd'.time();

        $component = Volt::test('profile.update-password-form')
            ->set('current_password', 'wrong-password')
            ->set('password', $newPassword)
            ->set('password_confirmation', $newPassword)
            ->call('updatePassword');

        $component
            ->assertHasErrors(['current_password'])
            ->assertNoRedirect();
    }
}
