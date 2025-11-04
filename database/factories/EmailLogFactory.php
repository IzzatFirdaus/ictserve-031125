<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Mail\TicketCreatedConfirmation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EmailLog>
 */
class EmailLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_email' => fake()->unique()->safeEmail(),
            'recipient_name' => fake()->name(),
            'subject' => fake()->sentence(),
            'mailable_class' => TicketCreatedConfirmation::class,
            'status' => 'queued',
            'message_id' => fake()->uuid(),
            'status_message' => null,
            'meta' => ['locale' => fake()->randomElement(['ms', 'en'])],
            'queued_at' => now(),
            'sent_at' => null,
            'failed_at' => null,
        ];
    }
}
