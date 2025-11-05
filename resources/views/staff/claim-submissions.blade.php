{{--
/**
 * View: Claim Guest Submissions
 * Description: Allows authenticated staff to link guest helpdesk tickets or loan applications to their account.
 */
--}}

<x-portal-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <header class="space-y-2">
            <h1 class="text-3xl font-semibold text-slate-100">
                {{ __('staff.claims.title') }}
            </h1>
            <p class="text-sm text-slate-400">
                {{ __('staff.claims.subtitle') }}
            </p>
        </header>

        @if (session('success'))
            <x-ui.alert type="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert type="error" dismissible>
                <ul class="space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
            <form method="POST" action="{{ route('staff.claim-submission') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="claim-email" class="text-sm font-medium text-slate-200">
                        {{ __('staff.claims.email_label') }}
                    </label>
                    <input
                        id="claim-email"
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        class="w-full rounded-md border border-slate-700 bg-slate-950/80 px-3 py-2 text-slate-100 placeholder:text-slate-500 focus:border-motac-blue focus:outline-none focus:ring-2 focus:ring-motac-blue/70"
                    >
                    <p class="text-xs text-slate-400">
                        {{ __('staff.claims.email_help') }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="claim-type" class="text-sm font-medium text-slate-200">
                        {{ __('staff.claims.type_label') }}
                    </label>
                    <select
                        id="claim-type"
                        name="submission_type"
                        required
                        class="w-full rounded-md border border-slate-700 bg-slate-950/80 px-3 py-2 text-slate-100 focus:border-motac-blue focus:outline-none focus:ring-2 focus:ring-motac-blue/70"
                    >
                        <option value="ticket" @selected(old('submission_type') === 'ticket')>
                            {{ __('staff.claims.type_ticket') }}
                        </option>
                        <option value="loan" @selected(old('submission_type') === 'loan')>
                            {{ __('staff.claims.type_loan') }}
                        </option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="claim-reference" class="text-sm font-medium text-slate-200">
                        {{ __('staff.claims.id_label') }}
                    </label>
                    <input
                        id="claim-reference"
                        type="number"
                        name="submission_id"
                        required
                        value="{{ old('submission_id') }}"
                        class="w-full rounded-md border border-slate-700 bg-slate-950/80 px-3 py-2 text-slate-100 placeholder:text-slate-500 focus:border-motac-blue focus:outline-none focus:ring-2 focus:ring-motac-blue/70"
                    >
                    <p class="text-xs text-slate-400">
                        {{ __('staff.claims.id_help') }}
                    </p>
                </div>

                <div class="flex items-center justify-end">
                    <x-ui.button type="submit" variant="primary">
                        {{ __('staff.claims.submit_button') }}
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card class="border border-dashed border-slate-800 bg-slate-900/60 backdrop-blur-sm">
            <h2 class="text-sm font-semibold text-slate-200">
                {{ __('staff.claims.info_heading') }}
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                {{ __('staff.claims.info_description') }}
            </p>

            <ul class="mt-4 space-y-2 text-sm text-slate-300">
                <li>- {{ __('staff.claims.info_step_ticket') }}</li>
                <li>- {{ __('staff.claims.info_step_loan') }}</li>
            </ul>
        </x-ui.card>
    </div>
</x-portal-layout>
