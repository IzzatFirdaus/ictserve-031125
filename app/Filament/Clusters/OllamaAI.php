<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use App\Filament\Concerns\HandlesTranslations;
use App\Models\User;
use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;

/**
 * Ollama AI Cluster
 *
 * Kumpulan sumber AI: FAQ, Dokumen, Template Auto-Reply, Log Mesej, Prestasi
 *
 * Selaras dengan D00 v3.6.0 Four-Tier Role System:
 * - admin: Pengurusan operasi AI
 * - superuser: Tadbir urus penuh + akses Laravel Telescope
 *
 * @trace Requirements 5.1, 6.5 (Filament Admin Interface)
 */
class OllamaAI extends Cluster
{
    use HandlesTranslations;

    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cpu-chip';
    }

    public static function getNavigationLabel(): string
    {
        return static::trans('ollama.navigation_label', 'Ollama AI');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return static::trans('ollama.navigation_label', 'Ollama AI');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }
}
