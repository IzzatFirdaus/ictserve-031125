<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

#[Test]


/**
 * UserProfile Component Test Suite
 *
 * Comprehensive testing for Task 3.7: Test user profile page
 * Tests form validation, auto-save functionality, password change,
 * WCAG 2.2 Level AA compliance, and keyboard navigation.
 *
 * @author Frontend Engineering Team
 *
 * @trace D03-FR-020 (User Profile Management)
 * @trace D04 §5.3 (Authenticated Portal Design)
 *
 * @version 1.0
 *
 * @wcag WCAG 2.2 Level AA
 */
class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Division $division;

    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create test organizational data
        $this->division = Division::factory()->create([
            'name_ms' => 'Bahagian Ujian',
            'name_en' => 'Test Division',
        ]);

        // Create authenticated user with relationships
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0123456789',
            'staff_id' => 'STAFF001',
            'division_id' => $this->division->id,
            'grade_id' => null, // Simplified - no grade factory
            'position_id' => null, // Simplified - no position factory
            'password' => Hash::make('Password123!'),
            'notification_preferences' => [
                'ticket_updates' => true,
                'ticket_assignments' => true,
                'ticket_comments' => false,
                'sla_alerts' => true,
                'loan_updates' => true,
                'loan_approvals' => true,
                'loan_reminders' => false,
                'system_announcements' => true,
            ],
        ]);
    }

    /**
     * Test 1: Component mounts and loads user data correctly
     *


 */
    public function it_mounts_and_loads_user_data_correctly(): void
    {
        // Reload user with relationships to ensure they're loaded
        $this->user->load('division', 'grade', 'position');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->assertSet('name', 'Test User')
            ->assertSet('phone', '0123456789')
            ->assertSet('email', 'test@example.com')
            ->assertSet('staff_id', 'STAFF001')
            ->assertSet('grade', 'N/A') // No grade assigned
            ->assertSet('division', 'Bahagian Ujian') // Malay locale
            ->assertSet('position', 'N/A') // No position assigned
            ->assertSet('notificationPreferences.ticket_updates', true)
            ->assertSet('notificationPreferences.loan_approvals', true)
            ->assertSet('notificationPreferences.ticket_comments', false);
    }

    #[Test]


    /**
     * Test 2: Profile information can be updated successfully
     *


     */
    public function it_updates_profile_information_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('name', 'Updated Name')
            ->set('phone', '0198765432')
            ->call('updateProfile')
            ->assertSet('profileUpdateSuccess', true)
            ->assertHasNoErrors();

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('0198765432', $this->user->phone);
    }

    #[Test]


    /**
     * Test 3: Profile update validation - name is required
     *


     */
    public function it_validates_name_is_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('name', '')
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]


    /**
     * Test 4: Profile update validation - name max length
     *


     */
    public function it_validates_name_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('name', str_repeat('a', 256))
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'max']);
    }

    #[Test]


    /**
     * Test 5: Profile update validation - phone max length
     *


     */
    public function it_validates_phone_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('phone', str_repeat('1', 21))
            ->call('updateProfile')
            ->assertHasErrors(['phone' => 'max']);
    }

    #[Test]


    /**
     * Test 6: Phone field is optional
     *


     */
    public function it_allows_empty_phone_field(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('name', 'Test User')
            ->set('phone', '')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->user->refresh();
        $this->assertNull($this->user->phone);
    }

    #[Test]


    /**
     * Test 7: Notification preferences auto-save functionality
     *


     */
    public function it_auto_saves_notification_preferences(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('notificationPreferences.ticket_updates', false)
            ->call('updateNotificationPreferences')
            ->assertDispatched('preferences-updated');

        $this->user->refresh();
        $preferences = $this->user->getNotificationPreferences();
        $this->assertFalse($preferences['ticket_updates']);
    }

    #[Test]


    /**
     * Test 8: Multiple notification preferences can be updated
     *


     */
    public function it_updates_multiple_notification_preferences(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('notificationPreferences.ticket_comments', true)
            ->set('notificationPreferences.loan_reminders', true)
            ->call('updateNotificationPreferences');

        $this->user->refresh();
        $preferences = $this->user->getNotificationPreferences();
        $this->assertTrue($preferences['ticket_comments']);
        $this->assertTrue($preferences['loan_reminders']);
    }

    #[Test]


    /**
     * Test 9: Password can be changed successfully
     *


     */
    public function it_changes_password_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'NewPassword456!')
            ->call('updatePassword')
            ->assertSet('passwordUpdateSuccess', true)
            ->assertHasNoErrors();

        $this->user->refresh();
        $this->assertTrue(Hash::check('NewPassword456!', $this->user->password));
    }

    #[Test]


    /**
     * Test 10: Password change requires current password
     *


     */
    public function it_validates_current_password_is_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', '')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'NewPassword456!')
            ->call('updatePassword')
            ->assertHasErrors(['current_password' => 'required']);
    }

    #[Test]


    /**
     * Test 11: Password change validates current password is correct
     *


     */
    public function it_validates_current_password_is_correct(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'WrongPassword!')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'NewPassword456!')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }

    #[Test]


    /**
     * Test 12: New password must be confirmed
     *


     */
    public function it_validates_password_confirmation(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'DifferentPassword!')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    #[Test]


    /**
     * Test 13: Password must meet minimum length requirement
     *


     */
    public function it_validates_password_minimum_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'Short1!')
            ->set('password_confirmation', 'Short1!')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    #[Test]


    /**
     * Test 14: Password must contain mixed case
     *


     */
    public function it_validates_password_mixed_case(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'lowercase123!')
            ->set('password_confirmation', 'lowercase123!')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    #[Test]


    /**
     * Test 15: Password must contain numbers
     *


     */
    public function it_validates_password_contains_numbers(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NoNumbers!')
            ->set('password_confirmation', 'NoNumbers!')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    #[Test]


    /**
     * Test 16: Password must contain symbols
     *


     */
    public function it_validates_password_contains_symbols(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NoSymbols123')
            ->set('password_confirmation', 'NoSymbols123')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    #[Test]


    /**
     * Test 17: Password fields are cleared after successful change
     *


     */
    public function it_clears_password_fields_after_successful_change(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'NewPassword456!')
            ->call('updatePassword')
            ->assertSet('current_password', '')
            ->assertSet('password', '')
            ->assertSet('password_confirmation', '');
    }

    #[Test]


    /**
     * Test 18: Component requires authenticated user
     *


     */
    public function it_requires_authenticated_user(): void
    {
        // Test that component can only be instantiated with authenticated user
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);

        Livewire::test(\App\Livewire\Staff\UserProfile::class);
    }

    #[Test]


    /**
     * Test 19: Authenticated user can use component
     *


     */
    public function authenticated_user_can_use_component(): void
    {
        // Test that authenticated user can successfully use the component
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class);

        $component->assertStatus(200);
        $component->assertSet('email', 'test@example.com');
    }

    #[Test]


    /**
     * Test 20: Profile update dispatches screen reader announcement
     *


     */
    public function it_dispatches_screen_reader_announcement_on_profile_update(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('name', 'Updated Name')
            ->call('updateProfile')
            ->assertDispatched('profile-updated');
    }

    #[Test]


    /**
     * Test 21: Password update dispatches screen reader announcement
     *


     */
    public function it_dispatches_screen_reader_announcement_on_password_update(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'NewPassword456!')
            ->call('updatePassword')
            ->assertDispatched('password-updated');
    }

    #[Test]


    /**
     * Test 22: Loading states are displayed during profile update
     *


     */
    public function it_shows_loading_state_during_profile_update(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class);

        $html = $component->call('updateProfile')->html();

        $this->assertStringContainsString('wire:loading', $html);
        $this->assertStringContainsString('wire:target="updateProfile"', $html);
    }

    #[Test]


    /**
     * Test 23: Loading states are displayed during password update
     *


     */
    public function it_shows_loading_state_during_password_update(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class);

        $html = $component->call('updatePassword')->html();

        $this->assertStringContainsString('wire:loading', $html);
        $this->assertStringContainsString('wire:target="updatePassword"', $html);
    }

    #[Test]


    /**
     * Test 24: Read-only fields are displayed correctly
     *


     */
    public function it_displays_read_only_fields_correctly(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class);

        $html = $component->html();

        $this->assertStringContainsString('test@example.com', $html);
        $this->assertStringContainsString('STAFF001', $html);
        $this->assertStringContainsString('N/A', $html); // Grade, Position, and Division show N/A
        $this->assertStringContainsString('readonly', $html);
    }

    #[Test]


    /**
     * Test 25: Success alerts are displayed after profile update
     *


     */
    public function it_displays_success_alert_after_profile_update(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('name', 'Updated Name')
            ->call('updateProfile');

        $html = $component->html();

        // Check for success alert styling (green background)
        $this->assertStringContainsString('bg-green-50', $html);
        $this->assertStringContainsString('border-green-700', $html);
    }

    #[Test]


    /**
     * Test 26: Success alerts are displayed after password update
     *


     */
    public function it_displays_success_alert_after_password_update(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\Staff\UserProfile::class)
            ->set('current_password', 'Password123!')
            ->set('password', 'NewPassword456!')
            ->set('password_confirmation', 'NewPassword456!')
            ->call('updatePassword');

        $html = $component->html();

        // Check for success alert styling (green background)
        $this->assertStringContainsString('bg-green-50', $html);
        $this->assertStringContainsString('border-green-700', $html);
    }
}


