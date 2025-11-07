<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\Users\UserWelcomeMail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Component name: Create User Page
 * Description: Filament resource page for creating new users with welcome email
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-004.3 (User Management - Welcome Email)
 * @trace D04 §3.3 (User Creation Workflow)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 *
 * @updated 2025-11-07
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Mutate form data before creating the user.
     * Generates temporary password with complexity requirements.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate temporary password with complexity requirements
        // 12 characters: uppercase, lowercase, numbers, symbols
        $temporaryPassword = $this->generateSecurePassword();

        // Hash password for storage
        $data['password'] = Hash::make($temporaryPassword);

        // Store temporary password temporarily for welcome email
        // This will be passed to the created user model
        $data['_temporary_password'] = $temporaryPassword;

        // Set flag to require password change on first login
        $data['password_changed_at'] = null;
        $data['require_password_change'] = true;

        return $data;
    }

    /**
     * Handle actions after user is created.
     * Sends welcome email with temporary password.
     */
    protected function afterCreate(): void
    {
        $user = $this->record;
        $temporaryPassword = $this->data['_temporary_password'] ?? null;

        if ($temporaryPassword) {
            // Send welcome email
            $loginUrl = route('filament.admin.auth.login');

            Mail::to($user->email)->queue(
                new UserWelcomeMail($user, $temporaryPassword, $loginUrl)
            );

            // Show success notification
            Notification::make()
                ->success()
                ->title(__('User Created Successfully'))
                ->body(__('Welcome email sent to :email with temporary password.', ['email' => $user->email]))
                ->send();
        }
    }

    /**
     * Generate a secure temporary password.
     * Meets complexity requirements: 12+ chars, uppercase, lowercase, numbers, symbols.
     */
    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Exclude I, O for clarity
        $lowercase = 'abcdefghjkmnpqrstuvwxyz'; // Exclude i, l, o for clarity
        $numbers = '23456789'; // Exclude 0, 1 for clarity
        $symbols = '!@#$%^&*';

        // Ensure at least one character from each category
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill remaining characters randomly
        $allChars = $uppercase.$lowercase.$numbers.$symbols;
        for ($i = 0; $i < 5; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Get the success notification title.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('User created and welcome email sent');
    }
}
