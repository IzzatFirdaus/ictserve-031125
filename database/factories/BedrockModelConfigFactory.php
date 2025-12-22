<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BedrockModelConfig;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BedrockModelConfig>
 */
class BedrockModelConfigFactory extends Factory
{
    protected $model = BedrockModelConfig::class;

    public function definition(): array
    {
        $modelKey = $this->faker->randomElement(['opus', 'sonnet', 'haiku']);

        $modelId = match ($modelKey) {
            'opus' => 'global.anthropic.claude-opus-4-5-20251101-v1:0',
            'sonnet' => 'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
            default => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        };

        return [
            'model_id' => $modelId,
            'model_name' => strtoupper($modelKey).' 4.5',
            'provider' => 'bedrock',
            'task_types' => [$this->faker->randomElement(['faq_query', 'document_analysis', 'auto_reply_generation'])],
            'cost_per_token' => $this->faker->randomFloat(8, 0.00000010, 0.00000500),
            'max_tokens' => $this->faker->numberBetween(1024, 200000),
            'enabled' => $this->faker->boolean(90),
            'configuration' => [
                'notes' => 'Konfigurasi ujian',
            ],
            'created_by' => $this->faker->boolean(70) ? User::factory() : null,
        ];
    }
}
