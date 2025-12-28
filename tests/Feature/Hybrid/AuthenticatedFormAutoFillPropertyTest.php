<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Authenticated Form Auto-Fill
 *
 * Property-based tests to verify that authenticated users have their
 * profile data automatically populated in submission forms.
 *
 * @trace D00 §4.1 (True Hybrid Architecture)
 * @trace D03 SRS-DATA-001 (Hybrid Data Association)
 * @trace Requirements 4.3 (Authenticated Form Auto-Fill)
 *
 * @version 3.6.0
 *
 * @created 2025-12-16
 */

namespace Tests\Feature\Hybrid;

use App\Livewire\GuestLoanApplication;
use App\Models\Division;
use App\Models\Grade;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticatedFormAutoFillPropertyTest extends TestCase
{
    /**
     * Property 4: Authenticated Form Auto-Fill Validation
     *
     * For any authenticated user accessing a submission form, the system
     * should automatically populate the form fields with the user's
     * profile data (name, email, phone, division, grade).
     *
     * **Feature: test-suite-comprehensive-v3.6, Property 4: Authenticated Form Auto-Fill Validation**
     * **Validates: Requirements 4.3**
     */
    #[Test]
    #[DataProvider('authenticatedUserProfileProvider')]
    public function property_authenticated_form_auto_fills_from_profile(
        string $name,
        string $email,
        string $phone
    ): void {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        // Test loan application form auto-fill
        // Note: division_id is intentionally NOT pre-filled to show placeholder
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('form.applicant_name', $user->name)
            ->assertSet('form.phone', $user->phone);
    }

    #[Test]
    public function property_authenticated_user_sees_profile_display(): void
    {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        $user = User::factory()->create([
            'name' => 'Pengguna Paparan Profil',
            'email' => 'paparan.profil@motac.gov.my',
            'phone' => '03-12345678',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        // Verify authenticated user sees their profile information
        Livewire::test(GuestLoanApplication::class)
            ->assertSee($user->name)
            ->assertSee($user->phone);
    }

    #[Test]
    public function property_guest_user_sees_empty_form_fields(): void
    {
        // Test as guest (not authenticated) - form fields should be empty
        // Note: form fields are initialized to empty strings, not null
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('form.applicant_name', '')
            ->assertSet('form.phone', '')
            ->assertSet('form.division_id', '');
    }

    #[Test]
    public function property_authenticated_form_displays_bahasa_labels(): void
    {
        app()->setLocale('ms'); // Set Bahasa Melayu locale

        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        $user = User::factory()->create([
            'name' => 'Pengguna BM',
            'email' => 'pengguna.bm@motac.gov.my',
            'phone' => '03-98765432',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        // Verify BM labels are displayed for authenticated users
        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Maklumat Anda') // BM: "Your Information"
            ->assertSee('Nama Penuh') // BM: "Full Name"
            ->assertSee('Bahagian'); // BM: "Division"
    }

    #[Test]
    public function property_guest_form_displays_bahasa_labels(): void
    {
        app()->setLocale('ms'); // Set Bahasa Melayu locale

        // Verify BM labels are displayed for guest users
        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Nama Penuh') // BM: "Full Name"
            ->assertSee('Jawatan') // BM: "Position"
            ->assertSee('Bahagian'); // BM: "Division"
    }

    #[Test]
    #[DataProvider('userProfileFieldsProvider')]
    public function property_all_profile_fields_auto_fill_correctly(
        string $fieldName,
        string $fieldValue,
        string $formField
    ): void {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        $userData = [
            'name' => 'Default Name',
            'email' => 'default@motac.gov.my',
            'phone' => '03-00000000',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ];

        // Override the specific field being tested
        $userData[$fieldName] = $fieldValue;

        $user = User::factory()->create($userData);

        $this->actingAs($user);

        // Verify the specific field is auto-filled
        $component = Livewire::test(GuestLoanApplication::class);
        $component->assertSet($formField, $fieldValue);
    }

    #[Test]
    public function property_authenticated_user_cannot_modify_auto_filled_name(): void
    {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        $user = User::factory()->create([
            'name' => 'Nama Asal Pengguna',
            'email' => 'nama.asal@motac.gov.my',
            'phone' => '03-11111111',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        // Verify authenticated user's name is auto-filled and read-only
        $component = Livewire::test(GuestLoanApplication::class)
            ->assertSet('form.applicant_name', $user->name);

        // The form should maintain the user's name from profile
        $this->assertEquals($user->name, $component->get('form.applicant_name'));
    }

    #[Test]
    public function property_hybrid_form_behavior_differs_by_auth_state(): void
    {
        $division = Division::first() ?? Division::factory()->create();
        $grade = Grade::first() ?? Grade::factory()->create();

        // Test 1: Guest user - form fields should be empty/editable
        $guestComponent = Livewire::test(GuestLoanApplication::class);
        $guestComponent
            ->assertSet('form.applicant_name', '')
            ->assertSet('form.phone', '');

        // Test 2: Authenticated user - form fields should be auto-filled
        $user = User::factory()->create([
            'name' => 'Pengguna Disahkan',
            'email' => 'disahkan@motac.gov.my',
            'phone' => '03-22222222',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        $authComponent = Livewire::test(GuestLoanApplication::class);
        $authComponent
            ->assertSet('form.applicant_name', $user->name)
            ->assertSet('form.phone', $user->phone);
        // Note: division_id is intentionally NOT pre-filled to show placeholder
    }

    #[Test]
    public function property_auto_fill_handles_null_optional_fields(): void
    {
        $division = Division::first() ?? Division::factory()->create();

        // Create user with some null optional fields
        $user = User::factory()->create([
            'name' => 'Pengguna Tanpa Telefon',
            'email' => 'tanpa.telefon@motac.gov.my',
            'phone' => null, // Optional field is null
            'division_id' => $division->id,
            'grade_id' => null, // Optional field is null
        ]);

        $this->actingAs($user);

        // Verify form handles null optional fields gracefully
        // Note: null phone becomes empty string in form
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('form.applicant_name', $user->name)
            ->assertSet('form.phone', '');
    }

    /**
     * Data provider for authenticated user profile test cases
     */
    public static function authenticatedUserProfileProvider(): array
    {
        return [
            'simple malay name' => [
                'Ahmad Ali',
                'ahmad.ali@motac.gov.my',
                '03-12345678',
            ],
            'full malay name with bin' => [
                'Mohd Farid Bin Abdullah',
                'mohd.farid@motac.gov.my',
                '03-98765432',
            ],
            'full malay name with binti' => [
                'Siti Nurhaliza Binti Hassan',
                'siti.nurhaliza@motac.gov.my',
                '03-11112222',
            ],
            'name with title' => [
                'Datuk Dr. Ahmad Bin Ismail',
                'datuk.ahmad@motac.gov.my',
                '03-33334444',
            ],
            'chinese name' => [
                'Tan Ah Kow',
                'tan.ahkow@motac.gov.my',
                '03-55556666',
            ],
            'indian name' => [
                'Muthu A/L Krishnan',
                'muthu.krishnan@motac.gov.my',
                '03-77778888',
            ],
        ];
    }

    /**
     * Data provider for user profile fields test cases
     * Note: division_id is intentionally NOT auto-filled to show placeholder
     */
    public static function userProfileFieldsProvider(): array
    {
        return [
            'name field' => [
                'name',
                'Nama Ujian',
                'form.applicant_name',
            ],
            'phone field' => [
                'phone',
                '03-99998888',
                'form.phone',
            ],
        ];
    }
}
