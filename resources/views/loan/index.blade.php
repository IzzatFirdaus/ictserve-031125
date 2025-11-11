<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Loan Applications') }}
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
                                   placeholder="Search applications..."
                                   class="flex-1 rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                            <button type="submit" 
                                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                Search
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
                                        @if($application->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($application->status === 'submitted') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($application->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-gray-400">No loan applications found.</p>
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
