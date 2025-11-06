

<?php
    use Illuminate\Support\Facades\Auth;
?>

<div class="py-6">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-slate-100 sm:text-3xl sm:truncate">
                    <?php echo e(__('common.dashboard')); ?>

                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    <?php echo e(__('common.welcome_back')); ?>, <?php echo e(Auth::user()->name); ?>

                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button wire:click="refreshData" type="button"
                    class="inline-flex items-center px-4 py-2 border border-slate-700 rounded-md shadow-sm text-sm font-medium text-slate-200 bg-slate-800 hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-[44px] min-w-[44px]"
                    aria-label="<?php echo e(__('common.refresh_dashboard')); ?>">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <?php echo e(__('common.refresh')); ?>

                </button>
            </div>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            
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
                                <dt class="text-sm font-medium text-slate-400 truncate">
                                    <?php echo e(__('common.my_open_tickets')); ?>

                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-slate-100">
                                        <?php echo e($this->statistics['open_tickets']); ?>

                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-800/50 px-5 py-3">
                    <div class="text-sm">
                        <a href="<?php echo e(route('helpdesk.authenticated.tickets')); ?>"
                            class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                            <?php echo e(__('common.view_all')); ?>

                        </a>
                    </div>
                </div>
            </div>

            
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
                                <dt class="text-sm font-medium text-slate-400 truncate">
                                    <?php echo e(__('common.my_pending_loans')); ?>

                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-slate-100">
                                        <?php echo e($this->statistics['pending_loans']); ?>

                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-800/50 px-5 py-3">
                    <div class="text-sm">
                        <a href="<?php echo e(route('loan.authenticated.history')); ?>"
                            class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                            <?php echo e(__('common.view_all')); ?>

                        </a>
                    </div>
                </div>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if(Auth::user()->hasRole('approver') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('superuser')): ?>
                <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-400 truncate">
                                        <?php echo e(__('common.pending_approvals')); ?>

                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-slate-100">
                                            <?php echo e($this->statistics['pending_approvals'] ?? 0); ?>

                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 px-5 py-3">
                        <div class="text-sm">
                            <a href="<?php echo e(route('staff.approvals.index')); ?>"
                                class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                                <?php echo e(__('common.review_approvals')); ?>

                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
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
                                <dt class="text-sm font-medium text-slate-400 truncate">
                                    <?php echo e(__('common.overdue_items')); ?>

                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-slate-100">
                                        <?php echo e($this->statistics['overdue_items']); ?>

                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-800/50 px-5 py-3">
                    <div class="text-sm">
                        <a href="<?php echo e(route('loan.authenticated.history')); ?>?status=overdue"
                            class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                            <?php echo e(__('common.view_overdue')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-slate-100 mb-4">
                <?php echo e(__('common.quick_actions')); ?>

            </h2>
            <div class="flex flex-wrap gap-4">
                <a href="<?php echo e(route('helpdesk.create')); ?>"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-[44px] min-w-[44px]">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <?php echo e(__('common.new_ticket')); ?>

                </a>
                <a href="<?php echo e(route('loan.guest.apply')); ?>"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-[44px] min-w-[44px]">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <?php echo e(__('common.request_loan')); ?>

                </a>
                <a href="<?php echo e(route('welcome')); ?>"
                    class="inline-flex items-center px-4 py-2 border border-slate-700 rounded-md shadow-sm text-sm font-medium text-slate-200 bg-slate-800 hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-[44px] min-w-[44px]">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    <?php echo e(__('common.view_all_services')); ?>

                </a>
            </div>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            
            <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 shadow rounded-lg">
                <div class="px-6 py-5 border-b border-slate-800">
                    <h3 class="text-lg leading-6 font-medium text-slate-100">
                        <?php echo e(__('common.my_recent_tickets')); ?>

                    </h3>
                </div>
                <div class="px-6 py-4">
                    <!--[if BLOCK]><![endif]--><?php if($this->recentTickets->isEmpty()): ?>
                        <p class="text-sm text-slate-400 text-center py-4">
                            <?php echo e(__('common.no_recent_tickets')); ?>

                        </p>
                    <?php else: ?>
                        <ul role="list" class="divide-y divide-slate-800">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="py-4" wire:key="ticket-<?php echo e($ticket->id); ?>">
                                    <div class="flex space-x-3">
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-slate-100">
                                                    <?php echo e($ticket->ticket_number); ?>

                                                </h4>
                                                <?php if (isset($component)) { $__componentOriginal1b56d3087a65949b8cf7a291743bb419 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1b56d3087a65949b8cf7a291743bb419 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.status-badge','data' => ['status' => $ticket->status,'type' => 'helpdesk']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->status),'type' => 'helpdesk']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1b56d3087a65949b8cf7a291743bb419)): ?>
<?php $attributes = $__attributesOriginal1b56d3087a65949b8cf7a291743bb419; ?>
<?php unset($__attributesOriginal1b56d3087a65949b8cf7a291743bb419); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1b56d3087a65949b8cf7a291743bb419)): ?>
<?php $component = $__componentOriginal1b56d3087a65949b8cf7a291743bb419; ?>
<?php unset($__componentOriginal1b56d3087a65949b8cf7a291743bb419); ?>
<?php endif; ?>
                                            </div>
                                            <p class="text-sm text-slate-400">
                                                <?php echo e(Str::limit($ticket->subject, 60)); ?>

                                            </p>
                                            <p class="text-xs text-slate-500">
                                                <?php echo e($ticket->created_at->diffForHumans()); ?>

                                            </p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </ul>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="px-6 py-3 bg-slate-800/50 text-right">
                    <a href="<?php echo e(route('helpdesk.authenticated.tickets')); ?>"
                        class="text-sm font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                        <?php echo e(__('common.view_all_tickets')); ?>

                    </a>
                </div>
            </div>

            
            <div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 shadow rounded-lg">
                <div class="px-6 py-5 border-b border-slate-800">
                    <h3 class="text-lg leading-6 font-medium text-slate-100">
                        <?php echo e(__('common.my_recent_loans')); ?>

                    </h3>
                </div>
                <div class="px-6 py-4">
                    <!--[if BLOCK]><![endif]--><?php if($this->recentLoans->isEmpty()): ?>
                        <p class="text-sm text-slate-400 text-center py-4">
                            <?php echo e(__('common.no_recent_loans')); ?>

                        </p>
                    <?php else: ?>
                        <ul role="list" class="divide-y divide-slate-800">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->recentLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="py-4" wire:key="loan-<?php echo e($loan->id); ?>">
                                    <div class="flex space-x-3">
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-slate-100">
                                                    <?php echo e($loan->application_number); ?>

                                                </h4>
                                                <?php if (isset($component)) { $__componentOriginal1b56d3087a65949b8cf7a291743bb419 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1b56d3087a65949b8cf7a291743bb419 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.status-badge','data' => ['status' => $loan->status->value,'type' => 'loan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loan->status->value),'type' => 'loan']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1b56d3087a65949b8cf7a291743bb419)): ?>
