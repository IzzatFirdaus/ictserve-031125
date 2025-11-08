<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'code' => 'G' . $this->faker->unique()->numberBetween(1, 99),
            'name' => $this->faker->jobTitle(),
            'level' => $this->faker->numberBetween(1, 54),
        ];
    }
}
