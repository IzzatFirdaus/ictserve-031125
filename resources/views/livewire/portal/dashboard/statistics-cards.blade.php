<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    {{-- My Open Tickets --}}
    <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-300 truncate">
                            My Open Tickets
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-100">
                                {{ $this->openTicketsCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('helpdesk.authenticated.tickets') }}"
                    class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                    View All
                </a>
            </div>
        </div>
    </div>

    {{-- My Pending Loans --}}
    <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-300 truncate">
                            My Pending Loans
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-100">
                                {{ $this->pendingLoansCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('loan.authenticated.history') }}"
                    class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                    View All
                </a>
            </div>
        </div>
    </div>

    {{-- Overdue Items --}}
    <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-300 truncate">
                            Overdue Items
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-100">
                                {{ $this->overdueItemsCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('loan.authenticated.history', ['status' => 'overdue']) }}"
                    class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                    View Overdue
                </a>
            </div>
        </div>
    </div>

    {{-- Pending Approvals (approvers only) --}}
    @if ($this->isApproverUser)
        <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-300 truncate">
                                Pending Approvals
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-slate-100">
                                    {{ $this->pendingApprovalsCount }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-slate-800/50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('loan.authenticated.history') }}"
                        class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                        View Pending
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
