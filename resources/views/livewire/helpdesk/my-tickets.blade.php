{{--
    My Tickets Component - MyDS Design System
    Trace: D13 §2.2-2.7 (Design Tokens), D14 §4.1 (Color Palette)
    WCAG 2.2 AA Compliant - 4.5:1 text contrast, 3:1 UI contrast, 44px touch targets

    Displays both claimed guest and authenticated submissions with filtering,
    sorting, and search capabilities.

    @trace Requirements 7.2, 1.4
    @wcag WCAG 2.2 AA compliant with proper ARIA labels
    @component Livewire component: App\Livewire\Helpdesk\MyTickets
--}}
<div class="space-y-6">
    {{-- Header --}}
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-heading font-semibold text-gray-100">{{ __('Tiket Saya') }}</h1>
            <p class="text-gray-300">{{ __('Jejaki semua tiket helpdesk yang pernah dihantar.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tickets.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-button transition-colors duration-200 hover:bg-primary-500 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-950 min-h-11">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Cipta Tiket') }}
            </a>
        </div>
    </header>

    {{-- Statistics Summary --}}
    <section aria-label="{{ __('Ringkasan Statistik') }}">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <x-ui.card variant="portal">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-100">{{ $this->ticketStats['total'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Jumlah Tiket') }}</p>
                </div>
            </x-ui.card>
            <x-ui.card variant="portal">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-primary-400">{{ $this->ticketStats['open'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Aktif') }}</p>
                </div>
            </x-ui.card>
            <x-ui.card variant="portal">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-success-400">{{ $this->ticketStats['resolved'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Selesai') }}</p>
                </div>
            </x-ui.card>
            <x-ui.card variant="portal">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-secondary-400">{{ $this->ticketStats['guest'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Tetamu') }}</p>
                </div>
            </x-ui.card>
            <x-ui.card variant="portal">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-warning-400">{{ $this->ticketStats['authenticated'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Disahkan') }}</p>
                </div>
            </x-ui.card>
        </div>
    </section>

    {{-- Filters and Search --}}
    <section aria-label="{{ __('Penapis dan Carian') }}">
        <x-ui.card variant="portal">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <x-form.input name="search" wire:model.live.debounce.300ms="search" label="{{ __('Cari tiket') }}"
                        placeholder="{{ __('Cari nombor tiket, subjek, atau penerangan...') }}"
                        aria-label="{{ __('Cari tiket') }}" hide-label />
                </div>

                {{-- Status Filter --}}
                <x-form.select name="statusFilter" wire:model.live="statusFilter"
                    label="{{ __('Tapis mengikut status') }}" aria-label="{{ __('Tapis mengikut status') }}"
                    hide-label>
                    <option value="all">{{ __('Semua Status') }}</option>
                    <option value="open">{{ __('Aktif') }}</option>
                    <option value="pending">{{ __('Menunggu Saya') }}</option>
                    <option value="resolved">{{ __('Selesai') }}</option>
                    <option value="closed">{{ __('Ditutup') }}</option>
                </x-form.select>

                {{-- Submission Type Filter --}}
                <x-form.select name="submissionTypeFilter" wire:model.live="submissionTypeFilter"
                    label="{{ __('Tapis mengikut jenis penghantaran') }}"
                    aria-label="{{ __('Tapis mengikut jenis penghantaran') }}" hide-label>
                    <option value="all">{{ __('Semua Jenis') }}</option>
                    <option value="guest">{{ __('Tetamu') }}</option>
                    <option value="authenticated">{{ __('Disahkan') }}</option>
                </x-form.select>

                {{-- Category Filter --}}
                <x-form.select name="categoryFilter" wire:model.live="categoryFilter"
                    label="{{ __('Tapis mengikut kategori') }}" aria-label="{{ __('Tapis mengikut kategori') }}"
                    hide-label>
                    <option value="">{{ __('Semua Kategori') }}</option>
                    @foreach ($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- Sort Options --}}
            <div class="mt-4 flex items-center gap-4 border-t border-gray-800 pt-4">
                <span class="text-sm font-medium text-gray-300">{{ __('Susun mengikut:') }}</span>
                <div class="flex items-center gap-2">
                    <x-form.select name="sortBy" wire:model.live="sortBy" class="w-40"
                        label="{{ __('Pilih medan untuk susun') }}" aria-label="{{ __('Pilih medan untuk susun') }}"
                        hide-label>
                        <option value="created_at">{{ __('Tarikh Dicipta') }}</option>
                        <option value="updated_at">{{ __('Tarikh Kemaskini') }}</option>
                        <option value="status">{{ __('Status') }}</option>
                    </x-form.select>

                    <button wire:click="$set('sortDirection', '{{ $sortDirection === 'asc' ? 'desc' : 'asc' }}')"
                        class="inline-flex items-center rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm font-medium text-gray-300 shadow-button transition-colors duration-200 hover:bg-gray-700 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-950 min-h-11"
                        aria-label="{{ $sortDirection === 'asc' ? __('Susun menurun') : __('Susun menaik') }}">
                        @if ($sortDirection === 'asc')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                        @else
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                        @endif
                    </button>
                </div>
            </div>
        </x-ui.card>
    </section>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <x-ui.alert type="success">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session()->has('error'))
        <x-ui.alert type="error">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    {{-- Tickets Table --}}
    <section aria-label="{{ __('Senarai Tiket') }}">
        <x-ui.card variant="portal">
            <div class="overflow-hidden rounded-lg border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Tiket') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Jenis') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Kategori') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Status') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Pegawai') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Kemaskini') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-300">
                                {{ __('Tindakan') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 bg-gray-900/70 backdrop-blur-sm">
                        @forelse ($this->tickets as $ticket)
                            <tr class="transition-colors duration-200 hover:bg-gray-800/50">
                                <td class="px-4 py-4 text-sm text-gray-100">
                                    <div class="font-medium">
                                        <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}"
                                            class="text-primary-400 hover:text-primary-300 focus:outline-none focus:ring-3 focus:ring-primary-500 rounded">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-300">{{ $ticket->subject }}</p>
                                    <p class="mt-1 text-xs text-gray-400">{{ __('Dicipta pada') }}
                                        {{ $ticket->created_at?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($ticket->user_id === null)
                                        <span
                                            class="inline-flex items-center rounded-full bg-secondary-900/30 px-2.5 py-0.5 text-xs font-medium text-secondary-400 border border-secondary-800">
                                            {{ __('Tetamu') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-warning-900/30 px-2.5 py-0.5 text-xs font-medium text-warning-400 border border-warning-800">
                                            {{ __('Disahkan') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-300">
                                    {{ $ticket->category?->name ?? __('Tiada') }}
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full bg-primary-900/30 px-2.5 py-0.5 text-xs font-medium text-primary-400 border border-primary-800">
                                        {{ \Illuminate\Support\Str::headline($ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-300">
                                    {{ $ticket->assignedUser?->name ?? __('Belum Ditugaskan') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-300">
                                    {{ $ticket->updated_at?->diffForHumans() }}
                                </td>
                                <td class="px-4 py-4 text-right text-sm">
                                    @if ($ticket->user_id === null && $ticket->guest_email === auth()->user()->email)
                                        <x-ui.button wire:click="claim({{ $ticket->id }})" size="xs"
                                            variant="secondary"
                                            aria-label="{{ __('Tuntut tiket :number', ['number' => $ticket->ticket_number]) }}">
                                            {{ __('Tuntut') }}
                                        </x-ui.button>
                                    @else
                                        <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}"
                                            class="text-primary-400 hover:text-primary-300 focus:outline-none focus:ring-3 focus:ring-primary-500 rounded"
                                            aria-label="{{ __('Lihat butiran tiket :number', ['number' => $ticket->ticket_number]) }}">
                                            {{ __('Butiran') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-300">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2">{{ __('Tiada tiket untuk paparan.') }}</p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ __('Cuba ubah penapis atau carian anda.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($this->tickets->hasPages())
                <div class="mt-4">
                    {{ $this->tickets->links() }}
                </div>
            @endif
        </x-ui.card>
    </section>
</div>
