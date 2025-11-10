<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Audit>
 */
class AuditFactory extends Factory
{
    protected $model = Audit::class;

    public function definition(): array
    {
        return [
            'user_type' => 'App\\Models\\User',
            'user_id' => $this->faker->randomNumber(),
            'event' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'auditable_type' => 'App\\Models\\LoanApplication',
            'auditable_id' => $this->faker->randomNumber(),
            'old_values' => [],
            'new_values' => [],
            'url' => $this->faker->url(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => 'PHPUnit/Static',
            'tags' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
