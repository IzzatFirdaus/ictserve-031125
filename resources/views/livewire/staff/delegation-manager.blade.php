{{--
    Component: Delegation Manager
    Description: Manage approval delegations for temporary approvers
    WCAG Level: AA
    Version: 1.0
    Traceability: R10, R12
--}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('delegation.title') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('delegation.description') }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <x-ui.button wire:click="openCreateModal" type="primary" class="w-full sm:w-auto">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('delegation.create_delegation') }}
            </x-ui.button>
        </div>
    </div>

    {{-- Delegations To Me (if any) --}}
    @if ($this->delegationsToMe->count() > 0)
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('delegation.delegated_to_me') }}
                </h3>
            </x-slot:header>
            <div class="space-y-3">
                @foreach ($this->delegationsToMe as $delegation)
                    <div
                        class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex items-center space-x-4">
                            <div class="shrink-0">
                                <div
                                    class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center">
                                    <x-heroicon-o-user-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div>
                                <p class="font-medium text-green-900 dark:text-green-100">
                                    {{ __('delegation.from') }}: {{ $delegation->originalApprover->name }}
                                </p>
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    {{ $delegation->start_date->format('d/m/Y') }} -
                                    {{ $delegation->end_date->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <x-ui.badge type="success">{{ __('delegation.active') }}</x-ui.badge>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif

    {{-- Filter --}}
    <div class="flex items-center space-x-4">
        <label for="status_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('delegation.filter_by_status') }}:
        </label>
        <x-form.select wire:model.live="status_filter" id="status_filter" class="w-48">
            <option value="all">{{ __('delegation.filter_all') }}</option>
            <option value="active">{{ __('delegation.filter_active') }}</option>
            <option value="upcoming">{{ __('delegation.filter_upcoming') }}</option>
            <option value="expired">{{ __('delegation.filter_expired') }}</option>
            <option value="inactive">{{ __('delegation.filter_inactive') }}</option>
        </x-form.select>
    </div>

    {{-- My Delegations List --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ __('delegation.my_delegations') }}
            </h3>
        </x-slot:header>

        @if ($this->delegations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('delegation.delegated_to') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('delegation.period') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('delegation.status') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('delegation.reason') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('delegation.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($this->delegations as $delegation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                    {{ substr($delegation->delegatedApprover->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $delegation->delegatedApprover->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $delegation->delegatedApprover->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div>{{ $delegation->start_date->format('d/m/Y') }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        {{ __('delegation.to') }} {{ $delegation->end_date->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($delegation->isCurrentlyActive())
                                        <x-ui.badge type="success">{{ __('delegation.active') }}</x-ui.badge>
                                    @elseif($delegation->start_date->gt(now()))
                                        <x-ui.badge type="warning">{{ __('delegation.upcoming') }}</x-ui.badge>
                                    @elseif($delegation->end_date->lt(now()) && $delegation->is_active)
                                        <x-ui.badge type="secondary">{{ __('delegation.expired') }}</x-ui.badge>
                                    @else
                                        <x-ui.badge type="secondary">{{ __('delegation.inactive') }}</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    <div class="max-w-xs truncate" title="{{ $delegation->reason }}">
                                        {{ Str::limit($delegation->reason, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if ($delegation->is_active && ($delegation->isCurrentlyActive() || $delegation->start_date->gt(now())))
                                        <x-ui.button wire:click="confirmDeactivate({{ $delegation->id }})"
                                            type="danger" size="sm">
                                            {{ __('delegation.deactivate') }}
                                        </x-ui.button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $this->delegations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('delegation.no_delegations') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('delegation.no_delegations_description') }}
                </p>
                <div class="mt-6">
                    <x-ui.button wire:click="openCreateModal" type="primary">
                        <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                        {{ __('delegation.create_first') }}
                    </x-ui.button>
                </div>
            </div>
        @endif
    </x-ui.card>

    {{-- Create Delegation Modal --}}
    <x-ui.modal wire:model="showCreateModal" max-width="lg">
        <x-slot:title>{{ __('delegation.create_delegation') }}</x-slot:title>

        <form wire:submit="createDelegation" class="space-y-6">
            @if ($errors->has('form'))
                <x-ui.alert type="danger">
                    {{ $errors->first('form') }}
                </x-ui.alert>
            @endif

            {{-- Delegated Approver --}}
            <div>
                <x-form.select wire:model="delegated_approver_id" :label="__('delegation.delegated_approver')" required>
                    <option value="">{{ __('delegation.select_approver') }}</option>
                    @foreach ($this->availableApprovers as $approver)
                        <option value="{{ $approver->id }}">
                            {{ $approver->name }} ({{ $approver->email }})
                        </option>
                    @endforeach
                </x-form.select>
                @error('delegated_approver_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date Range --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-form.input wire:model="start_date" type="date" :label="__('delegation.start_date')" :min="now()->format('Y-m-d')" required />
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <x-form.input wire:model="end_date" type="date" :label="__('delegation.end_date')" :min="$start_date" required />
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <x-form.textarea wire:model="reason" :label="__('delegation.reason')" :placeholder="__('delegation.reason_placeholder')" rows="3" required />
                @error('reason')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('delegation.reason_help') }}
                </p>
            </div>

            <x-slot:footer>
                <div class="flex justify-end space-x-3">
                    <x-ui.button wire:click="closeCreateModal" type="secondary">
                        {{ __('common.cancel') }}
                    </x-ui.button>
                    <x-ui.button type="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createDelegation">
                            {{ __('delegation.create') }}
                        </span>
                        <span wire:loading wire:target="createDelegation">
                            {{ __('common.processing') }}...
                        </span>
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </form>
    </x-ui.modal>

    {{-- Confirm Deactivate Modal --}}
    <x-ui.modal wire:model="showConfirmDeactivate" max-width="md">
        <x-slot:title>{{ __('delegation.confirm_deactivate') }}</x-slot:title>

        <p class="text-gray-600 dark:text-gray-400">
            {{ __('delegation.confirm_deactivate_message') }}
        </p>

        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <x-ui.button wire:click="cancelDeactivate" type="secondary">
                    {{ __('common.cancel') }}
                </x-ui.button>
                <x-ui.button wire:click="deactivateDelegation" type="danger" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deactivateDelegation">
                        {{ __('delegation.deactivate') }}
                    </span>
                    <span wire:loading wire:target="deactivateDelegation">
                        {{ __('common.processing') }}...
                    </span>
                </x-ui.button>
            </div>
        </x-slot:footer>
    </x-ui.modal>
</div>
