<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\InternalComment;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Internal Comment Factory
 *
 * Factory for generating test data for internal staff-only comments
 * with threading support and @mentions.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternalComment>
 */
class InternalCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = InternalComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Randomly choose between HelpdeskTicket and LoanApplication
        $commentableType = fake()->randomElement([
            HelpdeskTicket::class,
            LoanApplication::class,
        ]);

        // Get a random commentable instance
        $commentable = $commentableType::inRandomOrder()->first();

        // If no commentable exists, create one
        if (! $commentable) {
            $commentable = $commentableType === HelpdeskTicket::class
                ? HelpdeskTicket::factory()->create()
                : LoanApplication::factory()->create();
        }

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'commentable_type' => $commentableType,
            'commentable_id' => $commentable->id,
            'parent_id' => null, // Top-level comment by default
            'comment' => fake()->paragraph(3),
            'mentions' => null, // No mentions by default
        ];
    }

    /**
     * Create a reply to an existing comment
     */
    public function reply(InternalComment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'commentable_type' => $parent->commentable_type,
            'commentable_id' => $parent->commentable_id,
        ]);
    }

    /**
     * Create a comment with mentions
     */
    public function withMentions(array $userIds = []): static
    {
        if (empty($userIds)) {
            $userIds = User::inRandomOrder()->limit(fake()->numberBetween(1, 3))->pluck('id')->toArray();
        }

        return $this->state(fn (array $attributes) => [
            'mentions' => $userIds,
            'comment' => $this->generateCommentWithMentions($userIds),
        ]);
    }

    /**
     * Create a comment on a specific ticket
     */
    public function forTicket(HelpdeskTicket $ticket): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $ticket->id,
        ]);
    }

    /**
     * Create a comment on a specific loan application
     */
    public function forLoan(LoanApplication $loan): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_type' => LoanApplication::class,
            'commentable_id' => $loan->id,
        ]);
    }

    /**
     * Generate comment text with @mentions
     */
    private function generateCommentWithMentions(array $userIds): string
    {
        $users = User::whereIn('id', $userIds)->get();
        $comment = fake()->paragraph(2);

        foreach ($users as $user) {
            $comment .= ' @'.$user->name;
        }

        return $comment.' '.fake()->sentence();
    }
}
