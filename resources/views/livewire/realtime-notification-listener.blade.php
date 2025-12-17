{{--
/**
 * Real-time Notification Listener View
 *
 * Invisible component that handles WebSocket events and dispatches
 * browser events for UI updates. Include in layouts for global
 * real-time notification support.
 *
 * Features:
 * - Connection status indicator (optional)
 * - ARIA live region integration
 * - Toast notification dispatching
 * - Custom event broadcasting
 *
 * @see .kiro/specs/frontend-comprehensive-v3.6/requirements.md - Requirements 10.1, 10.3, 10.4
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 *
 * @trace D03 SRS-FR-008; D04 §5.3
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages)
 *
 * @version 1.0.0
 * @updated 2025-12-14
 */
--}}

<div x-data="{
    connected: $wire.entangle('connected'),
    init() {
        // Listen for Echo connection events
        window.addEventListener('echo:connected', () => {
            this.connected = true;
            $wire.handleConnected();
        });

        window.addEventListener('echo:disconnected', () => {
            this.connected = false;
            $wire.handleDisconnected();
        });

        // Check initial connection state
        if (window.echoConnectionState && window.echoConnectionState.connected) {
            this.connected = true;
        }
    }
}" class="hidden" aria-hidden="true">

    {{-- Connection status for debugging (hidden by default) --}}
    @if (config('app.debug'))
        <div x-show="false" class="fixed bottom-4 left-4 z-50">
            <div x-show="connected"
                class="flex items-center gap-2 px-3 py-1.5 bg-success-100 dark:bg-success-900/50 text-success-800 dark:text-success-200 rounded-full text-xs font-medium">
                <span class="w-2 h-2 bg-success-500 rounded-full animate-pulse"></span>
                {{ __('common.connected') }}
            </div>
            <div x-show="!connected"
                class="flex items-center gap-2 px-3 py-1.5 bg-warning-100 dark:bg-warning-900/50 text-warning-800 dark:text-warning-200 rounded-full text-xs font-medium">
                <span class="w-2 h-2 bg-warning-500 rounded-full"></span>
                {{ __('common.disconnected') }}
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Handle toast notifications from Livewire
            Livewire.on('toast', (data) => {
                if (typeof window.showToast === 'function') {
                    window.showToast(data.message, data.type || 'info');
                } else {
                    // Fallback: dispatch custom event for toast component
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.message,
                            type: data.type || 'info'
                        }
                    }));
                }
            });

            // Handle ARIA announcements from Livewire
            Livewire.on('announce', (data) => {
                window.dispatchEvent(new CustomEvent('announce', {
                    detail: {
                        message: data.message,
                        priority: data.priority || 'polite'
                    }
                }));
            });

            // Handle notification refresh
            Livewire.on('refresh-notifications', () => {
                // Dispatch to notification bell component
                Livewire.dispatch('refresh-notifications');
            });
        });
    </script>
@endpush
