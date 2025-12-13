{{--
    Ticket Details - MyDS Design System
    Trace: D13 §2.2-2.7 (Design Tokens), D14 §4.1 (Color Palette)
    WCAG 2.2 AA Compliant - 4.5:1 text contrast, 3:1 UI contrast, 44px touch targets
--}}
<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-heading font-semibold text-gray-900 dark:text-white">
                {{ __('Tiket') }} {{ $ticket->ticket_number }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $ticket->subject }}</p>
        </div>

        <div class="flex items-center gap-3">
            <span
                role="status"
                aria-label="{{ __('helpdesk.status_aria', ['status' => \Illuminate\Support\Str::headline($ticket->status)]) }}"
                class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900/30 px-3 py-1 text-sm font-medium text-primary-700 dark:text-primary-400">
                {{ \Illuminate\Support\Str::headline($ticket->status) }}
            </span>

            @if ($ticket->user_id === null && $ticket->guest_email === auth()->user()->email)
            <x-ui.button wire:click="claimTicket" icon="heroicon-o-hand-raised">
                {{ __('Tuntut Tiket Ini') }}
            </x-ui.button>
            @endif
        </div>
    </header>

    <section aria-label="{{ __('Maklumat Tiket') }}" class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2 shadow-card">
            <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">{{ __('Perincian') }}</h2>

            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Kategori') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        {{ $ticket->category?->name ?? __('Tidak dinyatakan') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Keutamaan') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ ucfirst($ticket->priority) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bahagian') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        {{ $ticket->division?->name ?? ($ticket->guest_division ?? __('Tidak dinyatakan')) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pegawai Bertugas') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        {{ $ticket->assignedUser?->name ?? __('Belum ditugaskan') }}
                    </dd>
                </div>
            </dl>

            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Keterangan Aduan') }}</h3>
                <p class="mt-2 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $ticket->description }}</p>
            </div>

            @if ($ticket->attachments->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Lampiran') }}</h3>
                <ul class="mt-2 space-y-2">
                    @foreach ($ticket->attachments as $attachment)
                    <li class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $attachment->file_name }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </x-ui.card>

        <x-ui.card class="shadow-card">
            <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">{{ __('Jejak Masa') }}
            </h2>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Dicipta') }}</dt>
                    <dd class="text-gray-900 dark:text-gray-100">
                        {{ $ticket->created_at?->translatedFormat('d M Y, h:i A') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Ditugaskan') }}</dt>
                    <dd class="text-gray-900 dark:text-gray-100">
                        {{ $ticket->assigned_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Respon Pertama') }}</dt>
                    <dd class="text-gray-900 dark:text-gray-100">
                        {{ $ticket->responded_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Selesai') }}</dt>
                    <dd class="text-gray-900 dark:text-gray-100">
                        {{ $ticket->resolved_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Ditutup') }}</dt>
                    <dd class="text-gray-900 dark:text-gray-100">
                        {{ $ticket->closed_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}
                    </dd>
                </div>
            </dl>
        </x-ui.card>
    </section>

    <section aria-label="{{ __('Komen & Perbincangan') }}" class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2 shadow-card">
            <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white">{{ __('Maklum Balas') }}</h2>

            <div class="mt-4 space-y-4">
                @forelse ($ticket->comments as $comment)
                <article
                    class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 transition-colors duration-200">
                    <header class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $comment->commenter_name ?? $comment->user?->name }}
                            </p>
                            <p class="text-gray-500 dark:text-gray-400">
                                {{ $comment->created_at?->diffForHumans() }}
                            </p>
                        </div>
                        @if ($comment->is_internal)
                        <span
                            class="rounded-full bg-warning-100 dark:bg-warning-900/30 px-2.5 py-0.5 text-xs font-medium text-warning-700 dark:text-warning-400">
                            {{ __('Nota Dalaman') }}
                        </span>
                        @endif
                    </header>
                    <p class="mt-3 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                        {{ $comment->comment }}
                    </p>
                </article>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Tiada maklum balas buat masa ini.') }}
                </p>
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card class="shadow-card">
            <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">{{ __('Tambah Komen') }}
            </h2>

            <form wire:submit.prevent="addComment" class="space-y-4">
                <x-form.textarea name="newComment" wire:model.live.debounce.300ms="newComment" rows="5"
                    placeholder="{{ __('Masukkan maklum balas anda di sini...') }}" required />

                <x-ui.button type="submit" icon="heroicon-o-chat-bubble-left-ellipsis" :disabled="$addingComment">
                    {{ __('Hantar Maklum Balas') }}
                </x-ui.button>
            </form>
        </x-ui.card>
    </section>
</div>