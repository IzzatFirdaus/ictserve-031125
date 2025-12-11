<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AutoReplyDraft;
use App\Models\AutoReplyTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model AutoReplyDraft
 *
 * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply draft management dengan approval workflow
 * Selaras dengan D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutoReplyDraft>
 */
class AutoReplyDraftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $draftContents = [
            'Terima kasih kerana menghubungi sokongan ICT MOTAC. Tiket anda telah diterima dan sedang diproses oleh pasukan teknikal kami. Kami akan menghubungi anda dalam tempoh 24 jam untuk kemas kini lanjut.',
            'Permohonan pinjaman aset anda telah diluluskan. Sila ambil aset tersebut di Pejabat ICT pada waktu pejabat. Pastikan anda membawa kad pengenalan untuk pengesahan.',
            'Maaf, permohonan pinjaman aset tidak dapat diluluskan kerana aset sedang digunakan. Sila cuba lagi pada tarikh lain atau hubungi pentadbir untuk alternatif.',
            'Status tiket anda telah dikemaskini. Masalah yang dilaporkan sedang dalam proses penyelesaian. Anggaran masa siap adalah 2-3 hari bekerja.',
            'Aset yang anda pinjam perlu dipulangkan esok. Sila pastikan aset dalam keadaan baik dan lengkap dengan semua aksesori.',
        ];

        // Polymorphic relationship - for now we'll use a placeholder
        // In real implementation, this would reference actual HelpdeskTicket or LoanApplication
        $replyableTypes = [
            'App\\Models\\HelpdeskTicket',
            'App\\Models\\LoanApplication',
        ];

        return [
            'replyable_type' => $this->faker->randomElement($replyableTypes),
            'replyable_id' => $this->faker->numberBetween(1, 100),
            'draft_content' => $this->faker->randomElement($draftContents),
            'template_id' => $this->faker->boolean(70) ? AutoReplyTemplate::factory() : null,
            'status' => $this->faker->randomElement([
                AutoReplyDraft::STATUS_DRAFT,
                AutoReplyDraft::STATUS_PENDING_REVIEW,
                AutoReplyDraft::STATUS_APPROVED,
                AutoReplyDraft::STATUS_REJECTED,
                AutoReplyDraft::STATUS_SENT,
            ]),
            'generated_by' => User::factory(),
            'approved_by' => $this->faker->boolean(50) ? User::factory() : null,
            'approved_at' => $this->faker->boolean(50) ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'rejection_reason' => $this->faker->boolean(20) ? 'Kandungan tidak sesuai atau memerlukan penambahbaikan' : null,
        ];
    }

    /**
     * Draft yang menunggu semakan
     *
     * @return static
     */
    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Draft yang diluluskan
     *
     * @return static
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutoReplyDraft::STATUS_APPROVED,
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Draft yang ditolak
     *
     * @return static
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutoReplyDraft::STATUS_REJECTED,
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'rejection_reason' => $this->faker->randomElement([
                'Kandungan tidak mencukupi',
                'Maklumat tidak tepat',
                'Memerlukan penambahbaikan',
                'Tidak mematuhi garis panduan',
            ]),
        ]);
    }

    /**
     * Draft untuk tiket helpdesk
     *
     * @return static
     */
    public function forHelpdesk(): static
    {
        return $this->state(fn (array $attributes) => [
            'replyable_type' => 'App\\Models\\HelpdeskTicket',
            'draft_content' => 'Terima kasih kerana melaporkan masalah teknikal. Tiket anda telah diterima dan akan diproses dalam tempoh 24 jam.',
        ]);
    }

    /**
     * Draft untuk pinjaman aset
     *
     * @return static
     */
    public function forAssetLoan(): static
    {
        return $this->state(fn (array $attributes) => [
            'replyable_type' => 'App\\Models\\LoanApplication',
            'draft_content' => 'Permohonan pinjaman aset anda sedang diproses. Kami akan maklumkan keputusan dalam masa 2-3 hari bekerja.',
        ]);
    }
}
