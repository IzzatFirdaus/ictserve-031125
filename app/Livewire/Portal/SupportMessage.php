<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Support Message Component
 *
 * In-app messaging system for contacting support with attachment support.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.4: In-app messaging with attachment support
 * - WCAG 2.2 AA: Form validation, error messages, keyboard navigation
 * - D12 §4: Unified component library integration
 */
class SupportMessage extends Component
{
    use WithFileUploads;

    /**
     * Message subject
     */
    public string $subject = '';

    /**
     * Message description
     */
    public string $description = '';

    /**
     * Message priority
     */
    public string $priority = 'normal';

    /**
     * Uploaded attachments
     */
    public array $attachments = [];

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // 10MB max
        ];
    }

    /**
     * Validation messages
     */
    protected function messages(): array
    {
        return [
            'subject.required' => __('validation.required', ['attribute' => __('portal.subject')]),
            'subject.min' => __('validation.min.string', ['attribute' => __('portal.subject'), 'min' => 5]),
            'subject.max' => __('validation.max.string', ['attribute' => __('portal.subject'), 'max' => 200]),
            'description.required' => __('validation.required', ['attribute' => __('portal.description')]),
            'description.min' => __('validation.min.string', ['attribute' => __('portal.description'), 'min' => 20]),
            'description.max' => __('validation.max.string', ['attribute' => __('portal.description'), 'max' => 2000]),
            'attachments.*.max' => __('validation.max.file', ['attribute' => __('portal.attachment'), 'max' => 10240]),
        ];
    }

    /**
     * Submit support message
     */
    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();

        // Create support ticket
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'open',
        ]);

        // Handle attachments
        if (! empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('support-attachments', 'private');

                $ticket->attachments()->create([
                    'filename' => $attachment->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $attachment->getMimeType(),
                    'size' => $attachment->getSize(),
                ]);
            }
        }

        // Send notification to support team
        // TODO: Implement notification

        // Reset form
        $this->reset(['subject', 'description', 'priority', 'attachments']);

        session()->flash('success', __('portal.support.message_sent'));

        $this->dispatch('support-message-sent', ticketId: $ticket->id);
    }

    /**
     * Remove attachment
     */
    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    /**
     * Get character count for description
     */
    public function getDescriptionCharacterCount(): int
    {
        return strlen($this->description);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.portal.support-message', [
            'characterCount' => $this->getDescriptionCharacterCount(),
        ]);
    }
}
