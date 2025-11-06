

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" aria-describedby="loan-dashboard-description">
    <p id="loan-dashboard-description" class="sr-only">
        <?php echo e(__('Ringkasan statistik pinjaman aset dan aktiviti semasa untuk kakitangan MOTAC.')); ?>

    </p>
    
    <?php if (isset($component)) { $__componentOriginal8e96cb30d753cea0c0f163f6295be009 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e96cb30d753cea0c0f163f6295be009 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.skip-links','data' => ['targets' => [
        ['id' => 'loan-stats', 'label' => __('loans.skip_to_statistics')],
        ['id' => 'loan-tabs', 'label' => __('loans.skip_to_tabs')],
        ['id' => 'main-content', 'label' => __('loans.skip_to_main_content')]
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.skip-links'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['targets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['id' => 'loan-stats', 'label' => __('loans.skip_to_statistics')],
        ['id' => 'loan-tabs', 'label' => __('loans.skip_to_tabs')],
        ['id' => 'main-content', 'label' => __('loans.skip_to_main_content')]
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8e96cb30d753cea0c0f163f6295be009)): ?>
<?php $attributes = $__attributesOriginal8e96cb30d753cea0c0f163f6295be009; ?>
<?php unset($__attributesOriginal8e96cb30d753cea0c0f163f6295be009); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e96cb30d753cea0c0f163f6295be009)): ?>
<?php $component = $__componentOriginal8e96cb30d753cea0c0f163f6295be009; ?>
<?php unset($__componentOriginal8e96cb30d753cea0c0f163f6295be009); ?>
<?php endif; ?>

    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100" id="dashboard-heading">
            <?php echo e(__('loans.authenticated_dashboard')); ?>

        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            <?php echo e(__('loans.dashboard_description')); ?>

        </p>
    </div>

    
    <div id="loan-stats" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8" role="region" aria-label="<?php echo e(__('loans.loan_statistics')); ?>">
        
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 border-blue-200 dark:border-blue-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 border-blue-200 dark:border-blue-700']); ?>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-blue-700 dark:text-blue-300 truncate">
                            <?php echo e(__('loans.active_loans')); ?>

                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                                <?php echo e($this->activeLoansCount); ?>

                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 border-yellow-200 dark:border-yellow-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 border-yellow-200 dark:border-yellow-700']); ?>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-yellow-700 dark:text-yellow-300 truncate">
                            <?php echo e(__('loans.pending_applications')); ?>

                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">
                                <?php echo e($this->pendingCount); ?>

                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 border-red-200 dark:border-red-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 border-red-200 dark:border-red-700']); ?>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-red-700 dark:text-red-300 truncate">
                            <?php echo e(__('loans.overdue_items')); ?>

                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-red-900 dark:text-red-100">
                                <?php echo e($this->overdueCount); ?>

                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 border-green-200 dark:border-green-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 border-green-200 dark:border-green-700']); ?>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-green-700 dark:text-green-300 truncate">
                            <?php echo e(__('loans.total_applications')); ?>

                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-green-900 dark:text-green-100">
                                <?php echo e($this->totalApplicationsCount); ?>

                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    </div>

    
    <div id="loan-tabs" role="region" aria-label="<?php echo e(__('loans.loan_management_tabs')); ?>">
        <?php if (isset($component)) { $__componentOriginal2f80d12fa4a9d0be1cb1fc5e60239698 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f80d12fa4a9d0be1cb1fc5e60239698 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.tabs','data' => ['tabs' => [
                ['id' => 'overview', 'label' => __('loans.tab_overview'), 'icon' => 'home'],
                ['id' => 'active', 'label' => __('loans.tab_active_loans'), 'icon' => 'check-circle', 'badge' => $this->activeLoansCount],
                ['id' => 'pending', 'label' => __('loans.tab_pending'), 'icon' => 'clock', 'badge' => $this->pendingCount],
                ['id' => 'history', 'label' => __('loans.tab_history'), 'icon' => 'document-text'],
            ],'activeTab' => $activeTab,'wire:model.live' => 'activeTab']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                ['id' => 'overview', 'label' => __('loans.tab_overview'), 'icon' => 'home'],
                ['id' => 'active', 'label' => __('loans.tab_active_loans'), 'icon' => 'check-circle', 'badge' => $this->activeLoansCount],
                ['id' => 'pending', 'label' => __('loans.tab_pending'), 'icon' => 'clock', 'badge' => $this->pendingCount],
                ['id' => 'history', 'label' => __('loans.tab_history'), 'icon' => 'document-text'],
            ]),'active-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeTab),'wire:model.live' => 'activeTab']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2f80d12fa4a9d0be1cb1fc5e60239698)): ?>
