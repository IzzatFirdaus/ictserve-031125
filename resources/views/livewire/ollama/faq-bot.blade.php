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
                        {{-- AWS Bedrock Smile Logo (D00 True Hybrid Architecture) --}}
                        <svg class="w-6 h-6" viewBox="0 0 304 182" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M86.4 66.4c0 3.7.4 6.7 1.1 8.9.8 2.2 1.8 4.6 3.2 7.2.5.8.7 1.6.7 2.3 0 1-.6 2-1.9 3l-6.3 4.2c-.9.6-1.8.9-2.6.9-1 0-2-.5-3-1.4-1.4-1.5-2.6-3.1-3.6-4.7-1-1.7-2-3.6-3.1-5.9-7.8 9.2-17.6 13.8-29.4 13.8-8.4 0-15.1-2.4-20-7.2-4.9-4.8-7.4-11.2-7.4-19.2 0-8.5 3-15.4 9.1-20.6 6.1-5.2 14.2-7.8 24.5-7.8 3.4 0 6.9.3 10.6.8 3.7.5 7.5 1.3 11.5 2.2v-7.3c0-7.6-1.6-12.9-4.7-16-3.2-3.1-8.6-4.6-16.3-4.6-3.5 0-7.1.4-10.8 1.3-3.7.9-7.3 2-10.8 3.4-1.6.7-2.8 1.1-3.5 1.3-.7.2-1.2.3-1.6.3-1.4 0-2.1-1-2.1-3.1v-4.9c0-1.6.2-2.8.7-3.5.5-.7 1.4-1.4 2.8-2.1 3.5-1.8 7.7-3.3 12.6-4.5 4.9-1.3 10.1-1.9 15.6-1.9 11.9 0 20.6 2.7 26.2 8.1 5.5 5.4 8.3 13.6 8.3 24.6v32.4zm-40.6 15.2c3.3 0 6.7-.6 10.3-1.8 3.6-1.2 6.8-3.4 9.5-6.4 1.6-1.9 2.8-4 3.4-6.4.6-2.4 1-5.3 1-8.7v-4.2c-2.9-.7-6-1.3-9.2-1.7-3.2-.4-6.3-.6-9.4-.6-6.7 0-11.6 1.3-14.9 4-3.3 2.7-4.9 6.5-4.9 11.5 0 4.7 1.2 8.2 3.7 10.6 2.4 2.5 5.9 3.7 10.5 3.7zm80.3 10.8c-1.8 0-3-.3-3.8-1-.8-.6-1.5-2-2.1-3.9L96.7 10.2c-.6-2-.9-3.3-.9-4 0-1.6.8-2.5 2.4-2.5h9.8c1.9 0 3.2.3 3.9 1 .8.6 1.4 2 2 3.9l16.8 66.2 15.6-66.2c.5-2 1.1-3.3 1.9-3.9.8-.6 2.2-1 4-1h8c1.9 0 3.2.3 4 1 .8.6 1.5 2 1.9 3.9l15.8 67.1 17.3-67.1c.6-2 1.3-3.3 2-3.9.8-.6 2.1-1 3.9-1h9.3c1.6 0 2.5.8 2.5 2.5 0 .5-.1 1-.2 1.6-.1.6-.3 1.4-.7 2.5l-24.1 77.3c-.6 2-1.3 3.3-2.1 3.9-.8.6-2.1 1-3.8 1h-8.6c-1.9 0-3.2-.3-4-1-.8-.7-1.5-2-1.9-4L156 23l-15.4 64.4c-.5 2-1.1 3.3-1.9 4-.8.7-2.2 1-4 1h-8.6zm128.5 2.7c-5.2 0-10.4-.6-15.4-1.8-5-1.2-8.9-2.5-11.5-4-1.6-.9-2.7-1.9-3.1-2.8-.4-.9-.6-1.9-.6-2.8v-5.1c0-2.1.8-3.1 2.3-3.1.6 0 1.2.1 1.8.3.6.2 1.5.6 2.5 1 3.4 1.5 7.1 2.7 11 3.5 4 .8 7.9 1.2 11.9 1.2 6.3 0 11.2-1.1 14.6-3.3 3.4-2.2 5.2-5.4 5.2-9.5 0-2.8-.9-5.1-2.7-7-1.8-1.9-5.2-3.6-10.1-5.2L246 52c-7.3-2.3-12.7-5.7-16-10.2-3.3-4.4-5-9.3-5-14.5 0-4.2.9-7.9 2.7-11.1 1.8-3.2 4.2-6 7.2-8.2 3-2.3 6.4-4 10.4-5.2 4-1.2 8.2-1.7 12.6-1.7 2.2 0 4.5.1 6.7.4 2.3.3 4.4.7 6.5 1.1 2 .5 3.9 1 5.7 1.6 1.8.6 3.2 1.2 4.2 1.8 1.4.8 2.4 1.6 3 2.5.6.8.9 1.9.9 3.3v4.7c0 2.1-.8 3.2-2.3 3.2-.8 0-2.1-.4-3.8-1.2-5.7-2.6-12.1-3.9-19.2-3.9-5.7 0-10.2.9-13.3 2.8-3.1 1.9-4.7 4.8-4.7 8.9 0 2.8 1 5.2 3 7.1 2 1.9 5.7 3.8 11 5.5l14.2 4.5c7.2 2.3 12.4 5.5 15.5 9.6 3.1 4.1 4.6 8.8 4.6 14 0 4.3-.9 8.2-2.6 11.6-1.8 3.4-4.2 6.4-7.3 8.8-3.1 2.5-6.8 4.3-11.1 5.6-4.5 1.4-9.2 2.1-14.3 2.1z"
                                fill="#FF9900" />
                            <path
                                d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z"
                                fill="#FF9900" />
                            <path
                                d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 4-9.9 12.9-32.2 8.7-37.5z"
                                fill="#FF9900" />
                        </svg>
                    @else
                        {{-- Ollama Local AI Icon (Llama) --}}
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M9 9h.01M15 9h.01" />
                            <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                            <path d="M8 4c-1 1-2 2.5-2 4M16 4c1 1 2 2.5 2 4" />
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
                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-md transition-all min-h-11 {{ $aiProvider === 'ollama' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400' }}"
                    aria-label="Tukar ke Ollama Tempatan">
                    {{-- Ollama Local AI Icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="8" />
                        <path d="M9 10h.01M15 10h.01" />
                        <path d="M9.5 14a3.5 3.5 0 0 0 5 0" />
                    </svg>
                    Ollama
                </button>
                <button type="button" wire:click="switchProvider('bedrock')"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-md transition-all min-h-11 {{ $aiProvider === 'bedrock' ? 'bg-white dark:bg-gray-700 text-orange-600 dark:text-orange-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400' }}"
                    aria-label="Tukar ke AWS Bedrock">
                    {{-- AWS Smile Icon (Compact) --}}
                    <svg class="w-4 h-4" viewBox="0 0 304 182" fill="currentColor" aria-hidden="true">
                        <path
                            d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z" />
                        <path
                            d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 4-9.9 12.9-32.2 8.7-37.5z" />
                    </svg>
                    Bedrock
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
