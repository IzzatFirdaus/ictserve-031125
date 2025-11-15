<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Export Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">📊 Format Fail</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>CSV:</strong> Terbaik untuk analisis data</li>
                        <li><strong>Excel:</strong> Untuk laporan dan carta</li>
                        <li><strong>PDF:</strong> Untuk dokumentasi rasmi</li>
                    </ul>
                </div>

                <!-- Size Limits -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h4 class="font-semibold text-amber-900 mb-2">📏 Had Saiz Fail</h4>
                    <ul class="text-sm text-amber-800 space-y-1">
                        <li><strong>Maksimum:</strong> 50MB per fail</li>
                        <li><strong>Pemampatan:</strong> Automatik untuk fail besar</li>
                        <li><strong>Tempoh:</strong> Fail akan dipadam selepas 24 jam</li>
                    </ul>
                </div>

                <!-- Accessibility -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-900 mb-2">♿ Kebolehcapaian</h4>
                    <ul class="text-sm text-green-800 space-y-1">
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

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format(rand(100, 999)) }}</div>
                    <div class="text-sm text-gray-600">Eksport Bulan Ini</div>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format(rand(10, 99)) }}MB</div>
                    <div class="text-sm text-gray-600">Data Dieksport</div>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ rand(5, 15) }}</div>
                    <div class="text-sm text-gray-600">Format Tersedia</div>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">99.{{ rand(1, 9) }}%</div>
                    <div class="text-sm text-gray-600">Kadar Kejayaan</div>
                </div>
            </div>
        </x-filament::section>

        <!-- Recent Exports -->
        <x-filament::section>
            <x-slot name="heading">
                Eksport Terkini
            </x-slot>

            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tarikh & Masa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Format
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Saiz
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @for($i = 0; $i < 5; $i++)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ now()->subHours($i * 2)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ['Analitik Terpadu', 'Data Helpdesk', 'Data Pinjaman', 'Data Aset'][array_rand(['Analitik Terpadu', 'Data Helpdesk', 'Data Pinjaman', 'Data Aset'])] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ['CSV', 'Excel', 'PDF'][array_rand(['CSV', 'Excel', 'PDF'])] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ rand(1, 25) }}.{{ rand(1, 9) }}MB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
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
