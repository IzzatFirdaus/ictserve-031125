@extends('layouts.guest')

@section('title', __('errors.access_blocked'))

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                {{-- Blocked Icon --}}
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-danger-100">
                    <svg class="h-8 w-8 text-danger-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>

                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    {{ __('errors.access_blocked') }}
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    {{ $message ?? __('errors.ip_blocked_message') }}
                </p>

                @if (isset($expiresAt))
                    <p class="mt-4 text-sm text-gray-500">
                        {{ __('errors.block_expires', ['time' => $expiresAt->diffForHumans()]) }}
                    </p>
                @endif

                @if (isset($reason) && config('app.debug'))
                    <p class="mt-2 text-xs text-gray-400">
                        {{ __('errors.reason') }}: {{ $reason }}
                    </p>
                @endif
            </div>

            <div class="mt-8 space-y-4">
                <div class="bg-warning-50 border-l-4 border-warning-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-warning-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-warning-700">
                                {{ __('errors.blocked_help_text') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('contact') }}"
                        class="inline-flex min-h-11 items-center text-primary-600 hover:text-primary-500 text-sm font-medium focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                        {{ __('errors.contact_support') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

