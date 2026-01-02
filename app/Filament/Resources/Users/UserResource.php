<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Filament\Clusters\Management;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

/**
 * Component name: User Resource
 * Description: Filament resource for admin panel CRUD operations on users with role-based permissions
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 (User Management)
 * @trace D04 §3.1 (Admin Panel)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
class UserResource extends Resource
{
    use HandlesTranslations;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return static::trans('filament.resources.user.singular', 'Pengguna');
    }

    public static function getPluralModelLabel(): string
    {
        return static::trans('filament.resources.user.plural', 'Pengguna');
    }

    public static function getNavigationLabel(): string
    {
        return static::trans('filament.resources.user.navigation', 'Pengguna');
    }

    /**
     * Filament will automatically use UserPolicy for authorization.
     * Policy methods: viewAny(), view(), create(), update(), delete(), restore(), forceDelete()
     *
     * @see \App\Policies\UserPolicy
     */

    /**
     * Control navigation visibility based on user permissions.
     * Only show in navigation if user has permission to view users.
     */
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user !== null && $user->can('viewAny', User::class);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<User>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
