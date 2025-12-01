<?php

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\TwoFactorAuthentication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_authentication_can_be_enabled()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(TwoFactorAuthentication::class)
            ->call('enableTwoFactorAuthentication')
            ->assertSet('showingQrCode', true)
            ->assertSet('showingConfirmation', true);

        $user->refresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
    }

    public function test_two_factor_authentication_can_be_confirmed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test(TwoFactorAuthentication::class)
            ->call('enableTwoFactorAuthentication');

        $user->refresh();

        $google2fa = new Google2FA();
        $code = $google2fa->getCurrentOtp($user->two_factor_secret);

        $component->set('code', $code)
            ->call('confirmTwoFactorAuthentication')
            ->assertSet('showingQrCode', false)
            ->assertSet('showingConfirmation', false)
            ->assertSet('showingRecoveryCodes', true);

        $user->refresh();

        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function test_two_factor_authentication_can_be_disabled()
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'secret',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(TwoFactorAuthentication::class)
            ->call('disableTwoFactorAuthentication');

        $user->refresh();

        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertNull($user->two_factor_recovery_codes);
    }
}
