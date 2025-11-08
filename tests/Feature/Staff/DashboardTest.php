<?php

namespace Tests\Feature\Staff;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_staff_dashboard_with_loan_status_badge()
    {
        // 1. Create a user
        $user = User::factory()->create();

        // 2. Create a loan application with a status
        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatus::APPROVED,
        ]);

        // 3. Authenticate as the user
        $this->actingAs($user);

        // 4. Access the dashboard
        $response = $this->get('/dashboard');

        // 5. Assert that the dashboard is rendered successfully
        $response->assertStatus(200);

        // 6. Assert that the status badge is rendered with the correct status
        $response->assertSee('Approved');
    }
}
