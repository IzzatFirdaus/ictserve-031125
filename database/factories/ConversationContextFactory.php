<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ConversationContext;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\ConversationContext>
 */
class ConversationContextFactory extends Factory
{
    protected $model = ConversationContext::class;

    public function definition(): array
    {
        $isAuthenticated = $this->faker->boolean(70);

        return [
            'user_id' => $isAuthenticated ? User::factory() : null,
            'session_id' => $isAuthenticated ? null : (string) Str::uuid(),
            'context_data' => [
                'recent_topics' => $this->faker->words(3),
            ],
            'personalization_data' => [
                'preferred_tone' => 'formal',
            ],
            'last_interaction' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'expires_at' => $this->faker->dateTimeBetween('now', '+30 days'),
        ];
    }
}
