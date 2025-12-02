<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoanApplicationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions if needed, or mock user permissions
        // For now, we assume a user with admin access
    }

    public function test_can_render_index_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(LoanApplicationResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(LoanApplicationResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $loanApplication = LoanApplication::factory()->create();

        $this->get(LoanApplicationResource::getUrl('edit', ['record' => $loanApplication]))
            ->assertSuccessful();
    }

    public function test_can_render_view_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $loanApplication = LoanApplication::factory()->create();

        $this->get(LoanApplicationResource::getUrl('view', ['record' => $loanApplication]))
            ->assertSuccessful();
    }
}
