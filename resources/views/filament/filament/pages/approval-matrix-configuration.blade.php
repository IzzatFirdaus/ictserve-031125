<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Configuration Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Konfigurasi Peraturan Kelulusan
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Atur peraturan kelulusan berdasarkan nilai aset, gred pemohon, dan tempoh pinjaman.
                </p>
            </div>
            <div class="p-6">
                {{ $this->form }}
            </div>
        </div>

        <!-- Approval Levels Reference -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Tahap Kelulusan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($this->matrixService->getApprovalLevels() as $level => $info)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center mr-3">
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $level }}</span>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $info['name'] }}
                            </h4>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            {{ $info['description'] }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($info['roles'] as $role)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $this->matrixService->getAvailableRoles()[$role] ?? $role }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Test Results -->
        @if(!empty($testResults))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Hasil Ujian Matriks
                </h3>
                <div class="space-y-4">
                    @foreach($testResults as $result)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $result['test_name'] }}
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result['passed'] ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                    {{ $result['passed'] ? 'Lulus' : 'Gagal' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Data Pinjaman:</h5>
                                    <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                                        <li>Nilai: RM {{ number_format($result['loan_data']['total_value']) }}</li>
                                        <li>Gred Pemohon: {{ $result['loan_data']['applicant_grade'] }}</li>
                                        <li>Tempoh: {{ $result['loan_data']['duration_days'] }} hari</li>
                                        <li>Kategori: {{ implode(', ', $result['loan_data']['asset_categories']) }}</li>
                                    </ul>
                                </div>
                                
                                <div>
                                    <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Pelulus Dijumpai ({{ count($result['actual_approvers']) }}):
                                    </h5>
                                    @if(!empty($result['actual_approvers']))
                                        <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                                            @foreach($result['actual_approvers'] as $approver)
                                                <li class="flex items-center">
                                                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                                    {{ $approver['name'] }} ({{ $approver['role'] }})
                                                    <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded">
                                                        L{{ $approver['level'] }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-500 italic">Tiada pelulus dijumpai</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Usage Guidelines -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                <x-heroicon-o-information-circle class="w-5 h-5 inline mr-2" />
                Panduan Penggunaan
            </h3>
            <div class="text-sm text-blue-800 dark:text-blue-200 space-y-3">
                <div>
                    <h4 class="font-medium mb-1">Keutamaan Peraturan:</h4>
                    <p>Peraturan dengan keutamaan lebih rendah (nombor kecil) akan diproses dahulu. Gunakan keutamaan untuk mengawal urutan pemprosesan.</p>
                </div>
                
                <div>
                    <h4 class="font-medium mb-1">Kriteria Aset:</h4>
                    <p>Tetapkan julat nilai aset dan kategori yang sesuai. Kosongkan nilai maksimum untuk tiada had atas.</p>
                </div>
                
                <div>
                    <h4 class="font-medium mb-1">Pelulus:</h4>
                    <p>Boleh tetapkan pelulus berdasarkan peranan, gred, atau individu tertentu. Sekurang-kurangnya satu jenis pelulus mesti ditetapkan.</p>
                </div>
                
                <div>
                    <h4 class="font-medium mb-1">Ujian Matriks:</h4>
                    <p>Gunakan fungsi ujian untuk memastikan peraturan berfungsi dengan betul sebelum menyimpan konfigurasi.</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>