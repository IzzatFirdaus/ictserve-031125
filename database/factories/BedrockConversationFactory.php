<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BedrockConversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BedrockConversation>
 */
class BedrockConversationFactory extends Factory
{
    protected $model = BedrockConversation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->faker->sentence(),
                ],
                [
                    'role' => 'assistant',
                    'content' => $this->faker->paragraph(),
                ],
            ],
            'model' => $this->faker->randomElement(['haiku', 'sonnet', 'opus']),
            'total_tokens' => $this->faker->numberBetween(10, 1000),
        ];
    }

    /**
     * Create a conversation for a guest user (no user_id).
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * Create a conversation with specific model.
     */
    public function withModel(string $model): static
    {
        return $this->state(fn (array $attributes) => [
            'model' => $model,
        ]);
    }
}
