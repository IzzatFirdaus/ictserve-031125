<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_render_index_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(AssetResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(AssetResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $asset = Asset::factory()->create();

        $this->get(AssetResource::getUrl('edit', ['record' => $asset]))
            ->assertSuccessful();
    }

    public function test_can_render_view_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $asset = Asset::factory()->create();

        $this->get(AssetResource::getUrl('view', ['record' => $asset]))
            ->assertSuccessful();
    }
}