<?php $attributes = $__attributesOriginal1b56d3087a65949b8cf7a291743bb419; ?>
<?php unset($__attributesOriginal1b56d3087a65949b8cf7a291743bb419); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1b56d3087a65949b8cf7a291743bb419)): ?>
<?php $component = $__componentOriginal1b56d3087a65949b8cf7a291743bb419; ?>
<?php unset($__componentOriginal1b56d3087a65949b8cf7a291743bb419); ?>
<?php endif; ?>
                                            </div>
                                            <p class="text-sm text-slate-400">
                                                <?php echo e($loan->loanItems->count()); ?> <?php echo e(__('common.items')); ?>

                                                <?php if($loan->loanItems->isNotEmpty()): ?>
                                                    - <?php echo e($loan->loanItems->first()->asset->name); ?>

                                                    <!--[if BLOCK]><![endif]--><?php if($loan->loanItems->count() > 1): ?>
                                                        <?php echo e(__('common.and_more', ['count' => $loan->loanItems->count() - 1])); ?>

                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                <?php echo e($loan->created_at->diffForHumans()); ?>

                                            </p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </ul>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="px-6 py-3 bg-slate-800/50 text-right">
                    <a href="<?php echo e(route('loan.authenticated.history')); ?>"
                        class="text-sm font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950">
                        <?php echo e(__('common.view_all_loans')); ?>

                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div wire:loading wire:target="refreshData"
        class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-sm font-medium text-slate-100"><?php echo e(__('common.refreshing')); ?>...</span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/staff/authenticated-dashboard.blade.php ENDPATH**/ ?>