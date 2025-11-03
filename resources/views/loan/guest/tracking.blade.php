@extends('layouts.guest')

@section('content')
    <div class="bg-gray-50 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <header>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ __('loans.tracking_title') }}
                </h1>
                <p class="mt-2 text-gray-600">
                    {{ __('loans.tracking_subtitle') }}
                </p>
            </header>

            <section class="bg-white shadow-lg rounded-lg p-6 space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ __('loans.application_details') }}
                    </h2>

                    <dl class="mt-4 grid gap-6 sm:grid-cols-2 text-sm text-gray-700">
                        <div>
                            <dt class="font-medium text-gray-600">{{ __('loans.application_number') }}</dt>
                            <dd class="mt-1 text-gray-900">{{ $application->application_number }}</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-600">{{ __('common.status') }}</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ $application->status?->label() ?? ucfirst($application->status) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-600">{{ __('loans.full_name') }}</dt>
                            <dd class="mt-1 text-gray-900">{{ $application->applicant_name }}</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-600">{{ __('loans.email_address') }}</dt>
                            <dd class="mt-1 text-gray-900">{{ $application->applicant_email }}</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-600">{{ __('loans.phone_number') }}</dt>
                            <dd class="mt-1 text-gray-900">{{ $application->applicant_phone }}</dd>
                        </div>

                        <div>
                            <dt class="font-medium text-gray-600">{{ __('loans.loan_period') }}</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ $application->loan_start_date?->translatedFormat('d M Y') }}
                                &ndash;
                                {{ $application->loan_end_date?->translatedFormat('d M Y') }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-600">{{ __('loans.purpose') }}</dt>
                            <dd class="mt-1 text-gray-900">{{ $application->purpose }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('loans.loan_items_heading') }}
                </h2>

                @if ($application->loanItems->isEmpty())
                    <p class="mt-4 text-sm text-gray-600">
                        {{ __('loans.no_loan_items') }}
                    </p>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach ($application->loanItems as $item)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $item->asset?->name ?? __('loans.asset') }}
                                </h3>

                                <dl class="mt-2 grid gap-4 sm:grid-cols-2 text-sm text-gray-700">
                                    <div>
                                        <dt class="font-medium text-gray-600">{{ __('loans.asset') }}</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $item->asset?->name ?? __('common.not_specified') }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="font-medium text-gray-600">{{ __('common.status') }}</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $application->status?->label() ?? ucfirst($application->status) }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="font-medium text-gray-600">{{ __('asset_loan.condition_before') }}</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $item->condition_before?->label() ?? __('common.not_specified') }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="font-medium text-gray-600">{{ __('asset_loan.condition_after') }}</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $item->condition_after?->label() ?? __('common.not_specified') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
