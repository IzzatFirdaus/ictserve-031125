<div class="min-h-screen flex flex-col bg-white dark:bg-slate-900">
    <!-- Header -->
    <header class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-orange-500/20 shadow-lg" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- MOTAC Logo -->
                    <img src="{{ asset('images/motac-logo.jpeg') }}" alt="{{ __('common.motac_logo') }}" class="h-12 w-auto">
                    <!-- BPM Logo -->
                    <img src="{{ asset('images/bpm-logo.png') }}" alt="{{ __('common.bpm_logo') }}" class="h-12 w-auto">
                    <div class="h-12 w-px bg-orange-500/30"></div>
                    <!-- AWS Logo (Correct smile logo) -->
                    <svg class="h-8 w-auto" viewBox="0 0 304 182" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M86.4 66.4c0 3.7.4 6.7 1.1 8.9.8 2.2 1.8 4.6 3.2 7.2.5.8.7 1.6.7 2.3 0 1-.6 2-1.9 3l-6.3 4.2c-.9.6-1.8.9-2.6.9-1 0-2-.5-3-1.4-1.4-1.5-2.6-3.1-3.6-4.7-1-1.7-2-3.6-3.1-5.9-7.8 9.2-17.6 13.8-29.4 13.8-8.4 0-15.1-2.4-20-7.2-4.9-4.8-7.4-11.2-7.4-19.2 0-8.5 3-15.4 9.1-20.6 6.1-5.2 14.2-7.8 24.5-7.8 3.4 0 6.9.3 10.6.8 3.7.5 7.5 1.3 11.5 2.2v-7.3c0-7.6-1.6-12.9-4.7-16-3.2-3.1-8.6-4.6-16.3-4.6-3.5 0-7.1.4-10.8 1.3-3.7.9-7.3 2-10.8 3.4-1.6.7-2.8 1.1-3.5 1.3-.7.2-1.2.3-1.6.3-1.4 0-2.1-1-2.1-3.1v-4.9c0-1.6.2-2.8.7-3.5.5-.7 1.4-1.4 2.8-2.1 3.5-1.8 7.7-3.3 12.6-4.5 4.9-1.3 10.1-1.9 15.6-1.9 11.9 0 20.6 2.7 26.2 8.1 5.5 5.4 8.3 13.6 8.3 24.6v32.4zm-40.6 15.2c3.3 0 6.7-.6 10.3-1.8 3.6-1.2 6.8-3.4 9.5-6.4 1.6-1.9 2.8-4 3.4-6.4.6-2.4 1-5.3 1-8.7v-4.2c-2.9-.7-6-1.3-9.2-1.7-3.2-.4-6.3-.6-9.4-.6-6.7 0-11.6 1.3-14.9 4-3.3 2.7-4.9 6.5-4.9 11.5 0 4.7 1.2 8.2 3.7 10.6 2.4 2.5 5.9 3.7 10.5 3.7zm80.3 10.8c-1.8 0-3-.3-3.8-1-.8-.6-1.5-2-2.1-3.9L96.7 10.2c-.6-2-.9-3.3-.9-4 0-1.6.8-2.5 2.4-2.5h9.8c1.9 0 3.2.3 3.9 1 .8.6 1.4 2 2 3.9l16.8 66.2 15.6-66.2c.5-2 1.1-3.3 1.9-3.9.8-.6 2.2-1 4-1h8c1.9 0 3.2.3 4 1 .8.6 1.5 2 1.9 3.9l15.8 67.1 17.3-67.1c.6-2 1.3-3.3 2-3.9.8-.6 2.1-1 3.9-1h9.3c1.6 0 2.5.8 2.5 2.5 0 .5-.1 1-.2 1.6-.1.6-.3 1.4-.7 2.5l-24.1 77.3c-.6 2-1.3 3.3-2.1 3.9-.8.6-2.1 1-3.8 1h-8.6c-1.9 0-3.2-.3-4-1-.8-.7-1.5-2-1.9-4L156 23l-15.4 64.4c-.5 2-1.1 3.3-1.9 4-.8.7-2.2 1-4 1h-8.6zm128.5 2.7c-5.2 0-10.4-.6-15.4-1.8-5-1.2-8.9-2.5-11.5-4-1.6-.9-2.7-1.9-3.1-2.8-.4-.9-.6-1.9-.6-2.8v-5.1c0-2.1.8-3.1 2.3-3.1.6 0 1.2.1 1.8.3.6.2 1.5.6 2.5 1 3.4 1.5 7.1 2.7 11 3.5 4 .8 7.9 1.2 11.9 1.2 6.3 0 11.2-1.1 14.6-3.3 3.4-2.2 5.2-5.4 5.2-9.5 0-2.8-.9-5.1-2.7-7-1.8-1.9-5.2-3.6-10.1-5.2L246 52c-7.3-2.3-12.7-5.7-16-10.2-3.3-4.4-5-9.3-5-14.5 0-4.2.9-7.9 2.7-11.1 1.8-3.2 4.2-6 7.2-8.2 3-2.3 6.4-4 10.4-5.2 4-1.2 8.2-1.7 12.6-1.7 2.2 0 4.5.1 6.7.4 2.3.3 4.4.7 6.5 1.1 2 .5 3.9 1 5.7 1.6 1.8.6 3.2 1.2 4.2 1.8 1.4.8 2.4 1.6 3 2.5.6.8.9 1.9.9 3.3v4.7c0 2.1-.8 3.2-2.3 3.2-.8 0-2.1-.4-3.8-1.2-5.7-2.6-12.1-3.9-19.2-3.9-5.7 0-10.2.9-13.3 2.8-3.1 1.9-4.7 4.8-4.7 8.9 0 2.8 1 5.2 3 7.1 2 1.9 5.7 3.8 11 5.5l14.2 4.5c7.2 2.3 12.4 5.5 15.5 9.6 3.1 4.1 4.6 8.8 4.6 14 0 4.3-.9 8.2-2.6 11.6-1.8 3.4-4.2 6.4-7.3 8.8-3.1 2.5-6.8 4.3-11.1 5.6-4.5 1.4-9.2 2.1-14.3 2.1z" fill="#FF9900"/>
                        <path d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z" fill="#FF9900"/>
                        <path d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 4-9.9 12.9-32.2 8.7-37.5z" fill="#FF9900"/>
                    </svg>
                    <h1 class="text-xl font-bold bg-linear-to-r from-orange-400 to-orange-600 bg-clip-text text-transparent">
                        Sembang Bedrock
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Theme switcher -->
                    <div class="flex items-center gap-2">
                    <div id="connection-status" class="text-sm text-orange-600 dark:text-orange-400 hidden" role="status" aria-live="polite">Sambungan belum tersedia</div>
                    <button id="theme-toggle" aria-label="Tukar tema" class="p-2 rounded hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-gray-500 dark:text-gray-200 min-h-11 min-w-11">
                        <x-heroicon-o-sun id="theme-icon-sun" class="w-5 h-5 hidden" />
                        <x-heroicon-o-moon id="theme-icon-moon" class="w-5 h-5 hidden" />
                    </button>
                    </div>
                    <a href="{{ route('welcome') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-orange-500 transition-colors">
                        {{ __('footer.home') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex">
        <!-- Sidebar -->
        <div class="{{ $showSidebar ? 'w-64' : 'w-0' }} transition-all duration-300 bg-white dark:bg-slate-900 border-r border-gray-200 dark:border-orange-900/30 overflow-hidden">
            <div class="p-4">
                    <button wire:click="newConversation" class="w-full px-4 py-2 bg-linear-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg mb-4 font-semibold shadow-lg shadow-orange-500/20 min-h-11" aria-label="Perbualan baharu">
                    + Perbualan Baharu
                </button>
                <div class="space-y-2">
                    @foreach($conversations as $conv)
                        <div class="group flex items-center gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                            <div wire:click="loadConversation({{ $conv->id }})" class="flex-1 truncate text-sm text-gray-300 {{ $conversationId === $conv->id ? 'font-bold text-orange-400' : '' }}">
                                {{ $conv->title }}
                            </div>
                            <button wire:click="deleteConversation({{ $conv->id }})" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-500 transition-opacity min-h-11 min-w-11" aria-label="Padam perbualan">
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex-1 p-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-2xl shadow-orange-500/10 p-6 border border-gray-200 dark:border-orange-900/30">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="$toggle('showSidebar')" class="p-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg transition-colors text-gray-500 dark:text-gray-200 min-h-11 min-w-11">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sembang <span class="text-orange-500">AWS Bedrock</span></h2>
                    <div class="w-44"></div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Model</label>
                        <select wire:model="model" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-orange-900/30 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="opus">Claude Opus 4.5 (Paling Berkuasa)</option>
                            <option value="sonnet">Claude Sonnet 4.5 (Seimbang)</option>
                            <option value="haiku">Claude Haiku 4.5 (Paling Pantas)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Pilihan</label>
                        <label class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-orange-900/30 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            <input type="checkbox" wire:model="useInternet" class="rounded bg-white dark:bg-slate-800 border-gray-200 dark:border-orange-900/30 text-orange-500 focus:ring-orange-500">
                            <x-heroicon-o-globe-alt class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                            <span class="text-sm text-gray-300">Cari web</span>
                        </label>
                    </div>
                </div>

                <div id="messages-list" role="log" aria-live="polite" aria-relevant="additions" class="mb-6 h-96 overflow-y-auto border border-gray-200 dark:border-orange-900/30 rounded-lg p-4 bg-white dark:bg-slate-900/50 backdrop-blur-sm">
                    @forelse($messages as $message)
                        <div class="mb-4 {{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}" role="article" aria-label="{{ $message['role'] }} message">
                            <div class="inline-block max-w-[80%] p-4 rounded-lg shadow-lg {{ $message['role'] === 'user' ? 'bg-linear-to-r from-orange-500 to-orange-600 text-white' : 'bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-orange-900/30 text-gray-900 dark:text-gray-100' }}">
                                @if($message['role'] === 'user')
                                    <p class="whitespace-pre-wrap">{{ $message['content'] }}</p>
                                @else
                                    <div class="prose prose-sm prose-invert max-w-none prose-headings:text-orange-400 prose-a:text-orange-400 prose-code:text-orange-300 prose-pre:bg-slate-900 prose-pre:border prose-pre:border-orange-900/30">{!! (new \League\CommonMark\CommonMarkConverter())->convert($message['content'])->getContent() !!}</div>
                                @endif
                                @if($message['role'] === 'assistant')
                                    <p class="text-xs mt-2 text-orange-400/70">
                                        {{ ucfirst($message['model'] ?? '') }}
                                        @if(isset($message['tokens']))
                                            • {{ $message['tokens'] }} token
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-500">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-16 h-16 mb-4 text-orange-500/30" />
                            <p class="text-center">Mula perbualan dengan AWS Bedrock...</p>
                        </div>
                    @endforelse
                </div>

                <form wire:submit="send" class="flex gap-3" role="form" aria-label="Send message form">
                    <label for="prompt" class="sr-only">{{ __('chat.message') }}</label>
                    <input
                        type="text"
                        id="prompt"
                        wire:model="prompt"
                        placeholder="Taip mesej anda..."
                        class="flex-1 px-4 py-3 bg-white dark:bg-slate-900 border border-gray-200 dark:border-orange-900/30 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    >
                    <button
                        type="submit"
                        class="px-8 py-3 bg-linear-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg font-semibold shadow-lg shadow-orange-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all min-h-11"
                        wire:loading.attr="disabled"
                        wire:target="send"
                        @if($sending) disabled @endif
                        aria-label="Hantar mesej"
                    >
                            <span wire:loading.remove wire:target="send" class="flex items-center gap-2">
                            <x-heroicon-o-paper-airplane class="w-5 h-5" />
                                {{ __('chat.send', [], app()->getLocale()) !== 'chat.send' ? __('chat.send', [], app()->getLocale()) : 'Hantar' }}
                        </span>
                        <span wire:loading wire:target="send" class="flex items-center gap-2">
                            <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" />
                            Menghantar...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-orange-500/20 mt-auto" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/motac-logo.jpeg') }}" alt="{{ __('common.motac_logo') }}" class="h-8 w-auto">
                    <img src="{{ asset('images/bpm-logo.png') }}" alt="{{ __('common.bpm_logo') }}" class="h-8 w-auto">
                </div>
                <p class="text-gray-400 text-sm text-center">
                    &copy; {{ date('Y') }} {{ __('footer.ministry_name') }}. {{ __('footer.all_rights_reserved') }}.
                </p>
                <div class="flex items-center space-x-4 text-sm text-gray-400">
                    <span>{{ __('footer.wcag_compliant') }}</span>
                    <span aria-hidden="true">|</span>
                    <span>Dikuasakan oleh AWS Bedrock</span>
                </div>
            </div>
        </div>
    </footer>
    <!-- Theme toggle and localStorage script: Ensures light default and optional dark mode -->
    <script>
        (function() {
            const root = document.documentElement;
            const btn = document.getElementById('theme-toggle');
            const iconSun = document.getElementById('theme-icon-sun');
            const iconMoon = document.getElementById('theme-icon-moon');

            function updateIcons(theme) {
                if (!iconSun || !iconMoon) return;
                if (theme === 'dark') {
                    iconSun.classList.add('hidden');
                    iconMoon.classList.remove('hidden');
                } else {
                    iconSun.classList.remove('hidden');
                    iconMoon.classList.add('hidden');
                }
            }

            function setTheme(theme) {
                if (theme === 'dark') {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
                localStorage.setItem('theme', theme);
                updateIcons(theme);
            }

            // Get stored preference (light default if not set)
            const stored = localStorage.getItem('theme') || 'light';
            setTheme(stored);

            if (btn) {
                btn.addEventListener('click', function () {
                    const current = root.classList.contains('dark') ? 'dark' : 'light';
                    const next = current === 'dark' ? 'light' : 'dark';
                    setTheme(next);
                });
            }
            // Provide a safe noop for keyboardShortcuts if not present to avoid Alpine errors in dev
            if (typeof window.keyboardShortcuts !== 'function') {
                window.keyboardShortcuts = function () { /* noop safe stub for keyboard shortcuts */ };
            }
            // Connection status check for Echo/Reverb
            function updateConnectionStatus() {
                const status = document.getElementById('connection-status');
                if (!status) return;
                try {
                    const Echo = window.Echo;
                    let connected = false;
                    if (Echo && Echo.connector) {
                        // common locations of connected flags
                        connected = !!(Echo.connector.socket && Echo.connector.socket.connected) || !!(Echo.connector.pusher && Echo.connector.pusher.connection?.state === 'connected');
                    }
                    if (!connected) {
                        status.classList.remove('hidden');
                        status.textContent = 'Sambungan belum tersedia';
                    } else {
                        status.classList.add('hidden');
                        status.textContent = '';
                    }
                } catch (e) {
                    status.classList.remove('hidden');
                    status.textContent = 'Sambungan belum tersedia';
                }
            }
            updateConnectionStatus();
            // Recheck status periodically
            setInterval(updateConnectionStatus, 5000);
        })();
    </script>
</div>
