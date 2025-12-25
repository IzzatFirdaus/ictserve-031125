<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Permohonan Pinjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('loans.index') }}" class="mb-6">
                        <div class="flex gap-4">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="{{ __('Cari permohonan...') }}"
                                   class="flex-1 min-h-11 rounded-lg border-gray-300 px-3 py-2 shadow-sm dark:border-gray-700 dark:bg-gray-900 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <button type="submit" 
                                    class="min-h-11 rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                {{ __('Cari') }}
                            </button>
                        </div>
                    </form>

                    <!-- Applications List -->
                    <div class="space-y-4">
                        @forelse ($applications as $application)
                            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-semibold">{{ $application->application_number }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $application->applicant_name }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $application->purpose }}
                                        </p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-medium
                                        @if($application->status === 'approved') bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200
                                        @elseif($application->status === 'submitted') bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200
                                        @elseif($application->status === 'rejected') bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-gray-400">{{ __('Tiada permohonan pinjaman ditemui.') }}</p>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
