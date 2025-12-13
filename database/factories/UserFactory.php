<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'staff', // Default role for new users
            'locale' => 'ms', // v3.5.0 True Hybrid - default to Bahasa Melayu
            'is_active' => true,
            'guest_submissions_linked' => 0, // v3.5.0 True Hybrid
        ];
    }

    /**
     * Indicate that the user has the staff role.
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'staff',
        ])->afterCreating(function ($user) {
            $user->assignRole('staff');
        });
    }

    /**
     * Indicate that the user has the approver role (Grade 41+).
     */
    public function approver(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'approver',
        ]);
    }

    /**
     * Indicate that the user has the admin role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ])->afterCreating(function ($user) {
            $user->assignRole('admin');
        });
    }

    /**
     * Indicate that the user has the superuser role.
     */
    public function superuser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superuser',
        ])->afterCreating(function ($user) {
            $user->assignRole('superuser');
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user has Google SSO linked.
     */
    public function withGoogleSso(): static
    {
        return $this->state(fn (array $attributes) => [
            'google_id' => fake()->numerify('##########'),
            'avatar' => fake()->imageUrl(200, 200, 'people'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the user has a @motac.gov.my email.
     */
    public function motacEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->userName().'@motac.gov.my',
        ]);
    }
}
