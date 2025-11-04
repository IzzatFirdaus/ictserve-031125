<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('Tiket') }} {{ $ticket->ticket_number }}
            </h1>
            <p class="text-gray-600">{{ $ticket->subject }}</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
                {{ \Illuminate\Support\Str::headline($ticket->status) }}
            </span>

            @if (is_null($ticket->user_id) && $ticket->guest_email === auth()->user()->email)
                <x-ui.button wire:click="claimTicket" icon="heroicon-o-hand-raised">
                    {{ __('Tuntut Tiket Ini') }}
                </x-ui.button>
            @endif
        </div>
    </header>

    <section aria-label="{{ __('Maklumat Tiket') }}" class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Perincian') }}</h2>

            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Kategori') }}</dt>
                    <dd class="mt-1 text-gray-900">{{ $ticket->category?->name ?? __('Tidak dinyatakan') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Keutamaan') }}</dt>
                    <dd class="mt-1 text-gray-900">{{ ucfirst($ticket->priority) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Bahagian') }}</dt>
                    <dd class="mt-1 text-gray-900">{{ $ticket->division?->name ?? $ticket->guest_division ?? __('Tidak dinyatakan') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Pegawai Bertugas') }}</dt>
                    <dd class="mt-1 text-gray-900">{{ $ticket->assignedUser?->name ?? __('Belum ditugaskan') }}</dd>
                </div>
            </dl>

            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Keterangan Aduan') }}</h3>
                <p class="mt-2 whitespace-pre-line text-gray-700">{{ $ticket->description }}</p>
            </div>

            @if ($ticket->attachments->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Lampiran') }}</h3>
                    <ul class="mt-2 space-y-2">
                        @foreach ($ticket->attachments as $attachment)
                            <li class="text-sm text-gray-700">
                                {{ $attachment->file_name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Jejak Masa') }}</h2>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">{{ __('Dicipta') }}</dt>
                    <dd class="text-gray-900">{{ $ticket->created_at?->translatedFormat('d M Y, h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Ditugaskan') }}</dt>
                    <dd class="text-gray-900">{{ $ticket->assigned_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Respon Pertama') }}</dt>
                    <dd class="text-gray-900">{{ $ticket->responded_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Selesai') }}</dt>
                    <dd class="text-gray-900">{{ $ticket->resolved_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Ditutup') }}</dt>
                    <dd class="text-gray-900">{{ $ticket->closed_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </section>

    <section aria-label="{{ __('Komen & Perbincangan') }}" class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Maklum Balas') }}</h2>

            <div class="mt-4 space-y-4">
                @forelse ($ticket->comments as $comment)
                    <article class="rounded-lg border border-gray-200 bg-white p-4">
                        <header class="flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium text-gray-900">{{ $comment->commenter_name ?? $comment->user?->name }}</p>
                                <p class="text-gray-500">{{ $comment->created_at?->diffForHumans() }}</p>
                            </div>
                            @if ($comment->is_internal)
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                    {{ __('Nota Dalaman') }}
                                </span>
                            @endif
                        </header>
                        <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                            {{ $comment->comment }}
                        </p>
                    </article>
                @empty
                    <p class="text-sm text-gray-500">
                        {{ __('Tiada maklum balas buat masa ini.') }}
                    </p>
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Tambah Komen') }}</h2>

            <form wire:submit.prevent="addComment" class="space-y-4">
                <x-form.textarea
                    name="newComment"
                    wire:model.live.debounce.300ms="newComment"
                    rows="5"
                    placeholder="{{ __('Masukkan maklum balas anda di sini...') }}"
                    required
                />

                <x-ui.button type="submit" icon="heroicon-o-chat-bubble-left-ellipsis" :disabled="$addingComment">
                    {{ __('Hantar Maklum Balas') }}
                </x-ui.button>
            </form>
        </x-ui.card>
    </section>
</div>
