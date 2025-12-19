<div class="py-6" wire:poll.60s="refreshData">
    {{-- Skip Links for Accessibility --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-m z-50">
        Langkau ke kandungan utama
    </a>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="main-content">
        {{-- Page Header --}}
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Papan pemuka peribadi anda - Dikemas kini: {{ now()->translatedFormat('d M Y, H:i') }}
            </p>
        </header>

        {{-- Quick Stats Cards --}}
        <section aria-labelledby="stats-heading" class="mb-8">
            <h2 id="stats-heading" class="sr-only">Statistik Ringkas</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Open Tickets --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <x-heroicon-o-ticket class="h-6 w-6 text-primary-600" aria-hidden="true" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Tiket Terbuka
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                            {{ $this->dashboardData['personal']['open_tickets'] ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                        <a href="{{ route('staff.tickets.index') }}"
                            class="text-sm font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            Lihat semua tiket
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Pending Loans --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <x-heroicon-o-document-text class="h-6 w-6 text-success-600" aria-hidden="true" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Pinjaman Tertunda
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                            {{ $this->dashboardData['personal']['pending_loans'] ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                        <a href="{{ route('staff.loans.index') }}"
                            class="text-sm font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            Lihat semua pinjaman
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Active Loans --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <x-heroicon-o-cube class="h-6 w-6 text-info-600" aria-hidden="true" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Pinjaman Aktif
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                            {{ $this->dashboardData['personal']['active_loans'] ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                        <a href="{{ route('staff.loans.active') }}"
                            class="text-sm font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            Lihat pinjaman aktif
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Overdue Items or Pending Approvals --}}
                @if ($this->isApprover && isset($this->dashboardData['quick_stats']['pending_approvals']))
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <x-heroicon-o-check-badge class="h-6 w-6 text-warning-600" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                            Menunggu Kelulusan
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                                {{ $this->dashboardData['quick_stats']['pending_approvals'] }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                            <a href="{{ route('staff.approvals.index') }}"
                                class="text-sm font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                                Lihat kelulusan
                                <span aria-hidden="true"> &rarr;</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l {{ ($this->dashboardData['personal']['overdue_items'] ?? 0) > 0 ? 'ring-2 ring-danger-500' : '' }}">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <x-heroicon-o-exclamation-triangle
                                        class="h-6 w-6 {{ ($this->dashboardData['personal']['overdue_items'] ?? 0) > 0 ? 'text-danger-600' : 'text-gray-400' }}"
                                        aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                            Item Tertunggak
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div
                                                class="text-2xl font-semibold {{ ($this->dashboardData['personal']['overdue_items'] ?? 0) > 0 ? 'text-danger-600' : 'text-gray-900 dark:text-white' }}">
                                                {{ $this->dashboardData['personal']['overdue_items'] ?? 0 }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ ($this->dashboardData['personal']['overdue_items'] ?? 0) > 0 ? 'Sila kembalikan segera' : 'Tiada item tertunggak' }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Pending Actions --}}
            <section aria-labelledby="actions-heading"
                class="bg-white dark:bg-gray-800 shadow-card rounded-l overflow-hidden">
                <div class="p-6">
                    <h2 id="actions-heading" class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Tindakan Diperlukan
                    </h2>
                    @if (empty($this->dashboardData['pending_actions']))
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="mx-auto h-12 w-12 text-success-400" aria-hidden="true" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Tiada tindakan diperlukan buat masa ini
                            </p>
                        </div>
                    @else
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->dashboardData['pending_actions'] as $action)
                                <li class="py-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="shrink-0">
                                            @if ($action['priority'] === 'critical')
                                                <span
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-danger-50 dark:bg-danger-900">
                                                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-danger-600"
                                                        aria-hidden="true" />
                                                </span>
                                            @elseif($action['priority'] === 'high')
                                                <span
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-warning-50 dark:bg-warning-900">
                                                    <x-heroicon-s-clock class="h-5 w-5 text-warning-600"
                                                        aria-hidden="true" />
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-info-50 dark:bg-info-900">
                                                    <x-heroicon-s-information-circle class="h-5 w-5 text-info-600"
                                                        aria-hidden="true" />
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $action['title'] }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $action['description'] }}
                                            </p>
                                        </div>
                                        <div class="shrink-0">
                                            <a href="{{ $action['url'] }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-m text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 min-h-11 min-w-11">
                                                Lihat
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

            {{-- Recent Activity --}}
            <section aria-labelledby="activity-heading"
                class="bg-white dark:bg-gray-800 shadow-card rounded-l overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 id="activity-heading" class="text-lg font-medium text-gray-900 dark:text-white">
                            Aktiviti Terkini
                        </h2>
                        <div class="flex space-x-2" role="group" aria-label="Penapis aktiviti">
                            @foreach ($filterOptions as $key => $label)
                                <button wire:click="setActivityFilter('{{ $key }}')" type="button"
                                    class="px-3 py-1.5 text-xs font-medium rounded-m min-h-11 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 {{ $activityFilter === $key ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}"
                                    aria-pressed="{{ $activityFilter === $key ? 'true' : 'false' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if (empty($this->filteredActivity))
                        <div class="text-center py-8">
                            <x-heroicon-o-clock class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Tiada aktiviti terkini
                            </p>
                        </div>
                    @else
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->filteredActivity as $activity)
                                <li class="py-3">
                                    <a href="{{ $activity['url'] }}"
                                        class="flex items-center space-x-4 hover:bg-gray-50 dark:hover:bg-gray-700 -mx-2 px-2 py-2 rounded-m focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <div class="shrink-0">
                                            @if ($activity['type'] === 'ticket')
                                                <x-heroicon-o-ticket class="h-5 w-5 text-primary-500"
                                                    aria-hidden="true" />
                                            @else
                                                <x-heroicon-o-document-text class="h-5 w-5 text-success-500"
                                                    aria-hidden="true" />
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $activity['reference'] }} - {{ $activity['title'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <x-data.status-badge :status="$activity['status']" size="sm" />
                                                <span
                                                    class="ml-2">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                        <x-heroicon-s-chevron-right class="h-5 w-5 text-gray-400"
                                            aria-hidden="true" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        </div>

        {{-- Quick Actions --}}
        <section aria-labelledby="quick-actions-heading" class="mt-6">
            <h2 id="quick-actions-heading" class="sr-only">Tindakan Pantas</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('helpdesk.create') }}"
                    class="relative group bg-white dark:bg-gray-800 p-6 shadow-card rounded-l hover:shadow-lg transition-shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <div>
                        <span
                            class="rounded-l inline-flex p-3 bg-primary-50 text-primary-700 dark:bg-primary-900 dark:text-primary-300 ring-4 ring-white dark:ring-gray-800">
                            <x-heroicon-o-plus-circle class="h-6 w-6" aria-hidden="true" />
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Hantar Tiket Baru
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Laporkan isu teknikal atau minta sokongan ICT
                        </p>
                    </div>
                    <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
                        aria-hidden="true">
                        <x-heroicon-s-arrow-right class="h-6 w-6" />
                    </span>
                </a>

                <a href="{{ route('loans.create') }}"
                    class="relative group bg-white dark:bg-gray-800 p-6 shadow-card rounded-l hover:shadow-lg transition-shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <div>
                        <span
                            class="rounded-l inline-flex p-3 bg-success-50 text-success-700 dark:bg-success-900 dark:text-success-300 ring-4 ring-white dark:ring-gray-800">
                            <x-heroicon-o-document-plus class="h-6 w-6" aria-hidden="true" />
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Mohon Pinjaman Aset
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Pinjam peralatan ICT untuk kegunaan rasmi
                        </p>
                    </div>
                    <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
                        aria-hidden="true">
                        <x-heroicon-s-arrow-right class="h-6 w-6" />
                    </span>
                </a>

                <a href="{{ route('status.check') }}"
                    class="relative group bg-white dark:bg-gray-800 p-6 shadow-card rounded-l hover:shadow-lg transition-shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <div>
                        <span
                            class="rounded-l inline-flex p-3 bg-info-50 text-info-700 dark:bg-info-900 dark:text-info-300 ring-4 ring-white dark:ring-gray-800">
                            <x-heroicon-o-magnifying-glass class="h-6 w-6" aria-hidden="true" />
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Semak Status
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Semak status tiket atau permohonan pinjaman
                        </p>
                    </div>
                    <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
                        aria-hidden="true">
                        <x-heroicon-s-arrow-right class="h-6 w-6" />
                    </span>
                </a>

                <a href="{{ route('staff.profile') }}"
                    class="relative group bg-white dark:bg-gray-800 p-6 shadow-card rounded-l hover:shadow-lg transition-shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <div>
                        <span
                            class="rounded-l inline-flex p-3 bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-300 ring-4 ring-white dark:ring-gray-800">
                            <x-heroicon-o-user-circle class="h-6 w-6" aria-hidden="true" />
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Profil Saya
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Urus maklumat profil dan tetapan akaun
                        </p>
                    </div>
                    <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
                        aria-hidden="true">
                        <x-heroicon-s-arrow-right class="h-6 w-6" />
                    </span>
                </a>
            </div>
        </section>
    </div>

    {{-- ARIA Live Region for Real-time Updates --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="dashboard-announcements">
        <span wire:loading wire:target="refreshData">Mengemas kini papan pemuka...</span>
    </div>
</div>
