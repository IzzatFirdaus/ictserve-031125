<?php

declare(strict_types=1);

namespace App\Traits;

use Livewire\Attributes\Validate;

/**
 * Honeypot Protection Trait
 *
 * Provides bot detection using honeypot fields (fields that should remain empty).
 * Real users won't see or fill these fields, but bots will.
 *
 * Usage:
 * 1. Add this trait to your Livewire component
 * 2. Include the honeypot field in your form (hidden with CSS)
 * 3. Call validateHoneypot() before processing the form
 *
 * @package App\Traits
 */
trait HasHoneypot
{
    /**
     * Honeypot field that should remain empty
     */
    #[Validate('max:0')]
    public string $website = '';

    /**
     * Validate that the honeypot field is empty
     *
     * @return bool True if validation passes (honeypot is empty), false otherwise
     */
    public function validateHoneypot(): bool
    {
        // If the honeypot field has been filled, it's likely a bot
        if (! empty($this->website)) {
            // Log the potential bot attempt
            logger()->warning('Honeypot triggered', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'honeypot_value' => $this->website,
            ]);

            // Add a generic validation error to appear normal
            $this->addError('form', __('Please try again later.'));

            return false;
        }

        return true;
    }

    /**
     * Render honeypot field HTML
     *
     * @return string HTML for honeypot field
     */
    public function renderHoneypot(): string
    {
        return '<div class="sr-only" aria-hidden="true" tabindex="-1">
            <label for="website">Website</label>
            <input 
                type="text" 
                id="website" 
                name="website" 
                wire:model="website"
                tabindex="-1"
                autocomplete="off"
            />
        </div>';
    }
}
