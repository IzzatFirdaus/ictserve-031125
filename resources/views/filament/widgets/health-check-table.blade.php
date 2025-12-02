<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Integration Health Status
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3">Service</th>
                        <th class="text-left py-2 px-3">Status</th>
                        <th class="text-left py-2 px-3">Response Time</th>
                        <th class="text-left py-2 px-3">Last Check</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getHealth() as $serviceName => $status)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3">{{ ucfirst($serviceName) }}</td>
                            <td class="py-2 px-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($status['status'] === 'healthy') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($status['status'] === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst($status['status']) }}
                                </span>
                            </td>
                            <td class="py-2 px-3">{{ isset($status['response_time']) ? number_format($status['response_time'], 2).'ms' : 'N/A' }}</td>
                            <td class="py-2 px-3">{{ $status['last_check'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
