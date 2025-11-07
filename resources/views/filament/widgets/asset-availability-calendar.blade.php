<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Asset Availability Calendar
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex gap-4">
                {{ $this->form }}
            </div>
        </x-slot>

        <div class="space-y-4">
            {{-- Legend --}}
            <div class="flex flex-wrap gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Legend:</span>
                @foreach($legend as $item)
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded" style="background-color: {{ $item['color'] }}"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Calendar Container --}}
            <div id="asset-calendar" class="min-h-[600px]"></div>
        </div>
    </x-filament::section>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('asset-calendar');
            const events = @json($events);
            const viewMode = @json($viewMode);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: viewMode === 'week' ? 'timeGridWeek' : 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: events,
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                eventContent: function(arg) {
                    return {
                        html: `<div class="fc-event-title fc-sticky" style="padding: 2px 4px; font-size: 0.875rem;">
                            ${arg.event.title}
                        </div>`
                    };
                },
                height: 'auto',
                aspectRatio: 1.8,
            });

            calendar.render();

            // Listen for Livewire updates
            Livewire.on('refreshCalendar', () => {
                calendar.refetchEvents();
            });
        });
    </script>
    @endpush
</x-filament-widgets::widget>
