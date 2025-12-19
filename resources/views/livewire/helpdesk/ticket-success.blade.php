<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-success-100 dark:bg-success-900/30">
                <svg class="h-10 w-10 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('helpdesk.ticket_submitted_successfully') }}
            </h2>

            @if ($ticketNumber)
                <div class="mt-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('helpdesk.your_ticket_number') }}</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $ticketNumber }}</p>
                </div>

                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('helpdesk.ticket_confirmation_email_sent') }}
                </p>
            @endif

            @if ($canClaim && auth()->check())
                <div class="mt-6 p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                    <p class="text-sm text-warning-800 dark:text-warning-200">
                        {{ __('helpdesk.can_claim_ticket_message') }}
                    </p>
                    <a href="{{ route('helpdesk.authenticated.tickets') }}"
                        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-warning-600 hover:bg-warning-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-warning-500 dark:focus-visible:ring-offset-gray-900 min-h-11 min-w-11">
                        {{ __('helpdesk.view_my_tickets') }}
                    </a>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-8 space-y-3">
                <a href="{{ route('helpdesk.track', ['ticketNumber' => $ticketNumber]) }}"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-h-11">
                    {{ __('helpdesk.track_ticket') }}
                </a>

                <a href="{{ route('helpdesk.create') }}"
                    class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-h-11">
                    {{ __('helpdesk.submit_another_ticket') }}
                </a>

                <a href="{{ route('welcome') }}"
                    class="w-full flex justify-center py-2 px-4 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 rounded-lg">
                    {{ __('common.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
</div>

