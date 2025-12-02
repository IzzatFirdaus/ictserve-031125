@extends('layouts.front')

@section('title', __('loan.success.title'))

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            {{-- Success Icon --}}
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
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
            <div class="text-left bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ __('loan.success.next_steps') }}</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-blue-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('loan.success.step_1') }}
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-blue-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('loan.success.step_2') }}
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-blue-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('loan.success.step_3') }}
                    </li>
                </ul>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('loan.guest.tracking', ['applicationNumber' => request('reference')]) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    {{ __('loan.success.track_application') }}
                </a>
                <a href="{{ url('/') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    {{ __('common.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
