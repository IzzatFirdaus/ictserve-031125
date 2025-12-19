{{-- FAQ Bot Widget - Floating Chat Bot --}}
{{-- Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0 --}}
{{-- Bahasa Melayu sahaja mengikut D15 v3.6.0 --}}
{{-- Updated: Using Heroicons Blade components instead of inline SVGs --}}

<div class="fixed z-50 {{ $position === 'bottom-right' ? 'bottom-4 right-4' : 'bottom-4 left-4' }}"
    x-data="faqBotWidget"
    x-init="initializeWidget({{ $isOpen ? 'true' : 'false' }}, {{ $isMinimized ? 'true' : 'false' }}, '{{ addslashes($announcement) }}')"
    role="region"
    aria-label="{{ __('ollama.widget.aria_label', [], 'ms') }}">
    {{-- Screen Reader Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" x-text="announcement"></div>

    {{-- Widget Toggle Button (Always Visible) --}}
    <button wire:click="toggleWidget" x-ref="toggleButton"
        class="flex items-center justify-center w-14 h-14 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg transition-all duration-200 focus:ring-4 focus:ring-primary-500 focus:ring-offset-2 min-h-11 min-w-11"
        :class="{ 'scale-110': isOpen }" aria-label="{{ __('ollama.widget.toggle_button', [], 'ms') }}"
        aria-expanded="false" x-bind:aria-expanded="isOpen" type="button" wire:loading.attr="disabled"
        wire:loading.class="opacity-75">
        {{-- Chat Icon --}}
        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6 transition-transform duration-300"
            x-bind:class="{ 'rotate-180': isOpen }" aria-hidden="true" />

        {{-- Notification Badge (if new messages) --}}
        @if (!$isOpen && count($messages) > 1)
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-danger-500 rounded-full animate-pulse"
                aria-hidden="true"></span>
        @endif
    </button>

    {{-- Widget Panel --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute bottom-16 right-0 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="widget-title" x-ref="panel"
        @keydown.tab.prevent="trapFocus($event)" @keydown.escape.prevent="$wire.closeWidget(); isOpen = false">
        {{-- Widget Header --}}
        <div class="bg-primary-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                {{-- Bot Avatar --}}
                <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                    <x-heroicon-o-cpu-chip class="w-5 h-5" aria-hidden="true" />
                </div>
                <div>
                    <h2 id="widget-title" class="font-semibold text-sm">{{ __('ollama.widget.title', [], 'ms') }}</h2>
                    @if ($this->isAuthenticated)
                        <p class="text-xs text-primary-100">
                            {{ __('ollama.widget.welcome_user', ['name' => $this->userName], 'ms') }}</p>
                    @else
                        <p class="text-xs text-primary-100">{{ __('ollama.widget.welcome_guest', [], 'ms') }}</p>
                    @endif
                </div>
            </div>

            {{-- Header Actions --}}
            <div class="flex items-center space-x-1">
                {{-- Minimize Button --}}
                {{-- WCAG 2.5.8: Touch target minimum 44×44px (min-h-11 min-w-11) --}}
                <button wire:click="minimizeWidget"
                    class="p-2 hover:bg-primary-500 rounded transition-colors min-h-11 min-w-11 flex items-center justify-center"
                    aria-label="{{ __('ollama.widget.minimize', [], 'ms') }}" type="button">
                    <x-heroicon-o-minus class="w-4 h-4" aria-hidden="true" />
                </button>

                {{-- Close Button --}}
                {{-- WCAG 2.5.8: Touch target minimum 44×44px (min-h-11 min-w-11) --}}
                <button wire:click="closeWidget"
                    class="p-2 hover:bg-primary-500 rounded transition-colors min-h-11 min-w-11 flex items-center justify-center"
                    aria-label="{{ __('ollama.widget.close', [], 'ms') }}" type="button">
                    <x-heroicon-o-x-mark class="w-4 h-4" aria-hidden="true" />
                </button>
            </div>
        </div>

        {{-- Widget Content (Hidden when minimized) --}}
        <div x-show="!isMinimized" class="flex flex-col h-96">
            {{-- Messages Area --}}
            <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50 dark:bg-gray-900" role="log"
                aria-label="{{ __('ollama.widget.conversation_log', [], 'ms') }}" aria-live="polite"
                x-ref="messagesContainer">
                @forelse($messages as $index => $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}"
                        wire:key="msg-{{ $index }}">
                        <div
                            class="max-w-xs {{ $message['role'] === 'user' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100' }} rounded-lg px-3 py-2 shadow-sm border border-gray-200 dark:border-gray-700">
                            {{-- Message Role Label (Screen Reader Only) --}}
                            <span class="sr-only">
                                {{ $message['role'] === 'user' ? __('ollama.widget.user_message', [], 'ms') : __('ollama.widget.bot_message', [], 'ms') }}
                            </span>

                            {{-- Message Content --}}
                            <p class="text-sm leading-relaxed">{{ $message['content'] }}</p>

                            {{-- Timestamp --}}
                            <time
                                class="text-xs {{ $message['role'] === 'user' ? 'text-primary-100' : 'text-gray-500 dark:text-gray-400' }} mt-1 block"
                                datetime="{{ $message['timestamp'] }}">
                                {{ \Carbon\Carbon::parse($message['timestamp'])->format('H:i') }}
                            </time>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-12 h-12 mx-auto mb-2 opacity-50"
                            aria-hidden="true" />
                        <p class="text-sm">{{ __('ollama.widget.no_messages', [], 'ms') }}</p>
                    </div>
                @endforelse

                {{-- Loading Indicator --}}
                @if ($isLoading)
                    <div class="flex justify-start">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg px-3 py-2 shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                        style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                        style="animation-delay: 0.2s"></div>
                                </div>
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400">{{ __('ollama.widget.typing', [], 'ms') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Error Message --}}
            @if ($errorMessage)
                <div
                    class="px-4 py-2 bg-danger-50 dark:bg-danger-900/20 border-t border-danger-200 dark:border-danger-800">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-s-exclamation-circle class="w-4 h-4 text-danger-500" aria-hidden="true" />
                        <p class="text-sm text-danger-700 dark:text-danger-400" role="alert">{{ $errorMessage }}</p>
                    </div>
                </div>
            @endif

            {{-- Input Area --}}
            <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <form wire:submit="submitQuery" class="space-y-3">
                    {{-- Query Input --}}
                    <div class="flex space-x-2">
                        <label for="widget-query" class="sr-only">
                            {{ __('ollama.widget.query_label', [], 'ms') }}
                        </label>
                        <input type="text" id="widget-query" wire:model="query" wire:keydown.enter="submitQuery"
                            placeholder="{{ __('ollama.widget.query_placeholder', [], 'ms') }}"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 min-h-11"
                            maxlength="500" {{ $isLoading ? 'disabled' : '' }} aria-describedby="widget-query-help" data-initial-focus>

                        {{-- Send Button --}}
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white rounded-md transition-colors focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 min-h-11 min-w-11 flex items-center justify-center"
                            {{ $isLoading ? 'disabled' : '' }}
                            aria-label="{{ __('ollama.widget.send_button', [], 'ms') }}">
                            @if ($isLoading)
                                <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" aria-hidden="true" />
                            @else
                                <x-heroicon-s-paper-airplane class="w-4 h-4" aria-hidden="true" />
                            @endif
                        </button>
                    </div>

                    {{-- Help Text --}}
                    <p id="widget-query-help" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('ollama.widget.query_help', [], 'ms') }}
                    </p>

                    {{-- Action Buttons --}}
                    <div class="flex justify-between items-center pt-2">
                        <button type="button" wire:click="clearConversation"
                            class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 underline focus:ring-2 focus:ring-primary-500 rounded px-1 py-1 inline-flex items-center gap-1">
                            <x-heroicon-o-trash class="w-3 h-3" aria-hidden="true" />
                            {{ __('ollama.widget.clear_conversation', [], 'ms') }}
                        </button>

                        <button type="button" wire:click="openFullBot"
                            class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:ring-2 focus:ring-primary-500 rounded px-1 py-1 inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3" aria-hidden="true" />
                            {{ __('ollama.widget.open_full_bot', [], 'ms') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Minimized State --}}
        <div x-show="isMinimized" class="p-4 bg-gray-50 dark:bg-gray-900">
            <button wire:click="restoreWidget"
                class="w-full text-left text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:ring-2 focus:ring-primary-500 rounded px-2 py-1 inline-flex items-center gap-2">
                <x-heroicon-o-chevron-up class="w-4 h-4" aria-hidden="true" />
                {{ __('ollama.widget.click_to_restore', [], 'ms') }}
            </button>
        </div>
    </div>
</div>

{{-- JavaScript for Auto-scroll and Accessibility --}}
<script>
    document.addEventListener('livewire:init', () => {
        // Auto-scroll to bottom when new messages arrive
        Livewire.on('announce', (event) => {
            const container = document.querySelector('[x-ref="messagesContainer"]');
            if (container) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);
            }
        });

        // Handle keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // ESC to close widget
            if (e.key === 'Escape') {
                const widget = document.querySelector('[x-data*="isOpen"]');
                if (widget && widget.__x.$data.isOpen) {
                    Livewire.dispatch('closeWidget');
                }
            }
        });
    });

    // Alpine.js component definition
    Alpine.data('faqBotWidget', () => ({
        isOpen: false,
        isMinimized: false,
        announcement: '',

        init() {
            // Initialize with default values - will be overridden by initializeWidget
        },

        initializeWidget(initialIsOpen, initialIsMinimized, initialAnnouncement) {
            // Set initial values
            this.isOpen = initialIsOpen;
            this.isMinimized = initialIsMinimized;
            this.announcement = initialAnnouncement;

            // Wait for Livewire to be ready before setting up watchers
            this.$nextTick(() => {
                // Check if $wire is available, if not wait for livewire:init
                if (typeof $wire !== 'undefined') {
                    this.setupLivewireSync();
                } else {
                    // Listen for Livewire initialization
                    document.addEventListener('livewire:init', () => {
                        this.setupLivewireSync();
                    });
                }
            });

            // Handle announcements (this doesn't depend on $wire)
            this.$watch('announcement', value => {
                if (value) {
                    this.$dispatch('announce', { message: value });
                }
            });

            // Handle focus management (this doesn't depend on $wire)
            this.$watch('isOpen', (open) => {
                if (open) {
                    this.focusFirst();
                } else {
                    this.$nextTick(() => this.$refs.toggleButton?.focus());
                }
            });
        },

        setupLivewireSync() {
            // Sync with Livewire - only called when $wire is available
            if (typeof $wire !== 'undefined') {
                this.$watch(() => $wire.isOpen, (value) => this.isOpen = value);
                this.$watch(() => $wire.isMinimized, (value) => this.isMinimized = value);
                this.$watch(() => $wire.announcement, (value) => this.announcement = value);
            }
        },

        focusableEls() {
            return [...(this.$refs.panel?.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])') || [])]
                .filter((el) => !el.disabled && el.offsetParent !== null);
        },

        trapFocus(event) {
            if (!this.isOpen || this.isMinimized) return;
            const els = this.focusableEls();
            if (!els.length) return;
            const first = els[0];
            const last = els[els.length - 1];
            const active = document.activeElement;
            if (event.shiftKey && active === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && active === last) {
                event.preventDefault();
                first.focus();
            }
        },

        focusFirst() {
            this.$nextTick(() => {
                const target = this.$refs.panel?.querySelector('[data-initial-focus]') || this.focusableEls()[0];
                target?.focus();
            });
        }
    }));
</script>
