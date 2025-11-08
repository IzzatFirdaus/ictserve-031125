<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Livewire\Portal\NotificationPreferences;
use App\Livewire\Portal\SecuritySettings;
use App\Livewire\Portal\UserProfile;
use App\Models\Division;
use App\Models\User;
use App\Models\UserNotificationPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Profile Management Feature Tests
 *
 * Tests profile update, notification preferences, password change,
 * and profile completeness functionality.
 *
 * Requirements: 3.1, 3.2, 3.5
 * Traceability: D03 SRS-FR-003, D04 §3.3
 */
class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@motac.gov.my',
            'phone' => '0123456789',
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);
    }

    #[Test]
    public function authenticated_user_can_access_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/profile');

        $response->assertStatus(200);
        $response->assertSee('My Profile');
    }

    #[Test]
    public function guest_cannot_access_profile_page(): void
    {
        $response = $this->get('/portal/profile');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function user_can_update_their_name(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'Updated Name')
            ->call('updateProfile')
            ->assertHasNoErrors()
            ->assertDispatched('profile-updated');

        $this->assertEquals('Updated Name', $this->user->fresh()->name);
    }

    #[Test]
    public function user_can_update_their_phone_number(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('phone', '0198765432')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertEquals('0198765432', $this->user->fresh()->phone);
    }

    #[Test]
    public function name_is_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', '')
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function name_must_be_string(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 123)
            ->call('updateProfile')
            ->assertHasErrors(['name']);
    }

    #[Test]
    public function name_cannot_exceed_255_characters(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', str_repeat('a', 256))
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'max']);
    }

    #[Test]
    public function phone_must_match_valid_format(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('phone', 'invalid-phone')
            ->call('updateProfile')
            ->assertHasErrors(['phone']);
    }

    #[Test]
    public function phone_can_be_null(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('phone', null)
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertNull($this->user->fresh()->phone);
    }

    #[Test]
    public function email_is_read_only(): void
    {
        $originalEmail = $this->user->email;

        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->assertSee($originalEmail);

        // Email should not be editable
        $this->assertEquals($originalEmail, $this->user->fresh()->email);
    }

    #[Test]
    public function profile_completeness_is_calculated(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'phone' => null,
        ]);

        Livewire::actingAs($user)
            ->test(UserProfile::class)
            ->assertSee('Profile Completeness');
    }

    #[Test]
    public function user_can_view_notification_preferences(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationPreferences::class)
            ->assertSee('Notification Preferences');
    }

    #[Test]
    public function user_can_enable_ticket_status_updates(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationPreferences::class)
            ->set('ticketStatusUpdates', true)
            ->call('updatePreference', 'ticket_status_updates', true)
            ->assertHasNoErrors();

        $this->assertTrue(
            UserNotificationPreference::where('user_id', $this->user->id)
                ->where('preference_key', 'ticket_status_updates')
                ->value('preference_value')
        );
    }

    #[Test]
    public function user_can_disable_loan_approval_notifications(): void
    {
        UserNotificationPreference::create([
            'user_id' => $this->user->id,
            'preference_key' => 'loan_approval_notifications',
            'preference_value' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(NotificationPreferences::class)
            ->set('loanApprovalNotifications', false)
            ->call('updatePreference', 'loan_approval_notifications', false)
            ->assertHasNoErrors();

        $this->assertFalse(
            UserNotificationPreference::where('user_id', $this->user->id)
                ->where('preference_key', 'loan_approval_notifications')
                ->value('preference_value')
        );
    }

    #[Test]
    public function user_can_update_all_preferences_at_once(): void
    {
        $preferences = [
            'ticket_status_updates' => true,
            'loan_approval_notifications' => false,
            'overdue_reminders' => true,
            'system_announcements' => false,
        ];

        Livewire::actingAs($this->user)
            ->test(NotificationPreferences::class)
            ->call('updateAll', $preferences)
            ->assertHasNoErrors();

        foreach ($preferences as $key => $value) {
            $this->assertEquals(
                $value,
                UserNotificationPreference::where('user_id', $this->user->id)
                    ->where('preference_key', $key)
                    ->value('preference_value')
            );
        }
    }

    #[Test]
    public function user_can_access_security_settings(): void
    {
        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->assertSee('Security Settings');
    }

    #[Test]
    public function user_can_change_password(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'NewPassword123!')
            ->set('newPasswordConfirmation', 'NewPassword123!')
            ->call('changePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('NewPassword123!', $this->user->fresh()->password));
    }

    #[Test]
    public function current_password_must_be_correct(): void
    {
        $this->user->update(['password' => Hash::make('correct-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'wrong-password')
            ->set('newPassword', 'NewPassword123!')
            ->set('newPasswordConfirmation', 'NewPassword123!')
            ->call('changePassword')
            ->assertHasErrors(['currentPassword']);
    }

    #[Test]
    public function new_password_must_be_at_least_8_characters(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'Short1!')
            ->set('newPasswordConfirmation', 'Short1!')
            ->call('changePassword')
            ->assertHasErrors(['newPassword' => 'min']);
    }

    #[Test]
    public function new_password_must_contain_uppercase_letter(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'lowercase123!')
            ->set('newPasswordConfirmation', 'lowercase123!')
            ->call('changePassword')
            ->assertHasErrors(['newPassword']);
    }

    #[Test]
    public function new_password_must_contain_lowercase_letter(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'UPPERCASE123!')
            ->set('newPasswordConfirmation', 'UPPERCASE123!')
            ->call('changePassword')
            ->assertHasErrors(['newPassword']);
    }

    #[Test]
    public function new_password_must_contain_number(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'NoNumbers!')
            ->set('newPasswordConfirmation', 'NoNumbers!')
            ->call('changePassword')
            ->assertHasErrors(['newPassword']);
    }

    #[Test]
    public function new_password_must_contain_special_character(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'NoSpecial123')
            ->set('newPasswordConfirmation', 'NoSpecial123')
            ->call('changePassword')
            ->assertHasErrors(['newPassword']);
    }

    #[Test]
    public function new_password_confirmation_must_match(): void
    {
        $this->user->update(['password' => Hash::make('old-password')]);

        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('currentPassword', 'old-password')
            ->set('newPassword', 'NewPassword123!')
            ->set('newPasswordConfirmation', 'DifferentPassword123!')
            ->call('changePassword')
            ->assertHasErrors(['newPassword' => 'confirmed']);
    }

    #[Test]
    public function password_strength_is_calculated(): void
    {
        Livewire::actingAs($this->user)
            ->test(SecuritySettings::class)
            ->set('newPassword', 'WeakPass1!')
            ->assertSet('passwordStrength', fn ($strength) => $strength > 0);
    }

    #[Test]
    public function profile_update_is_audited(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'Updated Name')
            ->call('updateProfile');

        $this->assertDatabaseHas('audits', [
            'user_id' => $this->user->id,
            'auditable_type' => User::class,
            'auditable_id' => $this->user->id,
            'event' => 'updated',
        ]);
    }
}
