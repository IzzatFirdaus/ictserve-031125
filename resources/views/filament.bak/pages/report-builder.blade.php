<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Report Configuration Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Konfigurasi Laporan</h3>

            <form wire:submit="generatePreview">
                {{ $this->form }}

                <div class="mt-6 flex gap-3">
                    <x-filament::button type="submit" icon="heroicon-o-eye">
                        Jana Pratonton
                    </x-filament::button>

                    @if ($showPreview)
                        <x-filament::button wire:click="exportReport" color="success" icon="heroicon-o-arrow-down-tray">
                            Export Laporan
                        </x-filament::button>

                        <x-filament::button wire:click="clearPreview" color="gray" icon="heroicon-o-x-mark">
                            Kosongkan
                        </x-filament::button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Report Preview --}}
        @if ($showPreview && $reportData)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pratonton Laporan</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Modul: <span class="font-medium">{{ ucfirst($reportData['module']) }}</span> |
                                Jumlah Rekod: <span class="font-medium">{{ $reportData['total_records'] }}</span> |
                                Dijana: <span
                                    class="font-medium">{{ \Carbon\Carbon::parse($reportData['generated_at'])->format('d M Y, H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                @if ($reportData['module'] === 'helpdesk')
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        No. Tiket</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Tajuk</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Keutamaan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Pengadu</th>
                                @elseif($reportData['module'] === 'loans')
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        No. Permohonan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Pemohon</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Tarikh Pinjam</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Aset</th>
                                @elseif($reportData['module'] === 'assets')
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kod Aset</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Keadaan</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($reportData['data']->take(10) as $row)
                                <tr>
                                    @if ($reportData['module'] === 'helpdesk')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['ticket_number'] }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit($row['title'], 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $row['status'])) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ ucfirst($row['priority']) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['submitter'] }}</td>
                                    @elseif($reportData['module'] === 'loans')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['application_number'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['applicant_name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $row['status'])) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['loan_start_date'] }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit($row['assets'], 50) }}</td>
                                    @elseif($reportData['module'] === 'assets')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['asset_code'] }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit($row['name'], 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $row['category'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $row['status'])) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ ucfirst($row['condition']) }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($reportData['total_records'] > 10)
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Menunjukkan 10 daripada {{ $reportData['total_records'] }} rekod. Export untuk melihat
                            semua data.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Empty State --}}
        @if (!$showPreview)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <x-heroicon-o-document-chart-bar class="w-16 h-16 mx-auto text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Pembina Laporan</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Pilih modul dan tetapkan penapis untuk menjana laporan tersuai.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
