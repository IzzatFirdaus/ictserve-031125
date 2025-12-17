<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Helpdesk;

use App\Livewire\Helpdesk\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_renders_for_an_authenticated_user(): void
    {
        Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $this->app->forgetInstance('encrypter');

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertStatus(200);
    }
}
