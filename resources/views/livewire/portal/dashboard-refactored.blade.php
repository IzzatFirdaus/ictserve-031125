<?php

use Livewire\Volt\Component;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;
use function Livewire\Volt\{state, computed};

new class extends Component {
    public function mount(): void
    {
        // Dashboard initialization
    }

    #[Computed]
    public function recentTickets(): Collection
    {
        return Ticket::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function ticketStats(): array
    {
        return [
            'open' => Ticket::where('user_id', auth()->id())->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', auth()->id())->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('user_id', auth()->id())->where('status', 'resolved')->count(),
        ];
    }
};

?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8 theme-transition">
    <!-- Main Content -->
    <main id="main-content" class="max-w-7xl mx-auto" tabindex="-1">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-heading font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('dashboard.subtitle') }}
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-8">
            <!-- Open Tickets Card -->
            <div class="bg-white dark:bg-gray-800 rounded-l shadow-card p-6 theme-transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('dashboard.open_tickets') }}
                        </p>
                        <p class="mt-2 text-3xl font-heading font-semibold text-gray-900 dark:text-white">
                            {{ $this->ticketStats['open'] }}
                        </p>
                    </div>
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-m bg-blue-100 dark:bg-blue-900">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6 text-blue-600 dark:text-blue-300" aria-hidden="true" />
                    </div>
                </div>
            </div>

            <!-- In Progress Card -->
            <div class="bg-white dark:bg-gray-800 rounded-l shadow-card p-6 theme-transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('dashboard.in_progress') }}
                        </p>
                        <p class="mt-2 text-3xl font-heading font-semibold text-gray-900 dark:text-white">
                            {{ $this->ticketStats['in_progress'] }}
                        </p>
                    </div>
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-m bg-yellow-100 dark:bg-yellow-900">
                        <x-heroicon-o-arrow-path class="w-6 h-6 text-yellow-600 dark:text-yellow-300" aria-hidden="true" />
                    </div>
                </div>
            </div>

            <!-- Resolved Card -->
            <div class="bg-white dark:bg-gray-800 rounded-l shadow-card p-6 theme-transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('dashboard.resolved') }}
                        </p>
                        <p class="mt-2 text-3xl font-heading font-semibold text-gray-900 dark:text-white">
                            {{ $this->ticketStats['resolved'] }}
                        </p>
                    </div>
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-m bg-green-100 dark:bg-green-900">
                        <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-300" aria-hidden="true" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets Section -->
        <div class="bg-white dark:bg-gray-800 rounded-l shadow-card p-6 theme-transition">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-heading font-semibold text-gray-900 dark:text-white">
                    {{ __('dashboard.recent_tickets') }}
                </h2>
                <a href="{{ route('tickets.index') }}"
                   class="btn-primary min-h-11 px-4 py-2 rounded-m shadow-button
                          bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                          focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                          transition-colors duration-200 inline-flex items-center justify-center"
                   wire:navigate>
                    {{ __('dashboard.view_all') }}
                </a>
            </div>

            @if($this->recentTickets->isEmpty())
                <div class="text-center py-12">
                    <x-heroicon-o-document-text class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" aria-hidden="true" />
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ __('dashboard.no_tickets') }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ __('dashboard.ticket_no') }}
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ __('dashboard.subject') }}
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ __('dashboard.status') }}
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ __('dashboard.date') }}
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    <span class="sr-only">{{ __('dashboard.actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->recentTickets as $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        #{{ $ticket->ticket_number }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-300">
                                        {{ $ticket->subject }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': '{{ $ticket->status }}' === 'open',
                                                  'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200': '{{ $ticket->status }}' === 'in_progress',
                                                  'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': '{{ $ticket->status }}' === 'resolved',
                                              }">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">
                                        {{ $ticket->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                           class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300
                                                  focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 py-1"
                                           wire:navigate>
                                            {{ __('dashboard.view') }}
                                            <span class="sr-only">{{ $ticket->subject }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Action Buttons Section -->
        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('tickets.create') }}"
               class="btn-primary min-h-11 px-6 py-3 rounded-m shadow-button
                      bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                      focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                      transition-colors duration-200 inline-flex items-center justify-center font-medium"
               wire:navigate>
                <x-heroicon-o-plus class="w-5 h-5 mr-2" aria-hidden="true" />
                {{ __('dashboard.create_ticket') }}
            </a>

            <a href="{{ route('profile.show') }}"
               class="btn-secondary min-h-11 px-6 py-3 rounded-m shadow-button
                      bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white
                      hover:bg-gray-300 dark:hover:bg-gray-600
                      focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:outline-none
                      transition-colors duration-200 inline-flex items-center justify-center font-medium"
               wire:navigate>
                <x-heroicon-o-user class="w-5 h-5 mr-2" aria-hidden="true" />
                {{ __('dashboard.view_profile') }}
            </a>
        </div>
    </main>
</div>
