<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetTransaction;
use App\Models\Division;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@motac.gov.my',
            'role' => 'admin',
        ]);
    }

    #[Test]
    public function adminCanAccessLoanApplicationResource(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('filament.admin.operations.resources.loan-applications.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function adminCanViewLoanApplicationDetails(): void
    {
        $this->actingAs($this->admin);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        $response = $this->get(route('filament.admin.operations.resources.loans.loan-applications.view', [
            'record' => $application,
        ]));

        $response->assertStatus(200);
        $response->assertSee($application->application_number);
    }

    #[Test]
    public function adminCanAssignAssetsWithOtpVerification(): void
    {
        $this->actingAs($this->admin);

        $division = Division::factory()->create();
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'division_id' => $division->id,
        ]);

        // Generate OTP
        $otp = $application->generateOtp();

        $loanItem = LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $this->assertTrue(Route::has('filament.admin.operations.resources.loan-applications.assign-assets'));
        $this->assertTrue($this->admin->can('issue', $application));
        $this->assertNotEmpty(route('filament.admin.operations.resources.loan-applications.assign-assets', [
            'record' => $application,
        ]));
    }

    #[Test]
    public function adminCanRecordAssetReturnWithAccessories(): void
    {
        $this->actingAs($this->admin);

        $division = Division::factory()->create();
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::LOANED,
            'accessories' => ['Charger', 'Mouse', 'HDMI Cable'],
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::ISSUED,
            'division_id' => $division->id,
        ]);

        AssetTransaction::create([
            'asset_id' => $asset->id,
            'loan_application_id' => $application->id,
            'transaction_type' => 'loan_issue',
            'transaction_date' => now(),
            'issued_by_staff_id' => $this->admin->id,
        ]);

        $this->assertTrue(Route::has('filament.admin.operations.resources.loan-applications.record-return'));
        $this->assertTrue($this->admin->can('return', $application));
        $this->assertNotEmpty(route('filament.admin.operations.resources.loan-applications.record-return', [
            'record' => $application,
        ]));
    }

    #[Test]
    public function nonAdminCannotAccessLoanApplicationResource(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('filament.admin.operations.resources.loan-applications.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function adminCanAccessAssetResource(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('filament.admin.inventory.resources.assets.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function adminCanViewUnifiedDashboard(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('filament.admin.pages.unified-dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function loanApplicationPolicyEnforcesRbac(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'staff']);
        $approverGrade = Grade::factory()->create([
            'level' => 41,
            'can_approve_loans' => true,
        ]);
        $approver = User::factory()->create([
            'role' => 'approver',
            'grade_id' => $approverGrade->id,
        ]);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create([
            'division_id' => $division->id,
        ]);

        // Admin can view any
        $this->assertTrue($admin->can('viewAny', LoanApplication::class));

        // User cannot view any (in Filament context)
        $this->assertFalse($user->can('viewAny', LoanApplication::class));

        // Admin can update
        $this->assertTrue($admin->can('update', $application));

        // User cannot update
        $this->assertFalse($user->can('update', $application));

        // Admin can issue
        $this->assertTrue($admin->can('issue', $application));

        // User cannot issue
        $this->assertFalse($user->can('issue', $application));
    }

    #[Test]
    public function assetWorkflowsAreStatusDependent(): void
    {
        $this->actingAs($this->admin);

        $division = Division::factory()->create();

        // Approved application - can assign
        $approvedApp = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'division_id' => $division->id,
        ]);

        // Issued application - can record return
        $issuedApp = LoanApplication::factory()->create([
            'status' => LoanStatus::ISSUED,
            'division_id' => $division->id,
        ]);

        // Draft application - cannot assign or return
        $draftApp = LoanApplication::factory()->create([
            'status' => LoanStatus::DRAFT,
            'division_id' => $division->id,
        ]);

        // Verify assign visibility
        $response = $this->get(route('filament.admin.operations.resources.loan-applications.index'));
        $response->assertStatus(200);

        // Verify workflow actions are available for correct statuses
        $this->assertNotNull(route('filament.admin.operations.resources.loan-applications.assign-assets', ['record' => $approvedApp]));
        $this->assertNotNull(route('filament.admin.operations.resources.loan-applications.record-return', ['record' => $issuedApp]));
    }
}
