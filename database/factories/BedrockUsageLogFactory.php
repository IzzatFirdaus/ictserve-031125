<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BedrockUsageLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\BedrockUsageLog>
 */
class BedrockUsageLogFactory extends Factory
{
    protected $model = BedrockUsageLog::class;

    public function definition(): array
    {
        $inputTokens = $this->faker->numberBetween(50, 2000);
        $outputTokens = $this->faker->numberBetween(50, 2000);
        $costPerToken = $this->faker->randomFloat(8, 0.00000010, 0.00000500);

        return [
            'request_id' => (string) Str::uuid(),
            'model_id' => $this->faker->randomElement([
                'global.anthropic.claude-opus-4-5-20251101-v1:0',
                'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
                'us.anthropic.claude-haiku-4-5-20251001-v1:0',
            ]),
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'cost_estimate' => ($inputTokens + $outputTokens) * $costPerToken,
            'response_time_ms' => $this->faker->numberBetween(200, 10000),
            'success' => $this->faker->boolean(95),
            'error_message' => null,
            'user_id' => $this->faker->boolean(70) ? User::factory() : null,
        ];
    }
}
