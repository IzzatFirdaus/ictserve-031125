<div class="bg-linear-to-br from-gray-900 via-slate-900 to-gray-900 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-slate-900 border-b border-orange-500/20 shadow-lg" role="banner">
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
                        Bedrock Chat
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('welcome') }}" class="text-sm text-gray-400 hover:text-orange-500 transition-colors">
                        {{ __('footer.home') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex">
        <!-- Sidebar -->
        <div class="{{ $showSidebar ? 'w-64' : 'w-0' }} transition-all duration-300 bg-slate-900 border-r border-orange-900/30 overflow-hidden">
            <div class="p-4">
                <button wire:click="newConversation" class="w-full px-4 py-2 bg-linear-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg mb-4 font-semibold shadow-lg shadow-orange-500/20 min-h-44">
                    + New Chat
                </button>
                <div class="space-y-2">
                    @foreach($conversations as $conv)
                        <div class="group flex items-center gap-2 p-2 rounded hover:bg-slate-800 cursor-pointer transition-colors">
                            <div wire:click="loadConversation({{ $conv->id }})" class="flex-1 truncate text-sm text-gray-300 {{ $conversationId === $conv->id ? 'font-bold text-orange-400' : '' }}">
                                {{ $conv->title }}
                            </div>
                            <button wire:click="deleteConversation({{ $conv->id }})" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-500 transition-opacity min-h-44 min-w-44">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex-1 p-6">
            <div class="bg-slate-800 rounded-lg shadow-2xl shadow-orange-500/10 p-6 border border-orange-900/30">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="$toggle('showSidebar')" class="p-2 hover:bg-slate-700 rounded-lg transition-colors text-gray-300 min-h-44 min-w-44">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h2 class="text-2xl font-bold text-white">AWS Bedrock <span class="text-orange-500">Chat</span></h2>
                    <div class="w-44"></div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-300">Model</label>
                        <select wire:model="model" class="w-full px-4 py-2 bg-slate-900 border border-orange-900/30 rounded-lg text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="opus">🧠 Claude Opus 4.5 (Most Powerful)</option>
                            <option value="sonnet">⚡ Claude Sonnet 4.5 (Balanced)</option>
                            <option value="haiku">🚀 Claude Haiku 4.5 (Fastest)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-300">Options</label>
                        <label class="flex items-center gap-2 px-4 py-2 bg-slate-900 border border-orange-900/30 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors">
                            <input type="checkbox" wire:model="useInternet" class="rounded bg-slate-800 border-orange-900/30 text-orange-500 focus:ring-orange-500">
                            <span class="text-sm text-gray-300">🌐 Search web</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6 h-96 overflow-y-auto border border-orange-900/30 rounded-lg p-4 bg-slate-900/50 backdrop-blur-sm">
                    @forelse($messages as $message)
                        <div class="mb-4 {{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                            <div class="inline-block max-w-[80%] p-4 rounded-lg shadow-lg {{ $message['role'] === 'user' ? 'bg-linear-to-r from-orange-500 to-orange-600 text-white' : 'bg-slate-800 border border-orange-900/30 text-gray-100' }}">
                                @if($message['role'] === 'user')
                                    <p class="whitespace-pre-wrap">{{ $message['content'] }}</p>
                                @else
                                    <div class="prose prose-sm prose-invert max-w-none prose-headings:text-orange-400 prose-a:text-orange-400 prose-code:text-orange-300 prose-pre:bg-slate-900 prose-pre:border prose-pre:border-orange-900/30">{!! (new \League\CommonMark\CommonMarkConverter())->convert($message['content'])->getContent() !!}</div>
                                @endif
                                @if($message['role'] === 'assistant')
                                    <p class="text-xs mt-2 text-orange-400/70">{{ ucfirst($message['model']) }} • {{ $message['tokens'] }} tokens</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-500">
                            <svg class="w-16 h-16 mb-4 text-orange-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-center">Start a conversation with AWS Bedrock...</p>
                        </div>
                    @endforelse
                </div>

                <form wire:submit="send" class="flex gap-3">
                    <input
                        type="text"
                        wire:model="prompt"
                        placeholder="Type your message..."
                        class="flex-1 px-4 py-3 bg-slate-900 border border-orange-900/30 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    >
                    <button
                        type="submit"
                        class="px-8 py-3 bg-linear-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg font-semibold shadow-lg shadow-orange-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all min-h-44"
                        wire:loading.attr="disabled"
                        wire:target="send"
                        @if($sending) disabled @endif
                    >
                        <span wire:loading.remove wire:target="send" class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Send
                        </span>
                        <span wire:loading wire:target="send" class="flex items-center gap-2">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-orange-500/20 mt-auto" role="contentinfo">
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
                    <span>Powered by AWS Bedrock</span>
                </div>
            </div>
        </div>
    </footer>
</div>
