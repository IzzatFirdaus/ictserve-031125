<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Polisi Kebenaran Dokumen (Document Authorization Policy)
 *
 * Mengawal akses kepada sumber Dokumen AI mengikut peranan pengguna
 * dalam sistem ICTServe v3.6.0 True Hybrid Architecture.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D00 v3.6.0 (Four-Tier Role System)
 *
 * @requirements 2.1, 2.5, 7.1
 */
class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan sama ada pengguna boleh melihat senarai dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh melihat dokumen tertentu
     *
     * Akses: admin, superuser, atau pemilik dokumen
     */
    public function view(User $user, Document $document): bool
    {
        // Admin dan superuser boleh lihat semua
        if ($user->hasAnyRole(['admin', 'superuser'])) {
            return true;
        }

        // Pemilik dokumen boleh lihat dokumen sendiri
        return $document->uploaded_by === $user->id;
    }

    /**
     * Tentukan sama ada pengguna boleh mencipta dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengemaskini dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function update(User $user, Document $document): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh memadam dokumen
     *
     * Akses: superuser sahaja
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Tentukan sama ada pengguna boleh memulihkan dokumen yang dipadam
     *
     * Akses: superuser sahaja
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Tentukan sama ada pengguna boleh memadam dokumen secara kekal
     *
     * Akses: superuser sahaja
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Tentukan sama ada pengguna boleh memuat naik dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function upload(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh memproses semula dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function reprocess(User $user, Document $document): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh melihat chunks dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function viewChunks(User $user, Document $document): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengeksport dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh melihat statistik dokumen
     *
     * Akses: admin, superuser sahaja
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }
}
