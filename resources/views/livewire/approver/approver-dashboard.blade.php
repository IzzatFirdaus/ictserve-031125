<div class="space-y-6">
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100">{{ __('Dashboard Kelulusan') }}</h1>
            <p class="text-slate-300">{{ __('Uruskan permohonan pinjaman yang memerlukan kelulusan anda.') }}</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-form.select
                name="status"
                wire:model.live="status"
                class="sm:w-48"
            >
                <option value="pending">{{ __('Menunggu Kelulusan') }}</option>
                <option value="history">{{ __('Sejarah Kelulusan') }}</option>
            </x-form.select>

            <x-form.input
                name="search"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Cari pemohon, no. permohonan...') }}"
                class="sm:w-64"
            />
        </div>
    </header>

    @if (session()->has('message'))
        <x-ui.alert type="success" class="mb-4">
            {{ session('message') }}
        </x-ui.alert>
    @endif

    <div class="overflow-hidden rounded-lg border border-slate-800">
        <table role="table" class="min-w-full divide-y divide-slate-800">
            <thead class="bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Permohonan') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Pemohon') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Tarikh Pinjaman') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Tindakan') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 bg-slate-900/70 backdrop-blur-sm">
                @forelse ($applications as $application)
                    <tr>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <div class="font-medium">
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="text-primary-400 hover:text-primary-300">
                                    {{ $application->application_number }}
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-slate-300">
                                {{ $application->purpose }}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <p class="font-medium">{{ $application->applicant_name }}</p>
                            <p class="text-xs text-slate-400">{{ $application->division?->name }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <p>{{ $application->loan_start_date?->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-slate-300">
                                {{ __('Hingga') }} {{ $application->loan_end_date?->translatedFormat('d M Y') }}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-slate-100">
                            @if ($status === 'pending')
                                <div class="flex justify-end gap-2">
                                    <x-ui.button
                                        size="sm"
                                        variant="success"
                                        wire:click="openApprovalModal({{ $application->id }})"
                                    >
                                        {{ __('Lulus') }}
                                    </x-ui.button>
                                    <x-ui.button
                                        size="sm"
                                        variant="danger"
                                        wire:click="openRejectionModal({{ $application->id }})"
                                    >
                                        {{ __('Tolak') }}
                                    </x-ui.button>
                                </div>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $application->status === \App\Enums\LoanStatus::APPROVED ? 'bg-success-900/30 text-success-400 border border-success-800' : 'bg-danger-900/30 text-danger-400 border border-danger-800' }}">
                                    {{ $application->status->label() }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-300">
                            {{ __('Tiada permohonan ditemui.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $applications->links() }}
    </div>

    {{-- Approval Modal --}}
    @if($showApprovalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-lg bg-slate-900 border border-slate-700 p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-100 mb-4">{{ __('Sahkan Kelulusan') }}</h3>
                <p class="text-slate-300 mb-4">
                    {{ __('Adakah anda pasti mahu meluluskan permohonan') }}
                    <span class="font-bold">{{ $selectedApplication?->application_number }}</span>?
                </p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('Catatan (Pilihan)') }}</label>
                    <textarea
                        wire:model="remarks"
                        class="w-full min-h-11 rounded-lg bg-slate-800 border-slate-700 text-slate-100 focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        rows="3"
                    ></textarea>
                    @error('remarks') <span class="text-xs text-danger-400">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <x-ui.button variant="secondary" wire:click="$set('showApprovalModal', false)">
                        {{ __('Batal') }}
                    </x-ui.button>
                    <x-ui.button variant="success" wire:click="approve">
                        {{ __('Sahkan Lulus') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    @endif

    {{-- Rejection Modal --}}
    @if($showRejectionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-lg bg-slate-900 border border-slate-700 p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-100 mb-4">{{ __('Sahkan Penolakan') }}</h3>
                <p class="text-slate-300 mb-4">
                    {{ __('Adakah anda pasti mahu menolak permohonan') }}
                    <span class="font-bold">{{ $selectedApplication?->application_number }}</span>?
                </p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('Sebab Penolakan (Wajib)') }}</label>
                    <textarea
                        wire:model="remarks"
                        class="w-full min-h-11 rounded-lg bg-slate-800 border-slate-700 text-slate-100 focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        rows="3"
                        required aria-required="true"
                    ></textarea>
                    @error('remarks') <span class="text-xs text-danger-400">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <x-ui.button variant="secondary" wire:click="$set('showRejectionModal', false)">
                        {{ __('Batal') }}
                    </x-ui.button>
                    <x-ui.button variant="danger" wire:click="reject">
                        {{ __('Sahkan Tolak') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
