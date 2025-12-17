<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Polisi Kebenaran FAQ (FAQ Authorization Policy)
 *
 * Mengawal akses kepada sumber FAQ mengikut peranan pengguna
 * dalam sistem ICTServe v3.6.0 True Hybrid Architecture.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D00 v3.6.0 (Four-Tier Role System)
 *
 * @requirements 4.1, 4.2, 6.5
 */
class FaqPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan sama ada pengguna boleh melihat senarai FAQ
     *
     * Akses: staff, approver, admin, superuser
     */
    public function viewAny(?User $user): bool
    {
        // Tetamu boleh query FAQ melalui API awam
        // Pengguna berdaftar boleh lihat senarai
        return true;
    }

    /**
     * Tentukan sama ada pengguna boleh melihat FAQ tertentu
     *
     * Akses: staff, approver, admin, superuser
     */
    public function view(?User $user, Faq $faq): bool
    {
        // Semua pengguna (termasuk tetamu) boleh lihat FAQ
        return true;
    }

    /**
     * Tentukan sama ada pengguna boleh mencipta FAQ
     *
     * Akses: admin, superuser sahaja
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengemaskini FAQ
     *
     * Akses: admin, superuser sahaja
     */
    public function update(User $user, Faq $faq): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh memadam FAQ
     *
     * Akses: admin, superuser sahaja
     */
    public function delete(User $user, Faq $faq): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh memulihkan FAQ yang dipadam
     *
     * Akses: admin, superuser sahaja
     */
    public function restore(User $user, Faq $faq): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh memadam FAQ secara kekal
     *
     * Akses: admin, superuser sahaja
     */
    public function forceDelete(User $user, Faq $faq): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengimport FAQ secara pukal
     *
     * Akses: admin, superuser sahaja
     */
    public function import(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengeksport FAQ
     *
     * Akses: admin, superuser sahaja
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengurus tag FAQ
     *
     * Akses: admin, superuser sahaja
     */
    public function manageTags(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }
}
