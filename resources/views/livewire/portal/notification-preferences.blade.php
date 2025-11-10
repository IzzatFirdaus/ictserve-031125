<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Notification Preferences') }}
            </h2>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                {{ __('Manage your notification preferences for helpdesk tickets and loan applications.') }}
            </p>

            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="email_notifications" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            checked
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="email_notifications" class="font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Email Notifications') }}
                        </label>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ __('Receive email updates about your submissions.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="loan_approval_notifications" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            checked
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="loan_approval_notifications" class="font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Loan Approval Notifications') }}
                        </label>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ __('Get notified when your loan applications are approved or rejected.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="helpdesk_notifications" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            checked
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="helpdesk_notifications" class="font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Helpdesk Ticket Notifications') }}
                        </label>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ __('Receive updates about your helpdesk tickets.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button 
                    type="button" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Save Preferences') }}
                </button>
            </div>
        </div>
    </div>
</div>
