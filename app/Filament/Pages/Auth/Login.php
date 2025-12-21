<?php

declare(strict_types=1);

/**
 * Custom Filament Admin Login Page
 *
 * Implements unified authentication design matching ICTServe v3.6.0 patterns:
 * - MOTAC branding with logo and theme switcher
 * - Google SSO integration per D03 SRS-AUTH-005
 * - Flexible login support (email/username) per D03 SRS-AUTH-003
 * - MyDS Design System compliance (v2025.2)
 * - WCAG 2.2 AA accessibility standards
 * - Bahasa Melayu exclusive interface (v3.6.0)
 * - Consistent styling with guest authentication pages
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.1 (Authentication), D03 SRS-AUTH-003 (Flexible Login), D03 SRS-AUTH-005 (Google SSO)
 * @trace D12 §9 (WCAG 2.2 AA), D13 §2.2-2.7 (MyDS), D14 §4 (MOTAC Branding)
 * @trace D15 (Bahasa Melayu Exclusive)
 *
 * @version 3.6.0
 *
 * @created 2025-12-18
 *
 * @updated 2025-12-18 - Added flexible login support and improved accessibility
 */

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    protected static string $layout = 'components.layouts.passthrough';

    /**
     * The MOTAC email domain for username-to-email conversion
     */
    private const MOTAC_EMAIL_DOMAIN = 'motac.gov.my';

    /**
     * Get the email form component with flexible login support (D03 SRS-AUTH-003)
     * Accepts full email (user@motac.gov.my) OR short username (user)
     */
    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label('Emel atau Nama Pengguna')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->placeholder('nama@motac.gov.my atau nama')
            ->helperText('Anda boleh log masuk menggunakan emel penuh atau nama pengguna sahaja');
    }

    /**
     * Get the password form component with ICTServe styling
     */
    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label('Kata Laluan')
            ->password()
            ->required()
            ->autocomplete('current-password');
    }

    /**
     * Get the remember form component with ICTServe styling
     */
    protected function getRememberFormComponent(): Checkbox
    {
        return parent::getRememberFormComponent()
            ->label('Ingat saya');
    }

    /**
     * Authenticate the user with flexible login support
     *
     * Implements D03 SRS-AUTH-003: Accept full email OR short username
     * Normalizes input to email format before authentication
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // Normalize the login identifier to email format (D03 SRS-AUTH-003)
        $email = $this->normalizeLoginIdentifier($data['email']);

        // Update the form data with normalized email for parent authentication
        $data['email'] = $email;
        $this->form->fill($data);

        // Check if user exists and has admin access before attempting authentication
        $user = User::where('email', $email)->first();
        if (! $user || ! $user->hasAnyRole(['admin', 'superuser'])) {
            throw ValidationException::withMessages([
                'data.email' => 'Anda tidak mempunyai kebenaran untuk mengakses panel pentadbir.',
            ]);
        }

        // Call parent authenticate method with normalized data
        return parent::authenticate();
    }

    /**
     * Normalize the login identifier to a full email address
     *
     * Per D03 SRS-AUTH-003:
     * - If input contains '@', treat as full email
     * - If input does not contain '@', append @motac.gov.my domain
     *
     * @param  string  $identifier  The login identifier (email or username)
     * @return string The normalized email address
     */
    protected function normalizeLoginIdentifier(string $identifier): string
    {
        $identifier = Str::lower(trim($identifier));

        // If already contains @, treat as full email
        if (str_contains($identifier, '@')) {
            return $identifier;
        }

        // Otherwise, append MOTAC domain to create full email
        return $identifier.'@'.self::MOTAC_EMAIL_DOMAIN;
    }

    /**
     * Get the heading for the login page
     */
    public function getHeading(): string
    {
        return 'Log Masuk Pentadbir';
    }

    /**
     * Get the subheading for the login page
     */
    public function getSubHeading(): string
    {
        return 'Sila log masuk untuk mengakses papan pemuka pentadbir';
    }
}
