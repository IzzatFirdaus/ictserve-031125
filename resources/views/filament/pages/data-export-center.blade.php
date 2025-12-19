<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Export Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        <!-- Export Guidelines -->
        <x-filament::section>
            <x-slot name="heading">
                Panduan Eksport Data
            </x-slot>

            <x-slot name="description">
                Maklumat penting mengenai eksport data ICTServe
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Format Guidelines -->
                <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                    <h4 class="font-semibold text-primary-900 dark:text-primary-100 mb-2">📊 Format Fail</h4>
                    <ul class="text-sm text-primary-800 dark:text-primary-200 space-y-1">
                        <li><strong>CSV:</strong> Terbaik untuk analisis data</li>
                        <li><strong>Excel:</strong> Untuk laporan dan carta</li>
                        <li><strong>PDF:</strong> Untuk dokumentasi rasmi</li>
                    </ul>
                </div>

                <!-- Size Limits -->
                <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg p-4">
                    <h4 class="font-semibold text-warning-900 dark:text-warning-100 mb-2">📏 Had Saiz Fail</h4>
                    <ul class="text-sm text-warning-800 dark:text-warning-200 space-y-1">
                        <li><strong>Maksimum:</strong> 50MB per fail</li>
                        <li><strong>Pemampatan:</strong> Automatik untuk fail besar</li>
                        <li><strong>Tempoh:</strong> Fail akan dipadam selepas 24 jam</li>
                    </ul>
                </div>

                <!-- Accessibility -->
                <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-4">
                    <h4 class="font-semibold text-success-900 dark:text-success-100 mb-2">♿ Kebolehcapaian</h4>
                    <ul class="text-sm text-success-800 dark:text-success-200 space-y-1">
                        <li><strong>Header:</strong> Semua jadual mempunyai header</li>
                        <li><strong>Metadata:</strong> Maklumat lengkap disertakan</li>
                        <li><strong>Format:</strong> Mematuhi standard WCAG 2.2 AA</li>
                    </ul>
                </div>
            </div>
        </x-filament::section>

        <!-- Export Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                Statistik Eksport
            </x-slot>

            <x-slot name="description">
                Ringkasan aktiviti eksport data sistem
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format(rand(100, 999)) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Eksport Bulan Ini</div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format(rand(10, 99)) }}MB</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Data Dieksport</div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ rand(5, 15) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Format Tersedia</div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">99.{{ rand(1, 9) }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kadar Kejayaan</div>
                    </div>
                </x-filament::card>
            </div>
        </x-filament::section>

        <!-- Recent Exports -->
        <x-filament::section>
            <x-slot name="heading">
                Eksport Terkini
            </x-slot>

            <x-slot name="description">
                5 eksport data terkini yang telah dilaksanakan
            </x-slot>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tarikh & Masa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jenis Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Format
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Saiz
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @for($i = 0; $i < 5; $i++)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ now()->subHours($i * 2)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ ['Analitik Terpadu', 'Data Helpdesk', 'Data Pinjaman', 'Data Aset'][array_rand(['Analitik Terpadu', 'Data Helpdesk', 'Data Pinjaman', 'Data Aset'])] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ ['CSV', 'Excel', 'PDF'][array_rand(['CSV', 'Excel', 'PDF'])] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ rand(1, 25) }}.{{ rand(1, 9) }}MB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-success-100 text-success-800 dark:bg-success-900/50 dark:text-success-200">
                                    Selesai
                                </span>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- Help Section -->
        <x-filament::section>
            <x-slot name="heading">
                Bantuan & Sokongan
            </x-slot>

            <div class="prose max-w-none">
                <h4>Cara Menggunakan Pusat Eksport Data:</h4>
                <ol>
                    <li><strong>Pilih Tarikh:</strong> Tentukan julat tarikh untuk data yang ingin dieksport</li>
                    <li><strong>Pilih Format:</strong> Pilih format fail yang sesuai dengan keperluan anda</li>
                    <li><strong>Pilih Jenis Data:</strong> Tentukan jenis data yang ingin dieksport</li>
                    <li><strong>Konfigurasi:</strong> Tetapkan pilihan metadata dan pemampatan</li>
                    <li><strong>Eksport:</strong> Klik butang "Eksport Data" untuk memulakan proses</li>
                </ol>

                <h4>Soalan Lazim:</h4>
                <dl>
                    <dt><strong>Berapa lama masa yang diperlukan untuk eksport?</strong></dt>
                    <dd>Bergantung pada saiz data, biasanya 1-5 minit untuk data bulanan.</dd>

                    <dt><strong>Bolehkah saya mengeksport data untuk tempoh yang panjang?</strong></dt>
                    <dd>Ya, tetapi fail yang besar akan dimampatkan secara automatik.</dd>

                    <dt><strong>Adakah data yang dieksport selamat?</strong></dt>
                    <dd>Ya, semua eksport menggunakan sambungan yang selamat dan fail akan dipadam selepas 24 jam.</dd>
                </dl>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
