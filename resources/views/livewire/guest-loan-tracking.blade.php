<div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded skip-to-content">{{ __('common.skip_to_content') }}</a>
    <main id="main-content" role="main">
    <div class="bg-slate-800 shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-lg leading-6 font-medium text-slate-100">
                {{ __('loan.tracking.title') }}
            </h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-400">
                {{ __('loan.tracking.subtitle') }}
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6 border-t border-slate-700">
            <form wire:submit="track" class="space-y-4" role="search" aria-label="{{ __('loan.tracking.title') }}">
                <div>
                    <label for="applicationNumber" class="block text-sm font-medium text-slate-300">
                        {{ __('loan.fields.application_number') }}
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" wire:model="applicationNumber" id="applicationNumber" 
                            aria-required="true"
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border-slate-600 bg-slate-900 text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:border-indigo-500 sm:text-sm"
                            placeholder="e.g. LA-20231125-0001">
                        <button type="submit" aria-label="{{ __('loan.actions.track') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 min-h-44">
                            {{ __('loan.actions.track') }}
                        </button>
                    </div>
                    @error('applicationNumber') 
                        <p class="mt-2 text-sm text-red-400" role="alert" aria-live="polite">{{ $message }}</p>
                    @enderror
                </div>
            </form>

            @if($searched && $application)
                <div class="mt-8 border-t border-slate-700 pt-8 animate-fadeIn">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="text-lg font-bold text-white">{{ $application->application_number }}</h4>
                            <p class="text-sm text-slate-400">{{ __('loan.fields.submitted_on', ['date' => $application->created_at->format('d M Y')]) }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $application->status->color() }}-100 text-{{ $application->status->color() }}-800">
                            {{ $application->status->label() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h5 class="text-sm font-medium text-slate-400 mb-2">{{ __('loan.fields.applicant') }}</h5>
                            <p class="text-slate-200">{{ $application->applicant_name }}</p>
                            <p class="text-sm text-slate-500">{{ $application->applicant_position }}</p>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-slate-400 mb-2">{{ __('loan.fields.loan_period') }}</h5>
                            <p class="text-slate-200">
                                {{ $application->loan_start_date->format('d M Y') }} - {{ $application->expected_return_date->format('d M Y') }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ $application->loan_start_date->diffInDays($application->expected_return_date) }} {{ __('loan.common.days') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm font-medium text-slate-400 mb-3">{{ __('loan.fields.items') }}</h2>
                        <ul class="divide-y divide-slate-700 rounded-md border border-slate-700 bg-slate-900/50" aria-label="{{ __('loan.fields.items') }}">
                            @foreach($application->loanItems as $item)
                                <li class="px-4 py-3 flex justify-between items-center">
                                    <span class="text-sm text-slate-200">{{ $item->asset_category_name }}</span>
                                    <span class="text-sm font-medium text-slate-400">x{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </main>
</div>
