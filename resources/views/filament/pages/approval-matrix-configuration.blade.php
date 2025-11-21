<x-filament-panels::page>
    {{ $this->form }}
    
    @if(!empty($testResults))
        <div class="mt-6 space-y-4">
            <h3 class="text-lg font-semibold">Hasil Ujian Matriks</h3>
            @foreach($testResults as $result)
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium">{{ $result['test_name'] }}</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result['passed'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $result['passed'] ? 'Lulus' : 'Gagal' }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Nilai: RM {{ number_format($result['loan_data']['total_value']) }} | 
                        Gred: {{ $result['loan_data']['applicant_grade'] }} | 
                        Pelulus: {{ count($result['actual_approvers']) }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>