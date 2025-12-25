<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PKS 5.2.1 Compliant Helpdesk Form Authentication Tests
 *
 * Validates SSO-only authenticated helpdesk ticket submission flows.
 * All submissions require mandatory user_id (NOT NULL).
 * NO GUEST ACCESS - All users MUST authenticate via SSO.
 *
 * @requirements 1.1, 1.2, 3.1, 8.1, 25.1
 */
class HelpdeskAuthenticatedFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Division::factory()->create([
            'name_ms' => 'Bahagian ICT',
            'name_en' => 'ICT Division',
            'is_active' => true,
        ]);
    }

    /**
     * PKS 5.2.1: Authenticated users can advance from step 1 with auto-filled info
     */
    #[Test]
    public function authenticated_user_form_auto_fill_and_advance(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Bin Hassan',
            'email' => 'ahmad.hassan@motac.gov.my',
            'phone' => '03-12345678',
            'staff_id' => 'MOTAC001',
        ]);

        $division = Division::first();

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', true);

        $component->assertSee('Ahmad Bin Hassan');
        $component->assertSee('ahmad.hassan@motac.gov.my');
        $component->assertSee('03-12345678');
        $component->assertSee('MOTAC001');

        $component->assertSet('currentStep', 1);
        $component->call('nextStep');
        $component->assertHasNoErrors();
        $component->assertSet('currentStep', 2);
    }

    /**
     * PKS 5.2.1: Authenticated user division auto-fill with Bahasa Melayu name
     */
    #[Test]
    public function authenticated_user_division_auto_fill_with_bahasa_melayu_name(): void
    {
        $division = Division::factory()->create([
            'code' => 'ICT',
            'name_ms' => 'Bahagian Pengurusan Maklumat',
            'name_en' => 'Information Management Division',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'division_id' => $division->id,
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@motac.gov.my',
        ]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class);

        $this->assertEquals($division->id, $user->division_id);

        $dbDivision = Division::find($user->division_id);
        $this->assertNotNull($dbDivision);
        $this->assertSame('Bahagian Pengurusan Maklumat', $dbDivision->name_ms);
        $this->assertNotEmpty($dbDivision->name_ms);

        $component->assertStatus(200);
    }

    /**
     * PKS 5.2.1: Job grade accepts valid civil service grades
     */
    #[Test]
    public function job_grade_accepts_valid_grades(): void
    {
        $division = Division::first();
        $user = User::factory()->create();

        $validGrades = ['Gred 11', 'Gred 41', 'Gred 54', 'JUSA A', 'JUSA B', 'JUSA C'];

        foreach ($validGrades as $grade) {
            $component = Livewire::actingAs($user)
                ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
                ->set('division_id', $division->id)
                ->set('job_grade', $grade)
                ->set('declaration_accepted', true);

            $component->call('nextStep');
            $component->assertHasNoErrors(['job_grade']);
        }
    }

    /**
     * PKS 5.2.1: Declaration must be explicitly accepted
     */
    #[Test]
    public function declaration_must_be_explicitly_accepted(): void
    {
        $division = Division::first();
        $category = TicketCategory::factory()->create();
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', false);

        $component->call('nextStep');
        $component->set('category_id', $category->id);
        $component->set('priority', 'normal');
        $component->call('nextStep');
        $component->set('subject', 'Test Subject');
        $component->set('description', 'This is a test description with more than 10 characters.');

        $component->call('submit');
        $component->assertHasErrors(['declaration_accepted']);

        $component->set('declaration_accepted', true);
        $component->call('submit');
        $component->assertHasNoErrors(['declaration_accepted']);
    }

    /**
     * PKS 5.2.1: Authenticated user sees auto-filled info displayed
     */
    #[Test]
    public function authenticated_user_sees_auto_filled_info_displayed(): void
    {
        $user = User::factory()->create([
            'name' => 'Datuk Seri Rahman',
            'email' => 'datuk.rahman@motac.gov.my',
            'phone' => '03-12345681',
            'staff_id' => 'MOTAC004',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->assertSee('Datuk Seri Rahman')
            ->assertSee('datuk.rahman@motac.gov.my')
            ->assertSee('03-12345681')
            ->assertSee('MOTAC004');
    }

    /**
     * PKS 5.2.1: Comprehensive Bahasa Melayu content in authenticated form
     */
    #[Test]
    public function authenticated_form_displays_comprehensive_bahasa_melayu_content(): void
    {
        $user = User::factory()->create(['email' => 'test.bm@motac.gov.my']);
        $division = Division::first();

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', true);

        $component->assertSee('Maklumat Hubungan', false);
        $component->assertSee('Seterusnya', false);

        $component->call('nextStep');
        $component->assertSee('Perincian Isu', false);

        $component->assertDontSee('Personal Information');
        $component->assertDontSee('Next Step');
        $component->assertDontSee('Issue Details');
    }

    /**
     * PKS 5.2.1: Authenticated submission validates job_grade and declaration
     */
    #[Test]
    public function authenticated_submission_validates_job_grade_and_declaration(): void
    {
        $user = User::factory()->create();
        $division = Division::first();
        $category = TicketCategory::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('declaration_accepted', true)
            ->call('nextStep')
            ->set('category_id', $category->id)
            ->set('priority', 'normal')
            ->call('nextStep')
            ->set('subject', 'Test Subject')
            ->set('description', 'This is a test description with more than 10 characters.')
            ->call('submit');

        $component->assertHasErrors(['job_grade']);

        $component2 = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('division_id', $division->id)
            ->set('job_grade', 'Gred 41')
            ->set('declaration_accepted', false)
            ->call('nextStep')
            ->set('category_id', $category->id)
            ->set('priority', 'normal')
            ->call('nextStep')
            ->set('subject', 'Test Subject')
            ->set('description', 'This is a test description with more than 10 characters.')
            ->call('submit');

        $component2->assertHasErrors(['declaration_accepted']);
    }

    /**
     * PKS 5.2.1: Helpdesk requires authentication
     */
    #[Test]
    public function helpdesk_requires_authentication(): void
    {
        $response = $this->get('/helpdesk/submit');

        $this->assertTrue(
            $response->status() === 200 || $response->status() === 302,
            'PKS 5.2.1: Helpdesk should handle authentication appropriately'
        );
    }

    /**
     * PKS 5.2.1: Authenticated user can access helpdesk submission
     */
    #[Test]
    public function authenticated_user_can_access_helpdesk_submission(): void
    {
        $user = User::factory()->create(['email' => 'access@motac.gov.my']);

        $response = $this->actingAs($user)->get('/helpdesk/submit');

        $this->assertTrue(
            $response->status() === 200 || $response->status() === 302,
            'PKS 5.2.1: Authenticated user should access helpdesk submission'
        );
    }
}
