@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="bg-white py-20 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
            {{ __('Sistem Perkhidmatan ICT') }}
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            {{ __('Platform sehenti untuk aduan ICT dan permohonan aset.') }}
        </p>
    </div>
</section>

<!-- Main Content Cards -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Card 1: Aduan ICT -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-8 flex-1 flex flex-col items-center text-center space-y-6">
                    <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center text-primary-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Aduan ICT') }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            {{ __('Laporkan kerosakan perkakasan, perisian, atau rangkaian untuk tindakan segera.') }}
                        </p>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <a href="{{ route('helpdesk.submit') }}" class="block w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                        {{ __('Buat Aduan') }}
                    </a>
                </div>
            </div>

            <!-- Card 2: Pinjaman Aset -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-8 flex-1 flex flex-col items-center text-center space-y-6">
                    <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center text-primary-600">
                        <!-- Laptop Icon -->
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /> 
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Pinjaman Aset') }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            {{ __('Mohon pinjaman peralatan ICT seperti komputer riba dan projektor.') }}
                        </p>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <a href="{{ route('loan.guest.create') }}" class="block w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                        {{ __('Mohon Sekarang') }}
                    </a>
                </div>
            </div>

            <!-- Card 3: Semak Status -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-8 flex-1 flex flex-col items-center text-center space-y-6">
                    <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center text-primary-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Semak Status') }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            {{ __('Semak status tiket aduan atau permohonan pinjaman anda.') }}
                        </p>
                    </div>
                    <div class="w-full">
                        <label for="ticket_no" class="sr-only">No. Tiket</label>
                        <input type="text" id="ticket_no" name="ticket_no" placeholder="Masukkan No. Tiket" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-600 focus:ring-primary-600 sm:text-sm">
                    </div>
                </div>
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <button type="button" class="block w-full py-3 px-4 bg-white hover:bg-gray-50 text-primary-600 border border-primary-600 text-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                        {{ __('Semak') }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection