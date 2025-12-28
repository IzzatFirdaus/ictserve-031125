<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * UserResourceTest - Filament User Resource Testing
 *
 * Tests CRUD operations, BM content assertions, and Livewire component testing
 * for the UserResource in Filament admin panel.
 *
 * @trace Requirements 1.1, 10.1, 10.4
 */
class UserResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'superuser']));
    }

    #[Test]
    public function can_render_index_page(): void
    {
        $this->get(UserResource::getUrl('index'))
            ->assertSuccessful()
            ->assertSee('Nama') // BM table header
            ->assertSee('E-mel') // BM table header
            ->assertSee('Peranan'); // BM table header
    }

    #[Test]
    public function can_render_create_page(): void
    {
        $this->get(UserResource::getUrl('create'))
            ->assertSuccessful()
            ->assertSee('Maklumat Asas') // BM section title
            ->assertSee('Maklumat Organisasi') // BM section title
            ->assertSee('Maklumat Perhubungan'); // BM section title
    }

    #[Test]
    public function can_render_edit_page(): void
    {
        $user = User::factory()->create();
        $this->get(UserResource::getUrl('edit', ['record' => $user]))
            ->assertSuccessful()
            ->assertSee($user->name)
            ->assertSee('Maklumat Asas'); // BM content assertion
    }

    #[Test]
    public function can_list_users_with_livewire(): void
    {
        $users = User::factory()->count(3)->create();

        Livewire::test(ListUsers::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($users);
    }

    #[Test]
    public function can_create_user_with_livewire(): void
    {
        $userData = [
            'name' => 'Ahmad Bin Ali',
            'email' => 'ahmad@motac.gov.my',
            'password' => 'password123',
            'role' => 'staff',
            'is_active' => true,
        ];

        Livewire::test(CreateUser::class)
            ->fillForm($userData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Bin Ali',
            'email' => 'ahmad@motac.gov.my',
            'role' => 'staff',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function can_validate_user_creation_form(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => '',
                'email' => 'invalid-email',
                'password' => '',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'required',
            ]);
    }

    #[Test]
    public function can_edit_user_with_livewire(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@motac.gov.my',
        ]);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
                'email' => 'updated@motac.gov.my',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@motac.gov.my',
        ]);
    }

    #[Test]
    public function can_delete_user(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction('delete');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    #[Test]
    public function displays_bahasa_melayu_form_labels(): void
    {
        Livewire::test(CreateUser::class)
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('email')
            ->assertFormFieldExists('password')
            ->assertFormFieldExists('role')
            ->assertFormFieldExists('is_active')
            ->assertSee(__('users.full_name'))
            ->assertSee(__('users.email_address'))
            ->assertSee(__('users.password'))
            ->assertSee(__('users.role'))
            ->assertSee(__('users.active_status'));
    }

    #[Test]
    public function displays_role_options_in_bahasa_melayu(): void
    {
        Livewire::test(CreateUser::class)
            ->assertSee('Staf') // BM role option
            ->assertSee('Pelulus (Gred 41+)') // BM role option
            ->assertSee('Admin') // BM role option
            ->assertSee('Superuser'); // BM role option
    }

    #[Test]
    public function non_superuser_cannot_change_roles(): void
    {
        // Create a regular admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create(['role' => 'staff']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldIsDisabled('role');
    }

    #[Test]
    public function can_filter_users_by_role(): void
    {
        $staffUsers = User::factory()->count(2)->create(['role' => 'staff']);
        $adminUsers = User::factory()->count(3)->create(['role' => 'admin']);

        Livewire::test(ListUsers::class)
            ->filterTable('role', 'staff')
            ->assertCanSeeTableRecords($staffUsers)
            ->assertCanNotSeeTableRecords($adminUsers);
    }

    #[Test]
    public function can_search_users_by_name(): void
    {
        $searchableUser = User::factory()->create(['name' => 'Ahmad Searchable']);
        $otherUser = User::factory()->create(['name' => 'Other User']);

        Livewire::test(ListUsers::class)
            ->searchTable('Ahmad')
            ->assertCanSeeTableRecords([$searchableUser])
            ->assertCanNotSeeTableRecords([$otherUser]);
    }
}
