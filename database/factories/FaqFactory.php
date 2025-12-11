<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model FAQ
 *
 * Per Requirements 1.1, 1.5: FAQ management dengan True Hybrid Architecture
 * Selaras dengan D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questions = [
            'Bagaimana cara reset kata laluan sistem?',
            'Apakah langkah untuk memohon akaun pengguna baru?',
            'Bagaimana cara mengemaskini maklumat profil?',
            'Apakah prosedur untuk melaporkan masalah teknikal?',
            'Bagaimana cara mengakses sistem dari rumah?',
            'Apakah keperluan minimum untuk menggunakan sistem?',
            'Bagaimana cara memuat turun laporan dari sistem?',
            'Apakah langkah keselamatan yang perlu diambil?',
            'Bagaimana cara menghubungi sokongan teknikal?',
            'Apakah waktu operasi sistem?',
        ];

        $answers = [
            'Untuk reset kata laluan, sila hubungi pentadbir sistem atau gunakan fungsi "Lupa Kata Laluan" pada halaman log masuk.',
            'Permohonan akaun baru boleh dibuat melalui borang pendaftaran atau hubungi pentadbir sistem untuk bantuan.',
            'Maklumat profil boleh dikemaskini melalui menu "Profil Saya" selepas log masuk ke sistem.',
            'Masalah teknikal boleh dilaporkan melalui sistem tiket helpdesk atau hubungi pasukan sokongan.',
            'Akses dari rumah memerlukan sambungan VPN yang sah. Sila hubungi IT untuk konfigurasi.',
            'Sistem memerlukan pelayar web terkini dan sambungan internet yang stabil.',
            'Laporan boleh dimuat turun melalui menu "Laporan" dengan memilih format yang dikehendaki.',
            'Pastikan kata laluan yang kuat, log keluar selepas guna, dan jangan kongsi maklumat log masuk.',
            'Sokongan teknikal boleh dihubungi melalui telefon, e-mel, atau sistem tiket helpdesk.',
            'Sistem beroperasi 24/7 dengan penyelenggaraan terjadual pada hujung minggu.',
        ];

        $tags = [
            ['kata-laluan', 'keselamatan'],
            ['akaun', 'pendaftaran'],
            ['profil', 'kemaskini'],
            ['masalah', 'teknikal', 'helpdesk'],
            ['akses', 'rumah', 'vpn'],
            ['keperluan', 'sistem'],
            ['laporan', 'muat-turun'],
            ['keselamatan', 'dasar'],
            ['sokongan', 'hubungan'],
            ['operasi', 'masa'],
        ];

        $questionIndex = $this->faker->numberBetween(0, count($questions) - 1);

        return [
            'question' => $questions[$questionIndex],
            'answer' => $answers[$questionIndex],
            'tags' => $tags[$questionIndex],
            'match_score' => $this->faker->randomFloat(2, 0.3, 1.0),
            // True Hybrid Architecture: nullable created_by untuk guest/authenticated
            'created_by' => $this->faker->boolean(70) ? User::factory() : null,
        ];
    }

    /**
     * FAQ dengan skor persamaan tinggi
     *
     * @return static
     */
    public function highScore(): static
    {
        return $this->state(fn (array $attributes) => [
            'match_score' => $this->faker->randomFloat(2, 0.8, 1.0),
        ]);
    }

    /**
     * FAQ tanpa pencipta (guest submission)
     *
     * @return static
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => null,
        ]);
    }

    /**
     * FAQ dengan tag tertentu
     *
     * @param array $tags
     * @return static
     */
    public function withTags(array $tags): static
    {
        return $this->state(fn (array $attributes) => [
            'tags' => $tags,
        ]);
    }
}
