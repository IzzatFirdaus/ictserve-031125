<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AutoReplyTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model AutoReplyTemplate
 *
 * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply template management
 * Selaras dengan D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutoReplyTemplate>
 */
class AutoReplyTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $templates = [
            [
                'name' => 'Penyelesaian Tiket Helpdesk',
                'content' => 'Terima kasih kerana menghubungi sokongan ICT. Tiket anda #{{ticket_number}} telah diselesaikan. Masalah {{issue_type}} telah diperbaiki pada {{resolution_date}}. Jika anda menghadapi sebarang masalah lanjut, sila hubungi kami.',
                'variables' => ['ticket_number', 'issue_type', 'resolution_date'],
            ],
            [
                'name' => 'Kelulusan Pinjaman Aset',
                'content' => 'Permohonan pinjaman aset anda untuk {{asset_name}} telah diluluskan. Sila ambil aset tersebut di {{pickup_location}} pada {{pickup_date}}. Tempoh pinjaman adalah {{loan_duration}} hari.',
                'variables' => ['asset_name', 'pickup_location', 'pickup_date', 'loan_duration'],
            ],
            [
                'name' => 'Penolakan Pinjaman Aset',
                'content' => 'Maaf, permohonan pinjaman aset anda untuk {{asset_name}} tidak dapat diluluskan kerana {{rejection_reason}}. Sila hubungi pentadbir untuk maklumat lanjut.',
                'variables' => ['asset_name', 'rejection_reason'],
            ],
            [
                'name' => 'Kemas Kini Status Tiket',
                'content' => 'Status tiket anda #{{ticket_number}} telah dikemaskini kepada {{new_status}}. {{additional_notes}} Anggaran masa penyelesaian: {{estimated_completion}}.',
                'variables' => ['ticket_number', 'new_status', 'additional_notes', 'estimated_completion'],
            ],
            [
                'name' => 'Peringatan Pemulangan Aset',
                'content' => 'Peringatan: Aset {{asset_name}} yang anda pinjam perlu dipulangkan pada {{return_date}}. Sila pastikan aset dalam keadaan baik dan lengkap.',
                'variables' => ['asset_name', 'return_date'],
            ],
        ];

        $template = $this->faker->randomElement($templates);

        return [
            'name' => $template['name'],
            'template_content' => $template['content'],
            'variables' => $template['variables'],
            'status' => $this->faker->randomElement([
                AutoReplyTemplate::STATUS_DRAFT,
                AutoReplyTemplate::STATUS_ACTIVE,
                AutoReplyTemplate::STATUS_ARCHIVED,
            ]),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Template aktif
     *
     * @return static
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutoReplyTemplate::STATUS_ACTIVE,
        ]);
    }

    /**
     * Template draft
     *
     * @return static
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutoReplyTemplate::STATUS_DRAFT,
        ]);
    }

    /**
     * Template untuk tiket helpdesk
     *
     * @return static
     */
    public function forHelpdesk(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Template Tiket Helpdesk',
            'template_content' => 'Tiket anda #{{ticket_number}} telah {{action}}. {{details}}',
            'variables' => ['ticket_number', 'action', 'details'],
        ]);
    }

    /**
     * Template untuk pinjaman aset
     *
     * @return static
     */
    public function forAssetLoan(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Template Pinjaman Aset',
            'template_content' => 'Permohonan pinjaman {{asset_name}} anda telah {{status}}. {{message}}',
            'variables' => ['asset_name', 'status', 'message'],
        ]);
    }
}
