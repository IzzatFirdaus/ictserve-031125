<x-filament-widgets::widget>
    <x-filament.components::widget-card title="Slow Database Queries"
        description="Pertanyaan pangkalan data yang perlahan" icon="heroicon-o-clock" color="warning" :interactive="false">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3">Query</th>
                        <th class="text-left py-2 px-3">Execution Time</th>
                        <th class="text-left py-2 px-3">Rows Examined</th>
                        <th class="text-left py-2 px-3">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getQueries() as $query)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3 max-w-md truncate">{{ $query['query'] }}</td>
                            <td class="py-2 px-3">{{ number_format($query['execution_time'], 2) }}s</td>
                            <td class="py-2 px-3">{{ number_format($query['rows_examined']) }}</td>
                            <td class="py-2 px-3">{{ $query['timestamp']->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 px-3 text-center text-gray-500">No slow queries detected</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament.components::widget-card>
</x-filament-widgets::widget>
