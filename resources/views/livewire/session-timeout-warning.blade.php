<script>
// Define Alpine data IMMEDIATELY before it's used by the component
document.addEventListener('alpine:init', () => {
    Alpine.data('sessionTimeout', () => ({
        // Session timeout: 30 minutes (1800 seconds)
        sessionTimeout: 1800,
        // Warning at 28 minutes (1680 seconds)
        warningThreshold: 1680,
        // Countdown interval
        countdownInterval: null,
        // Time elapsed since last activity
        elapsedSeconds: 0,
        // Remaining seconds before logout
        remainingSeconds: 120,

        init() {
            // Start monitoring session activity
            this.startMonitoring();

            // Listen for session extension event
            Livewire.on('session-extended', () => {
                this.resetTimer();
            });

            // Reset timer on user activity
            ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, () => this.resetTimer(), { passive: true });
            });
        },

        startMonitoring() {
            // Check session status every second
            setInterval(() => {
                this.elapsedSeconds++;

                // Show warning at 28 minutes
                if (this.elapsedSeconds === this.warningThreshold) {
                    @this.call('showWarning');
                    this.startCountdown();
                }

                // Force logout at 30 minutes
                if (this.elapsedSeconds >= this.sessionTimeout) {
                    @this.call('logout');
                }
            }, 1000);
        },

        startCountdown() {
            // Update remaining time every second
            this.countdownInterval = setInterval(() => {
                this.remainingSeconds--;

                if (this.remainingSeconds <= 0) {
                    clearInterval(this.countdownInterval);
                    @this.call('logout');
                }
            }, 1000);
        },

        resetTimer() {
            this.elapsedSeconds = 0;
            this.remainingSeconds = 120;

            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
        },

        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
    }));
});
</script>

<div x-data="sessionTimeout" x-init="init()" wire:ignore.self>
    @if($showWarning)
    <!-- Session Timeout Warning Modal -->
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/75 dark:bg-gray-950/90 transition-opacity" aria-hidden="true"></div>

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <!-- Warning Icon -->
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-900/20 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-warning-600 dark:text-warning-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                            {{ __('auth.session_expiring_title') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('auth.session_expiring_message') }}
                            </p>
                            <p class="mt-2 text-sm font-medium text-warning-700 dark:text-warning-400">
                                {{ __('auth.time_remaining') }}: <span x-text="formatTime(remainingSeconds)" class="tabular-nums"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        wire:click="extendSession"
                        class="inline-flex w-full justify-center rounded-md bg-primary-600 dark:bg-primary-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto transition-colors"
                    >
                        {{ __('auth.extend_session') }}
                    </button>
                    <button
                        type="button"
                        wire:click="logout"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600 sm:mt-0 sm:w-auto transition-colors"
                    >
                        {{ __('auth.logout') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
