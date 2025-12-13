{{-- 
    FAQ Bot AI Component - WCAG 2.2 AA Compliant
    ICTServe v3.6.0 - Bahasa Melayu sahaja (D15 v3.6.0)
    Requirements: 1.1, 1.4, 5.1, 5.2, 5.3, 5.6, 5.7
--}}
<div class="flex flex-col h-full bg-white dark:bg-gray-900 rounded-l shadow-card border border-gray-200 dark:border-gray-700"
    x-data="{
        announcement: '',
        init() {
            Livewire.on('announce', ({ message }) => {
                this.announcement = message;
            });
        }
    }" lang="ms">
    {{-- Skip Links for Keyboard Navigation (Req 5.2) --}}
    <nav class="sr-only focus-within:not-sr-only focus-within:absolute focus-within:z-50 focus-within:bg-white focus-within:p-4 focus-within:shadow-lg focus-within:rounded-m"
        aria-label="Pautan navigasi pantas">
        <a href="#faq-chat-input"
            class="text-primary-600 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
            Langkau ke ruangan sembang
        </a>
    </nav>

    {{-- Screen Reader Live Region (Req 5.7) --}}
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true" x-text="announcement"></div>

    {{-- Chat Header --}}
    <header class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full"
                aria-hidden="true">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    FAQ Bot AI
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Tanya soalan anda
                </p>
            </div>
        </div>

        {{-- Clear Button (Req 5.6 - 44x44px touch target) --}}
        <button type="button" wire:click="clearConversation"
            class="inline-flex items-center justify-center min-h-11 min-w-11 p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-m hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors"
            aria-label="Padam perbualan" title="Padam perbualan">
            <x-heroicon-o-trash class="w-5 h-5" aria-hidden="true" />
        </button>
    </header>


    {{-- Chat Messages Area (Req 5.3 - ARIA live regions) --}}
    <div id="faq-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4" role="log" aria-live="polite"
        aria-atomic="false" aria-relevant="additions" aria-label="Kawasan mesej sembang"
        @if ($isLoading) aria-busy="true" @endif>
        @forelse ($messages as $index => $message)
            <div wire:key="message-{{ $index }}"
                class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-l p-4 {{ $message['role'] === 'user'
                    ? 'bg-primary-600 text-white'
                    : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' }}"
                    role="article" aria-label="{{ $message['role'] === 'user' ? 'Mesej anda' : 'Respons AI' }}">
                    <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message['content'] }}</p>
                    <time
                        class="block mt-2 text-xs {{ $message['role'] === 'user' ? 'text-primary-200' : 'text-gray-500 dark:text-gray-400' }}"
                        datetime="{{ $message['timestamp'] }}">
                        {{ \Carbon\Carbon::parse($message['timestamp'])->format('H:i') }}
                    </time>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4"
                    aria-hidden="true">
                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Selamat datang ke FAQ Bot
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                    Tanya sebarang soalan berkaitan perkhidmatan ICT. Saya akan cuba membantu anda.
                </p>
            </div>
        @endforelse

        {{-- Loading Indicator (Req 5.7 - Accessible loading states) --}}
        @if ($isLoading)
            <div class="flex justify-start" role="status" aria-live="polite" aria-busy="true"
                aria-label="Sedang memproses respons">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-l p-4 flex items-center gap-3">
                    <div class="flex space-x-1" aria-hidden="true">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                            style="animation-delay: 0ms;"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                            style="animation-delay: 150ms;"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                            style="animation-delay: 300ms;"></span>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Sedang memproses...</span>
                    <span class="sr-only">Sedang memproses respons anda</span>
                </div>
            </div>
        @endif
    </div>


    {{-- Error Message (Req 5.7 - Accessible error states) --}}
    @if ($errorMessage)
        <div class="mx-4 mb-4 p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-l"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="flex items-start gap-3">
                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-danger-600 dark:text-danger-400 shrink-0 mt-0.5"
                    aria-hidden="true" />
                <div>
                    <p class="text-sm font-medium text-danger-800 dark:text-danger-200">
                        {{ $errorMessage }}
                    </p>
                    <button type="button" wire:click="$set('errorMessage', null)"
                        class="mt-2 text-sm text-danger-600 dark:text-danger-400 underline hover:no-underline focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Chat Input Form (Req 5.1, 5.2, 5.6) --}}
    <form wire:submit="submitQuery" class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex gap-3">
            <div class="flex-1">
                <label for="faq-chat-input" class="sr-only">
                    Masukkan soalan anda
                </label>
                <input type="text" id="faq-chat-input" wire:model="query" placeholder="Taip soalan anda di sini..."
                    maxlength="500" autocomplete="off" aria-describedby="query-help" @disabled($isLoading)
                    class="w-full min-h-11 px-4 py-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-l text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" />
                <p id="query-help" class="sr-only">
                    Tekan Enter untuk menghantar soalan. Maksimum 500 aksara.
                </p>
            </div>

            {{-- Submit Button (Req 5.6 - 44x44px touch target) --}}
            <button type="submit" @disabled($isLoading || empty($query))
                class="inline-flex items-center justify-center min-h-11 min-w-11 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-l focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="Hantar soalan">
                @if ($isLoading)
                    <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" aria-hidden="true" />
                    <span class="sr-only">Sedang menghantar...</span>
                @else
                    <x-heroicon-o-paper-airplane class="w-5 h-5" aria-hidden="true" />
                    <span class="sr-only">Hantar</span>
                @endif
            </button>
        </div>

        {{-- Character Count (Req 5.5) --}}
        <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
            <span>{{ strlen($query ?? '') }}/500</span>
            <span class="sr-only">{{ strlen($query ?? '') }} daripada 500 aksara digunakan</span>
        </div>
    </form>
</div>
