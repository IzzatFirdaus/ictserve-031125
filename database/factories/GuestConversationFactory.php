<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GuestConversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model GuestConversation
 *
 * Menyokong True Hybrid Architecture dengan Account Linking
 * Menjana sejarah perbualan tetamu yang realistik
  *
 * @extends Factory<\App\Models\GuestConversation>
 */
class GuestConversationFactory extends Factory
{
    /**
     * Model yang berkaitan dengan factory ini
     */
    protected $model = GuestConversation::class;

    /**
     * Takrifkan keadaan lalai model
     */
    public function definition(): array
    {
        $hasEmail = $this->faker->boolean(60); // 60% ada e-mel
        $isClaimed = $this->faker->boolean(30); // 30% sudah dituntut

        return [
            'session_id' => 'guest_' . Str::random(32),
            'email' => $hasEmail ? $this->faker->safeEmail : null,
            'conversation_history' => $this->generateConversationHistory(),
            'claimed_by_user_id' => $isClaimed ? User::factory() : null,
            'claimed_at' => $isClaimed ? $this->faker->dateTimeBetween('-7 days', 'now') : null,
            'expires_at' => $this->faker->dateTimeBetween('now', '+30 minutes'),
        ];
    }

    /**
     * State untuk perbualan yang belum dituntut
     */
    public function unclaimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'claimed_by_user_id' => null,
            'claimed_at' => null,
        ]);
    }

    /**
     * State untuk perbualan yang sudah dituntut
     */
    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'claimed_by_user_id' => User::factory(),
            'claimed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * State untuk perbualan yang masih aktif
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('now', '+30 minutes'),
        ]);
    }

    /**
     * State untuk perbualan yang sudah tamat tempoh
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-2 hours', '-1 minute'),
        ]);
    }

    /**
     * State untuk perbualan dengan e-mel
     */
    public function withEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->safeEmail,
        ]);
    }

    /**
     * State untuk perbualan tanpa e-mel
     */
    public function withoutEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }

    /**
     * State untuk perbualan pendek (1-3 mesej)
     */
    public function shortConversation(): static
    {
        return $this->state(fn (array $attributes) => [
            'conversation_history' => $this->generateConversationHistory(1, 3),
        ]);
    }

    /**
     * State untuk perbualan panjang (5-10 mesej)
     */
    public function longConversation(): static
    {
        return $this->state(fn (array $attributes) => [
            'conversation_history' => $this->generateConversationHistory(5, 10),
        ]);
    }

    /**
     * Menjana sejarah perbualan yang realistik
     */
    private function generateConversationHistory(int $minMessages = 2, int $maxMessages = 8): array
    {
        $messageCount = $this->faker->numberBetween($minMessages, $maxMessages);
        $history = [];

        // Mesej pertama sentiasa dari pengguna
        $history[] = [
            'role' => 'user',
            'content' => $this->generateUserMessage(),
            'timestamp' => $this->faker->dateTimeBetween('-2 hours', '-1 hour')->format('c'),
        ];

        // Mesej kedua sentiasa dari assistant
        if ($messageCount > 1) {
            $history[] = [
                'role' => 'assistant',
                'content' => $this->generateAssistantMessage(),
                'timestamp' => $this->faker->dateTimeBetween('-1 hour', '-30 minutes')->format('c'),
            ];
        }

        // Mesej tambahan berselang-seli
        for ($i = 2; $i < $messageCount; $i++) {
            $role = $i % 2 === 0 ? 'user' : 'assistant';
            $content = $role === 'user'
                ? $this->generateFollowUpUserMessage()
                : $this->generateFollowUpAssistantMessage();

            $history[] = [
                'role' => $role,
                'content' => $content,
                'timestamp' => $this->faker->dateTimeBetween('-30 minutes', 'now')->format('c'),
            ];
        }

        return $history;
    }

    /**
     * Menjana mesej pengguna awal
     */
    private function generateUserMessage(): string
    {
        return $this->faker->randomElement([
            'Bagaimana cara menggunakan sistem helpdesk?',
            'Saya menghadapi masalah dengan komputer saya',
            'Bolehkah saya meminjam projector untuk mesyuarat?',
            'Sistem e-mel tidak berfungsi dengan baik',
            'Bagaimana cara memohon aset ICT?',
            'Printer di pejabat saya rosak',
            'Saya perlukan bantuan dengan perisian baru',
            'Kata laluan saya tidak boleh log masuk',
        ]);
    }

    /**
     * Menjana respons assistant awal
     */
    private function generateAssistantMessage(): string
    {
        return $this->faker->randomElement([
            'Terima kasih atas pertanyaan anda. Saya akan membantu anda dengan masalah ini.',
            'Untuk menggunakan sistem helpdesk, anda boleh mengikuti langkah-langkah berikut...',
            'Saya faham masalah anda. Mari kita selesaikan langkah demi langkah.',
            'Untuk permohonan pinjaman aset, anda perlu mengisi borang yang tersedia.',
            'Masalah yang anda hadapi adalah biasa. Berikut adalah penyelesaiannya...',
            'Sila cuba langkah-langkah berikut untuk menyelesaikan masalah anda.',
        ]);
    }

    /**
     * Menjana mesej susulan pengguna
     */
    private function generateFollowUpUserMessage(): string
    {
        return $this->faker->randomElement([
            'Terima kasih! Bolehkah anda terangkan lebih lanjut?',
            'Saya sudah cuba tetapi masih tidak berjaya',
            'Adakah cara lain untuk menyelesaikan ini?',
            'Berapa lama proses ini akan mengambil masa?',
            'Saya perlukan bantuan tambahan',
            'Bolehkah saya bercakap dengan teknisi?',
            'Apakah dokumen yang diperlukan?',
        ]);
    }

    /**
     * Menjana respons susulan assistant
     */
    private function generateFollowUpAssistantMessage(): string
    {
        return $this->faker->randomElement([
            'Sudah tentu! Saya akan terangkan dengan lebih terperinci.',
            'Jika langkah tersebut tidak berhasil, sila cuba alternatif ini.',
            'Proses ini biasanya mengambil masa 1-2 hari bekerja.',
            'Saya akan hubungkan anda dengan teknisi kami.',
            'Dokumen yang diperlukan adalah salinan IC dan borang permohonan.',
            'Adakah anda memerlukan bantuan lain?',
            'Sila maklumkan jika masalah berterusan.',
        ]);
    }
}
