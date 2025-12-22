<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ModelPerformanceMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ModelPerformanceMetric>
 */
class ModelPerformanceMetricFactory extends Factory
{
    protected $model = ModelPerformanceMetric::class;

    public function definition(): array
    {
        return [
            'model_id' => $this->faker->randomElement([
                'ollama:llama3.1',
                'bedrock:haiku',
                'bedrock:sonnet',
                'bedrock:opus',
            ]),
            'metric_type' => $this->faker->randomElement(['p50_ms', 'p95_ms', 'success_rate', 'cost_usd']),
            'metric_value' => $this->faker->randomFloat(6, 0, 10000),
            'measurement_time' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'metadata' => [
                'source' => 'ujian',
            ],
        ];
    }
}
