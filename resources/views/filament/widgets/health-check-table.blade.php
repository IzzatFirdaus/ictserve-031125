<x-filament-widgets::widget>
    <x-filament.components::widget-card title="Integration Health Status" description="Status kesihatan integrasi sistem"
        icon="heroicon-o-heart" color="info" :interactive="false">

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
                    @foreach ($this->getHealth() as $serviceName => $status)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3">{{ ucfirst($serviceName) }}</td>
                            <td class="py-2 px-3">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if ($status['status'] === 'healthy') bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200
                                    @elseif($status['status'] === 'warning') bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200
                                    @else bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200 @endif">
                                    {{ ucfirst($status['status']) }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                {{ isset($status['response_time']) ? number_format($status['response_time'], 2) . 'ms' : 'N/A' }}
                            </td>
                            <td class="py-2 px-3">{{ $status['last_check'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament.components::widget-card>
</x-filament-widgets::widget>