<?php $attributes = $__attributesOriginal2f80d12fa4a9d0be1cb1fc5e60239698; ?>
<?php unset($__attributesOriginal2f80d12fa4a9d0be1cb1fc5e60239698); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2f80d12fa4a9d0be1cb1fc5e60239698)): ?>
<?php $component = $__componentOriginal2f80d12fa4a9d0be1cb1fc5e60239698; ?>
<?php unset($__componentOriginal2f80d12fa4a9d0be1cb1fc5e60239698); ?>
<?php endif; ?>

        
        <div id="main-content" class="mt-6" role="tabpanel" aria-labelledby="tab-<?php echo e($activeTab); ?>">
            
            <!--[if BLOCK]><![endif]--><?php if($activeTab === 'overview'): ?>
                <div class="space-y-6">
                    
                    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <?php echo e(__('loans.quick_actions')); ?>

                        </h2>
                        <div class="flex flex-wrap gap-4">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('loan.authenticated.create')).'','color' => 'primary','class' => 'min-h-[44px] min-w-[44px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('loan.authenticated.create')).'','color' => 'primary','class' => 'min-h-[44px] min-w-[44px]']); ?>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <?php echo e(__('loans.new_application')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('staff.profile')).'','color' => 'secondary','class' => 'min-h-[44px] min-w-[44px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('staff.profile')).'','color' => 'secondary','class' => 'min-h-[44px] min-w-[44px]']); ?>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <?php echo e(__('loans.manage_profile')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

                    
                    <!--[if BLOCK]><![endif]--><?php if($this->activeLoans->count() > 0): ?>
                        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                <?php echo e(__('loans.recent_active_loans')); ?>

                            </h2>
                            <div class="space-y-4">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->activeLoans->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-r">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    <?php echo e($loan->application_number); ?>

                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    <?php echo e($loan->loanItems->count()); ?> <?php echo e(__('loans.items')); ?> •
                                                    <?php echo e($loan->start_date->translatedFormat('d M Y')); ?> - <?php echo e($loan->end_date->translatedFormat('d M Y')); ?>

                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <?php echo e($loan->status->label()); ?>

                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <?php if($this->activeLoans->count() > 3): ?>
                                <div class="mt-4">
                                    <button wire:click="switchTab('active')" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium min-h-[44px] min-w-[44px]">
                                        <?php echo e(__('loans.view_all_active')); ?> →
                                    </button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'text-center py-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-center py-12']); ?>
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e(__('loans.no_active_loans')); ?></h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e(__('loans.no_active_loans_description')); ?></p>
                            <div class="mt-6">
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('loan.authenticated.create')).'','color' => 'primary','class' => 'min-h-[44px] min-w-[44px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('loan.authenticated.create')).'','color' => 'primary','class' => 'min-h-[44px] min-w-[44px]']); ?>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <?php echo e(__('loans.create_first_application')); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

            
            <?php elseif($activeTab === 'active'): ?>
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        <?php echo e(__('loans.active_loans_list')); ?>

                    </h2>
                    <!--[if BLOCK]><![endif]--><?php if($this->activeLoans->count() > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.application_number')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.items')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.period')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.status')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->activeLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e($loan->application_number); ?></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($loan->created_at->translatedFormat('d M Y')); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    <?php echo e($loan->loanItems->count()); ?> <?php echo e(__('loans.items')); ?>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    <?php echo e($loan->start_date->translatedFormat('d M Y')); ?> - <?php echo e($loan->end_date->translatedFormat('d M Y')); ?>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: <?php echo e($loan->status->color()); ?>20; color: <?php echo e($loan->status->color()); ?>;">
                                                    <?php echo e($loan->status->label()); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="<?php echo e(route('loan.authenticated.show', $loan->id)); ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 min-h-[44px] inline-flex items-center"><?php echo e(__('loans.view_details')); ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('loans.no_active_loans')); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            
            <?php elseif($activeTab === 'pending'): ?>
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        <?php echo e(__('loans.pending_applications_list')); ?>

                    </h2>
                    <!--[if BLOCK]><![endif]--><?php if($this->pendingApplications->count() > 0): ?>
                        <div class="space-y-4">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->pendingApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100"><?php echo e($application->application_number); ?></h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: <?php echo e($application->status->color()); ?>20; color: <?php echo e($application->status->color()); ?>;">
                                            <?php echo e($application->status->label()); ?>

                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2"><?php echo e(Str::limit($application->purpose, 100)); ?></p>
                                    <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                        <span><?php echo e($application->created_at->translatedFormat('d M Y')); ?></span>
                                        <a href="<?php echo e(route('loan.authenticated.show', $application->id)); ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-medium min-h-[44px] inline-flex items-center"><?php echo e(__('loans.view_details')); ?> →</a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('loans.no_pending_applications')); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            
            <?php elseif($activeTab === 'history'): ?>
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <?php echo e(__('loans.loan_history')); ?>

                        </h2>

                        
                        <div class="flex flex-col sm:flex-row gap-2">
                            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'search','type' => 'search','placeholder' => ''.e(__('loans.search_loans')).'','class' => 'min-h-[44px]','ariaLabel' => ''.e(__('loans.search_loans')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'search','type' => 'search','placeholder' => ''.e(__('loans.search_loans')).'','class' => 'min-h-[44px]','aria-label' => ''.e(__('loans.search_loans')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $attributes = $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $component = $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>

                            <!--[if BLOCK]><![endif]--><?php if($search || $statusFilter): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['wire:click' => 'clearFilters','color' => 'secondary','size' => 'sm','class' => 'min-h-[44px] min-w-[44px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'clearFilters','color' => 'secondary','size' => 'sm','class' => 'min-h-[44px] min-w-[44px]']); ?>
                                    <?php echo e(__('loans.clear_filters')); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($this->loanHistory->count() > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.application')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.purpose')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.period')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.status')); ?></th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('loans.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->loanHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e($loan->application_number); ?></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($loan->created_at->translatedFormat('d M Y')); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100"><?php echo e(Str::limit($loan->purpose, 50)); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    <?php echo e($loan->start_date->translatedFormat('d M')); ?> - <?php echo e($loan->end_date->translatedFormat('d M Y')); ?>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: <?php echo e($loan->status->color()); ?>20; color: <?php echo e($loan->status->color()); ?>;">
                                                    <?php echo e($loan->status->label()); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="<?php echo e(route('loan.authenticated.show', $loan->id)); ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 min-h-[44px] inline-flex items-center"><?php echo e(__('loans.view')); ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="mt-4">
                            <?php echo e($this->loanHistory->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">
                                <!--[if BLOCK]><![endif]--><?php if($search || $statusFilter): ?>
                                    <?php echo e(__('loans.no_results_found')); ?>

                                <?php else: ?>
                                    <?php echo e(__('loans.no_loan_history')); ?>

                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div aria-live="polite" aria-atomic="true" class="sr-only">
        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
            <?php echo e(session('success')); ?>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/loans/authenticated-dashboard.blade.php ENDPATH**/ ?>