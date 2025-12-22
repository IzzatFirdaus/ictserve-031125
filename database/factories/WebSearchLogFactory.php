<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\WebSearchLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\WebSearchLog>
 */
class WebSearchLogFactory extends Factory
{
    protected $model = WebSearchLog::class;

    public function definition(): array
    {
        $resultsCount = $this->faker->numberBetween(0, 5);

        return [
            'request_id' => (string) Str::uuid(),
            'search_query' => $this->faker->sentence(6),
            'provider' => $this->faker->randomElement(['duckduckgo', 'bing', 'google']),
            'results_count' => $resultsCount,
            'sources_used' => $resultsCount > 0 ? $this->faker->randomElements([
                'https://www.motac.gov.my',
                'https://www.malaysia.gov.my',
                'https://www.mampu.gov.my',
            ], $this->faker->numberBetween(1, min(3, $resultsCount))) : [],
            'cost' => null,
            'user_id' => $this->faker->boolean(70) ? User::factory() : null,
        ];
    }
}
