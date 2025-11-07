<?php

declare(strict_types=1);

// name: UserNotificationPreferenceSeeder
// description: Seeds default notification preferences for all users
// author: dev-team@motac.gov.my
// trace: SRS-FR-004; D04 §4.4; Requirements 3.2
// last-updated: 2025-11-06

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserNotificationPreference;
use Illuminate\Database\Seeder;

class UserNotificationPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preferences = [
            'ticket_status_updates',
            'loan_approval_notifications',
            'overdue_reminders',
            'system_announcements',
            'ticket_assignments',
            'comment_replies',
        ];

        User::chunk(100, function ($users) use ($preferences) {
            foreach ($users as $user) {
                foreach ($preferences as $preference) {
                    UserNotificationPreference::firstOrCreate([
                        'user_id' => $user->id,
                        'preference_key' => $preference,
                    ], [
                        'preference_value' => true,
                    ]);
                }
            }
        });

        $this->command->info('Default notification preferences seeded for all users.');
    }
}
