<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'superuser']));
    }

    public function test_can_render_index_page(): void
    {
        $this->get(UserResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_page(): void
    {
        $this->get(UserResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_page(): void
    {
        $user = User::factory()->create();
        $this->get(UserResource::getUrl('edit', ['record' => $user]))
            ->assertSuccessful();
    }

    public function test_can_create_user(): void
    {
        $newData = User::factory()->make();

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => $newData->name,
                'email' => $newData->email,
                'password' => 'password',
                'role' => 'staff',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => $newData->email,
        ]);
    }

    public function test_can_edit_user(): void
    {
        $user = User::factory()->create();
        $newName = 'Updated Name';

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => $newName,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $newName,
        ]);
    }
}
