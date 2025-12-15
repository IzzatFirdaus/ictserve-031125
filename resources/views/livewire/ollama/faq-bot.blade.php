{{--
    FAQ Bot AI Component - Clean Chat-First UI (D00-D18 Compliant)
    ICTServe v3.6.0 - Bahasa Melayu sahaja (D15 v3.6.0)
    MyDS Design System v2025.2 - WCAG 2.2 AA Compliant
--}}
<div class="flex flex-col h-full max-w-4xl mx-auto px-4 md:px-6 lg:px-8" lang="ms">

    {{-- Screen Reader Live Region (WCAG 2.2 AA) --}}
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

    {{-- Compact Header --}}
    <header class="py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $aiProvider === 'bedrock' ? 'bg-orange-100 dark:bg-orange-900/30' : 'bg-primary-100 dark:bg-primary-900/30' }}">
                    @if ($aiProvider === 'bedrock')
                        {{-- AWS Bedrock Icon --}}
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24"
                            fill="currentColor">
                            <rect x="2" y="4" width="20" height="2" rx="1" />
                            <rect x="2" y="8" width="20" height="2" rx="1" />
                            <rect x="2" y="12" width="20" height="2" rx="1" />
                            <rect x="2" y="16" width="20" height="2" rx="1" />
                            <circle cx="6" cy="5" r="0.5" opacity="0.7" />
                            <circle cx="6" cy="9" r="0.5" opacity="0.7" />
                            <circle cx="6" cy="13" r="0.5" opacity="0.7" />
                            <circle cx="6" cy="17" r="0.5" opacity="0.7" />
                        </svg>
                    @else
                        {{-- Ollama Icon --}}
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                        FAQ Bot AI
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $aiProvider === 'bedrock' ? 'AWS Bedrock' : 'Ollama Tempatan' }}
                        @if ($aiProvider === 'bedrock')
                            · {{ ucfirst(str_replace('_', ' ', $bedrockModel)) }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex items-center gap-1">
                <button type="button" wire:click="clearConversation"
                    class="inline-flex items-center justify-center min-h-11 min-w-11 p-2 text-gray-500 hover:text-danger rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    aria-label="Padam perbualan" title="Padam perbualan">
                    <x-heroicon-o-trash class="w-5 h-5" />
                </button>
            </div>
        </div>
    </header>

    {{-- Chat Messages Area (Primary Focus) --}}
    <div id="faq-chat-messages" class="flex-1 overflow-y-auto py-4 space-y-4" role="log" aria-live="polite"
        aria-label="Kawasan mesej sembang">

        @forelse ($messages as $index => $message)
            <div wire:key="message-{{ $index }}"
                class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div
                    class="max-w-[85%] {{ $message['role'] === 'user'
                        ? 'bg-primary-600 text-white rounded-2xl rounded-br-md'
                        : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl rounded-bl-md' }} px-4 py-3">
                    <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message['content'] }}</p>
                    <div
                        class="flex items-center gap-2 mt-1.5 {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <time class="text-xs {{ $message['role'] === 'user' ? 'text-primary-200' : 'text-gray-400' }}">
                            {{ \Carbon\Carbon::parse($message['timestamp'])->format('H:i') }}
                        </time>
                        @if ($message['role'] === 'assistant' && isset($message['provider']))
                            <span
                                class="text-xs {{ $message['role'] === 'user' ? 'text-primary-200' : 'text-gray-400' }}">
                                · {{ $message['provider'] === 'bedrock' ? 'Bedrock' : 'Ollama' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-12">
                <div
                    class="w-16 h-16 bg-primary-50 dark:bg-primary-900/20 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-8 h-8 text-primary-500" />
                </div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Selamat datang ke FAQ Bot
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mb-6">
                    Tanya sebarang soalan berkaitan perkhidmatan ICT. Saya akan cuba membantu anda.
                </p>
            </div>
        @endforelse

        {{-- Loading Indicator --}}
        @if ($isLoading)
            <div class="flex justify-start">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="flex space-x-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                style="animation-delay: 0ms;"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                style="animation-delay: 150ms;"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                style="animation-delay: 300ms;"></span>
                        </div>
                        <span class="text-sm text-gray-500">Sedang memproses...</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Error Message --}}
    @if ($errorMessage)
        <div class="mx-0 mb-3 p-3 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg"
            role="alert">
            <div class="flex items-center gap-2">
                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-danger-600 shrink-0" />
                <p class="text-sm text-danger-800 dark:text-danger-200 flex-1">{{ $errorMessage }}</p>
                <button type="button" wire:click="$set('errorMessage', null)"
                    class="text-danger-600 hover:text-danger-800">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif

    {{-- Input Area with Inline Provider Selection --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 pb-4 space-y-3">
        {{-- Compact Provider Toggle --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                <button type="button" wire:click="switchProvider('ollama')"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-md transition-all min-h-11 {{ $aiProvider === 'ollama' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    Ollama
                </button>
                <button type="button" wire:click="switchProvider('bedrock')"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-md transition-all min-h-11 {{ $aiProvider === 'bedrock' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="2" y="5" width="20" height="1.5" rx="0.75" />
                        <rect x="2" y="9" width="20" height="1.5" rx="0.75" />
                        <rect x="2" y="13" width="20" height="1.5" rx="0.75" />
                        <rect x="2" y="17" width="20" height="1.5" rx="0.75" />
                    </svg>
                    AWS
                </button>
            </div>

            {{-- Bedrock Model Selector (Compact) --}}
            @if ($aiProvider === 'bedrock')
                <select wire:model.live="bedrockModel"
                    class="text-xs bg-gray-100 dark:bg-gray-800 border-0 rounded-lg py-2.5 px-3 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary-500 min-h-11">
                    <optgroup label="Claude">
                        <option value="haiku">Haiku (Pantas)</option>
                        <option value="sonnet">Sonnet (Seimbang)</option>
                        <option value="opus">Opus (Berkuasa)</option>
                    </optgroup>
                    <optgroup label="Nova">
                        <option value="nova_micro">Nova Micro</option>
                        <option value="nova_lite">Nova Lite</option>
                        <option value="nova_pro">Nova Pro</option>
                    </optgroup>
                    <optgroup label="Titan">
                        <option value="titan_text_lite">Titan Lite</option>
                        <option value="titan_text_express">Titan Express</option>
                    </optgroup>
                </select>
            @endif
        </div>

        {{-- Chat Input --}}
        <form wire:submit="submitQuery" class="flex gap-2">
            <div class="flex-1 relative">
                <input type="text" id="faq-chat-input" wire:model.live="query" placeholder="Taip soalan anda..."
                    maxlength="500" autocomplete="off" @disabled($isLoading)
                    class="w-full min-h-11 px-4 py-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors disabled:opacity-50" />
            </div>
            <button type="submit" @disabled($isLoading || empty(trim($query ?? '')))
                class="inline-flex items-center justify-center min-h-11 min-w-11 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                @if ($isLoading)
                    <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" />
                @else
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                @endif
            </button>
        </form>

        {{-- Character count (subtle) --}}
        <div class="text-right text-xs text-gray-400">
            {{ strlen($query ?? '') }}/500
        </div>
    </div>
</div>
