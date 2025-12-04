{{--
/**
 * Component: ARIA Live Region
 * Description: WCAG 2.2 AA compliant live region for screen reader announcements
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages)
 * @version 2.0.0
 * @created 2025-12-04
 *
 * Requirement 9.3: ARIA labels, landmarks, live regions
 */
--}}

@props([
    'politeness' => 'polite', // 'polite', 'assertive', or 'off'
    'atomic' => true,
    'relevant' => 'additions text', // 'additions', 'removals', 'text', 'all'
])

<div x-data="{
    message: '',
    queue: [],
    processing: false,

    announce(msg, priority = 'polite') {
        if (priority === 'assertive') {
            // Assertive messages interrupt immediately
            this.message = msg;
            setTimeout(() => this.message = '', 5000);
        } else {
            // Polite messages are queued
            this.queue.push(msg);
            this.processQueue();
        }
    },

    processQueue() {
        if (this.processing || this.queue.length === 0) return;
        this.processing = true;

        const msg = this.queue.shift();
        this.message = msg;

        setTimeout(() => {
            this.message = '';
            this.processing = false;
            this.processQueue();
        }, 3000);
    }
}" x-on:notify.window="announce($event.detail.message, $event.detail.priority || 'polite')"
    x-on:announce.window="announce($event.detail.message, $event.detail.priority || 'polite')" class="sr-only"
    role="status" aria-live="{{ $politeness }}" aria-atomic="{{ $atomic ? 'true' : 'false' }}"
    aria-relevant="{{ $relevant }}">
    <span x-text="message"></span>
</div>

{{-- Assertive region for urgent announcements --}}
<div x-data="{ urgentMessage: '' }"
    x-on:urgent-announce.window="urgentMessage = $event.detail.message; setTimeout(() => urgentMessage = '', 5000)"
    class="sr-only" role="alert" aria-live="assertive" aria-atomic="true">
    <span x-text="urgentMessage"></span>
</div>
