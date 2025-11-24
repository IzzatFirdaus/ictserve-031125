<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Portal;

use App\Livewire\Portal\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0123456789',
        ]);

        $this->actingAs($this->user);
    }

    #[Test]
    public function renders_successfully(): void
    {
        Livewire::test(UserProfile::class)
            ->assertOk();
    }

    #[Test]
    public function mounts_with_user_data(): void
    {
        Livewire::test(UserProfile::class)
            ->assertSet('name', 'Test User')
            ->assertSet('phone', '0123456789');
    }

    #[Test]
    public function profile_completeness_is_full_when_contact_details_present(): void
    {
        $component = Livewire::test(UserProfile::class);

        $this->assertSame(100, $component->get('profileCompleteness'));
    }

    #[Test]
    public function profile_completeness_accounts_for_missing_phone(): void
    {
        $this->user->update(['phone' => null]);

        $component = Livewire::test(UserProfile::class);

        $this->assertSame(66, $component->get('profileCompleteness'));
    }

    #[Test]
    public function updates_profile_successfully(): void
    {
        Livewire::test(UserProfile::class)
            ->set('name', 'Updated Name')
            ->set('phone', '0198765432')
            ->call('updateProfile')
            ->assertDispatched('profile-updated');

        $this->assertSame('Updated Name', $this->user->fresh()->name);
        $this->assertSame('0198765432', $this->user->fresh()->phone);
    }

    #[Test]
    public function validates_phone_format(): void
    {
        Livewire::test(UserProfile::class)
            ->set('phone', 'invalid')
            ->call('updateProfile')
            ->assertHasErrors(['phone']);
    }
}
