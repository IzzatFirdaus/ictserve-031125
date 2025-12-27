@extends('layouts.front')

@section('title', __('loan.success.title'))

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                {{-- Success Icon --}}
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-success-100 mb-6">
                    <x-heroicon-o-check class="h-8 w-8 text-success-600" />
                </div>

                {{-- Success Message --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('loan.success.title') }}</h1>
                <p class="text-gray-600 mb-6">{{ __('loan.success.message') }}</p>

                {{-- Application Reference --}}
                @if (request('reference'))
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-500 mb-1">{{ __('loan.success.reference_number') }}</p>
                        <p class="text-xl font-mono font-bold text-primary-600">{{ request('reference') }}</p>
                    </div>
                @endif

                {{-- Next Steps --}}
                <div class="text-left bg-primary-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ __('loan.success.next_steps') }}</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <x-heroicon-s-check-circle class="h-5 w-5 text-primary-500 mr-2 shrink-0" />
                            {{ __('loan.success.step_1') }}
                        </li>
                        <li class="flex items-start">
                            <x-heroicon-s-check-circle class="h-5 w-5 text-primary-500 mr-2 shrink-0" />
                            {{ __('loan.success.step_2') }}
                        </li>
                        <li class="flex items-start">
                            <x-heroicon-s-check-circle class="h-5 w-5 text-primary-500 mr-2 shrink-0" />
                            {{ __('loan.success.step_3') }}
                        </li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('loan.guest.tracking', ['applicationNumber' => request('reference')]) }}"
                        class="inline-flex min-h-11 items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        {{ __('loan.success.track_application') }}
                    </a>
                    <a href="{{ url('/') }}"
                        class="inline-flex min-h-11 items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        {{ __('common.back_to_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
