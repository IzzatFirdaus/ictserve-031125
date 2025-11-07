{{--
/**
 * Support Message Component View
 *
 * In-app messaging form for contacting support.
 * WCAG 2.2 AA compliant with form validation and error handling.
 *
 * @package Resources\Views\Livewire\Portal
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.4: In-app messaging system
 * - WCAG 2.2 AA: Form accessibility, error messages, keyboard navigation
 * - D12 §4: Unified component library integration
 */
--}}

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-900">
            {{ __('portal.help.contact_support') }}
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('portal.help.contact_support_description') }}
        </p>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-success-50 p-4" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-success-600" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-success-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Support Form --}}
    <form wire:submit="submit" class="space-y-6">
        {{-- Subject --}}
        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700">
                {{ __('portal.subject') }}
                <span class="text-danger-600">*</span>
            </label>
            <input type="text" id="subject" wire:model.live.debounce.300ms="subject"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('subject') border-danger-300 @enderror"
                placeholder="{{ __('portal.support.subject_placeholder') ?? 'Brief description of your issue' }}"
                aria-required="true" aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('subject') ? 'subject-error' : '' }}" />
            @error('subject')
                <p class="mt-2 text-sm text-danger-600" id="subject-error" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Priority --}}
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700">
                {{ __('portal.priority') }}
                <span class="text-danger-600">*</span>
            </label>
            <select id="priority" wire:model="priority"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                aria-required="true">
                <option value="low">{{ __('portal.priority_low') }}</option>
                <option value="normal">{{ __('portal.priority_normal') }}</option>
                <option value="high">{{ __('portal.priority_high') }}</option>
                <option value="urgent">{{ __('portal.priority_urgent') }}</option>
            </select>
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">
                {{ __('portal.description') }}
                <span class="text-danger-600">*</span>
            </label>
            <textarea id="description" wire:model.live.debounce.300ms="description" rows="6"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('description') border-danger-300 @enderror"
                placeholder="{{ __('portal.support.description_placeholder') ?? 'Provide detailed information about your issue' }}"
                aria-required="true" aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                aria-describedby="description-help {{ $errors->has('description') ? 'description-error' : '' }}"></textarea>
            <div class="mt-2 flex items-center justify-between">
                <p class="text-xs text-gray-500" id="description-help">
                    {{ __('portal.support.description_help') ?? 'Minimum 20 characters' }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ $characterCount }} / 2000 {{ __('portal.characters') }}
                </p>
            </div>
            @error('description')
                <p class="mt-2 text-sm text-danger-600" id="description-error" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Attachments --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('portal.attachments') }}
                <span class="text-gray-500">({{ __('portal.optional') ?? 'Optional' }})</span>
            </label>
            <div class="mt-1">
                <input type="file" wire:model="attachments" multiple
                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
            </div>
            <p class="mt-2 text-xs text-gray-500">
                {{ __('portal.support.attachment_help') ?? 'PDF, DOC, DOCX, JPG, PNG (max 10MB each)' }}
            </p>

            {{-- Attachment List --}}
            @if (!empty($attachments))
                <div class="mt-4 space-y-2">
                    @foreach ($attachments as $index => $attachment)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 bg-gray-50 p-3">
                            <div class="flex items-center">
                                <x-heroicon-o-paper-clip class="h-5 w-5 text-gray-400" />
                                <span class="ml-2 text-sm text-gray-700">
                                    {{ $attachment->getClientOriginalName() }}
                                </span>
                            </div>
                            <button type="button" wire:click="removeAttachment({{ $index }})"
                                class="text-danger-600 hover:text-danger-700 focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 rounded"
                                aria-label="{{ __('portal.remove_attachment', ['filename' => $attachment->getClientOriginalName()]) }}">
                                <x-heroicon-o-x-mark class="h-5 w-5" />
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            @error('attachments.*')
                <p class="mt-2 text-sm text-danger-600" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('contact') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                {{ __('portal.cancel') }}
            </a>
            <button type="submit"
                class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled">
                <span wire:loading.remove>
                    {{ __('portal.support.send_message') ?? 'Send message' }}
                </span>
                <span wire:loading>
                    {{ __('portal.sending') ?? 'Sending...' }}
                </span>
            </button>
        </div>
    </form>
</div>
