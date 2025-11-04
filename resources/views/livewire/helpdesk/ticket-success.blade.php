<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                {{ __('helpdesk.ticket_submitted_successfully') }}
            </h2>

            @if ($ticketNumber)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">{{ __('helpdesk.your_ticket_number') }}</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $ticketNumber }}</p>
                </div>

                <p class="mt-4 text-sm text-gray-600">
                    {{ __('helpdesk.ticket_confirmation_email_sent') }}
                </p>
            @endif

            @if ($canClaim && auth()->check())
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        {{ __('helpdesk.can_claim_ticket_message') }}
                    </p>
                    <a href="{{ route('helpdesk.authenticated.tickets') }}"
                        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        {{ __('helpdesk.view_my_tickets') }}
                    </a>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-8 space-y-3">
                <a href="{{ route('helpdesk.track', ['ticketNumber' => $ticketNumber]) }}"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('helpdesk.track_ticket') }}
                </a>

                <a href="{{ route('helpdesk.create') }}"
                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('helpdesk.submit_another_ticket') }}
                </a>

                <a href="{{ route('welcome') }}"
                    class="w-full flex justify-center py-2 px-4 text-sm font-medium text-gray-600 hover:text-gray-900">
                    {{ __('common.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
</div>
