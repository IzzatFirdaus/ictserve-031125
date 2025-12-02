<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendEmailDigests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-digests {--frequency= : Filter by frequency (daily, weekly, monthly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email digests to users based on their preferences';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $frequency = $this->option('frequency');

        // Get users with email digest enabled
        $query = \App\Models\UserNotificationPreference::query()
            ->where('email_digest_enabled', true)
            ->with('user');

        if ($frequency) {
            $query->where('email_digest_frequency', $frequency);
        }

        $preferences = $query->get();

        $this->info("Found {$preferences->count()} users with email digest enabled.");

        foreach ($preferences as $preference) {
            $user = $preference->user;

            if (! $user) {
                continue;
            }

            // Check if it's time to send the digest
            if (! $this->shouldSendDigest($preference)) {
                continue;
            }

            // Get unread notifications
            $notifications = $user->notifications()
                ->whereNull('read_at')
                ->where('created_at', '>=', $this->getDigestSince($preference))
                ->get();

            if ($notifications->isEmpty()) {
                continue;
            }

            // Send email digest
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\NotificationDigest($user, $notifications)
            );

            $this->info("Sent digest to {$user->email}");
        }

        $this->info('Email digests sent successfully.');

        return self::SUCCESS;
    }

    private function shouldSendDigest(\App\Models\UserNotificationPreference $preference): bool
    {
        // Check if current time matches the digest time (within 1 hour window)
        $currentHour = now()->format('H');
        $digestHour = $preference->email_digest_time->format('H');

        return abs((int) $currentHour - (int) $digestHour) <= 1;
    }

    private function getDigestSince(\App\Models\UserNotificationPreference $preference): \Carbon\Carbon
    {
        return match ($preference->email_digest_frequency) {
            'daily' => now()->subDay(),
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonth(),
            default => now()->subDay(),
        };
    }
}
