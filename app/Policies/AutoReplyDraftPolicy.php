<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AutoReplyDraft;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Polisi Kebenaran Draf Balasan Auto (Auto Reply Draft Authorization Policy)
 *
 * Mengawal akses kepada sumber Draf Balasan Auto mengikut peranan pengguna
 * dalam sistem ICTServe v3.6.0 True Hybrid Architecture.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D00 v3.6.0 (Four-Tier Role System)
 *
 * @requirements 3.1, 3.2, 3.4, 3.6
 */
class AutoReplyDraftPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan sama ada pengguna boleh melihat senarai draf
     *
     * Akses: approver, admin, superuser
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['approver', 'admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh melihat draf tertentu
     *
     * Akses: approver, admin, superuser, atau penjana draf
     */
    public function view(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        // Admin dan superuser boleh lihat semua
        if ($user->hasAnyRole(['admin', 'superuser'])) {
            return true;
        }

        // Approver boleh lihat draf yang menunggu kelulusan
        if ($user->hasRole('approver') && $autoReplyDraft->status === AutoReplyDraft::STATUS_PENDING_REVIEW) {
            return true;
        }

        // Penjana draf boleh lihat draf sendiri
        return $autoReplyDraft->generated_by === $user->id;
    }

    /**
     * Tentukan sama ada pengguna boleh mencipta draf
     *
     * Akses: admin, superuser sahaja
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengemaskini draf
     *
     * Akses: admin, superuser sahaja (draf belum diluluskan)
     */
    public function update(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        // Hanya draf atau pending_review boleh dikemaskini
        if (! in_array($autoReplyDraft->status, [
            AutoReplyDraft::STATUS_DRAFT,
            AutoReplyDraft::STATUS_PENDING_REVIEW,
        ])) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh memadam draf
     *
     * Akses: superuser sahaja
     */
    public function delete(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Tentukan sama ada pengguna boleh memulihkan draf yang dipadam
     *
     * Akses: superuser sahaja
     */
    public function restore(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Tentukan sama ada pengguna boleh memadam draf secara kekal
     *
     * Akses: superuser sahaja
     */
    public function forceDelete(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        return $user->hasRole('superuser');
    }

    /**
     * Tentukan sama ada pengguna boleh meluluskan draf
     *
     * Akses: approver, admin, superuser
     */
    public function approve(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        // Hanya draf pending_review boleh diluluskan
        if ($autoReplyDraft->status !== AutoReplyDraft::STATUS_PENDING_REVIEW) {
            return false;
        }

        // Penjana tidak boleh meluluskan draf sendiri
        if ($autoReplyDraft->generated_by === $user->id) {
            return false;
        }

        return $user->hasAnyRole(['approver', 'admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh menolak draf
     *
     * Akses: approver, admin, superuser
     */
    public function reject(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        // Hanya draf pending_review boleh ditolak
        if ($autoReplyDraft->status !== AutoReplyDraft::STATUS_PENDING_REVIEW) {
            return false;
        }

        return $user->hasAnyRole(['approver', 'admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh menghantar draf yang diluluskan
     *
     * Akses: admin, superuser sahaja
     */
    public function send(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        // Hanya draf yang diluluskan boleh dihantar
        if ($autoReplyDraft->status !== AutoReplyDraft::STATUS_APPROVED) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh menjana draf baharu
     *
     * Akses: admin, superuser sahaja
     */
    public function generate(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh melihat sejarah kelulusan
     *
     * Akses: approver, admin, superuser
     */
    public function viewApprovalHistory(User $user, AutoReplyDraft $autoReplyDraft): bool
    {
        return $user->hasAnyRole(['approver', 'admin', 'superuser']);
    }

    /**
     * Tentukan sama ada pengguna boleh mengeksport draf
     *
     * Akses: admin, superuser sahaja
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'superuser']);
    }
}
