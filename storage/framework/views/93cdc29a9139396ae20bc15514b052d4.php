

<div class="min-h-screen bg-slate-950 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-slate-900/70 backdrop-blur-sm rounded-lg border border-slate-800 mb-6 overflow-hidden">
            <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-red-600 w-16 h-16 flex items-center justify-center rounded">
                        <span class="text-white font-bold text-xl">BPM</span>
                    </div>
                    <div class="text-slate-100">
                        <h1 class="text-xl font-bold"><?php echo e(__('loan.form.title')); ?></h1>
                        <p class="text-sm text-slate-300"><?php echo e(__('loan.form.subtitle')); ?></p>
                    </div>
                </div>
                <div class="text-slate-100 text-right">
                    <div class="text-2xl font-bold"><?php echo e(__('loan.form.section_label')); ?> <?php echo e($currentStep); ?></div>
                    <div class="text-sm text-slate-300"><?php echo e(__('loan.form.of_4_pages')); ?></div>
                </div>
            </div>
        </div>

        
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= 4; $i++): ?>
                    <div class="flex-1 <?php echo e($i < 4 ? 'mr-2' : ''); ?>">
                        <div class="relative">
                            <div class="h-2 rounded-full <?php echo e($i <= $currentStep ? 'bg-blue-600' : 'bg-slate-700'); ?>">
                            </div>
                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 text-center">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                    <?php echo e($i <= $currentStep ? 'bg-blue-600 text-white border-2 border-blue-600' : 'bg-slate-800 text-slate-400 border-2 border-slate-700'); ?>">
                                    <?php echo e($i); ?>

                                </div>
                                <p class="text-xs mt-1 <?php echo e($i <= $currentStep ? 'text-slate-100' : 'text-slate-400'); ?>"><?php echo e(__("loan.form.step_{$i}_label")); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <form wire:submit="submitForm">
            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 1): ?>
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
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-1-description">
                        <legend class="sr-only">
                            <?php echo e(__('loan.form.section_1_applicant')); ?>

                        </legend>
                        
                        <div id="guest-loan-step-1-description"
                            class="bg-slate-800 -mx-6 -mt-6 px-6 py-3 mb-6 rounded-t-lg">
                            <h2 class="text-lg font-semibold text-slate-100">
                                <?php echo e(__('loan.form.section_1_applicant')); ?>

                            </h2>
                            <p class="text-sm text-slate-300 mt-1"><?php echo e(__('loan.form.required_fields_note')); ?></p>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
                            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-6 space-y-4">
                                <h3 class="text-sm font-semibold text-slate-100 uppercase tracking-wide">
                                    <?php echo e(__('loan.form.your_information')); ?>

                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <dt class="text-sm font-medium text-slate-300"><?php echo e(__('loan.fields.applicant_name')); ?></dt>
                                        <dd class="mt-1 text-base text-slate-100"><?php echo e(auth()->user()->name); ?></dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-slate-300"><?php echo e(__('loan.fields.phone')); ?></dt>
                                        <dd class="mt-1 text-base text-slate-100"><?php echo e(auth()->user()->phone ?? __('loan.messages.not_provided')); ?></dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-slate-300"><?php echo e(__('loan.fields.position_grade')); ?></dt>
                                        <dd class="mt-1 text-base text-slate-100"><?php echo e($form['position'] ?: __('loan.messages.not_provided')); ?></dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-slate-300"><?php echo e(__('loan.fields.division_unit')); ?></dt>
                                        <dd class="mt-1 text-base text-slate-100">
                                            <!--[if BLOCK]><![endif]--><?php if(auth()->user()->division): ?>
                                                <?php echo e(auth()->user()->division->name); ?>

                                            <?php else: ?>
                                                <?php echo e(__('loan.messages.not_provided')); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </dd>
                                    </div>
                                </div>

                                <p class="text-xs text-blue-300 mt-4">
                                    <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <?php echo e(__('loan.messages.info_from_profile')); ?>

                                </p>
                            </div>
                        <?php else: ?>
                            
                            
                            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.applicant_name','name' => 'form.applicant_name','label' => __('loan.fields.applicant_name'),'required' => true,'placeholder' => __('loan.placeholders.applicant_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.applicant_name','name' => 'form.applicant_name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.applicant_name')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.applicant_name'))]); ?>
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

                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.position','name' => 'form.position','label' => __('loan.fields.position_grade'),'required' => true,'placeholder' => __('loan.placeholders.position')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.position','name' => 'form.position','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.position_grade')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.position'))]); ?>
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

                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.phone','name' => 'form.phone','type' => 'tel','label' => __('loan.fields.phone'),'required' => true,'placeholder' => __('loan.placeholders.phone')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.phone','name' => 'form.phone','type' => 'tel','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.phone')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.phone'))]); ?>
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
                            </div>

                            
                            <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['wire:model.live' => 'form.division_id','name' => 'form.division_id','label' => __('loan.fields.division_unit'),'required' => true,'placeholder' => __('loan.placeholders.select_division')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.division_id','name' => 'form.division_id','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.division_unit')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.select_division'))]); ?>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($division->id); ?>"><?php echo e($division->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $attributes = $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $component = $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <?php if (isset($component)) { $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.textarea','data' => ['wire:model.live.debounce.300ms' => 'form.purpose','name' => 'form.purpose','label' => __('loan.fields.purpose'),'rows' => '4','required' => true,'placeholder' => __('loan.placeholders.purpose'),'showCharCount' => true,'maxlength' => '500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.purpose','name' => 'form.purpose','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.purpose')),'rows' => '4','required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.purpose')),'showCharCount' => true,'maxlength' => '500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $attributes = $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $component = $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>

                        
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.location','name' => 'form.location','label' => __('loan.fields.location'),'required' => true,'placeholder' => __('loan.placeholders.location')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.location','name' => 'form.location','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.location')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.location'))]); ?>
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

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live' => 'form.loan_start_date','name' => 'form.loan_start_date','type' => 'date','label' => __('loan.fields.loan_start_date'),'required' => true,'min' => date('Y-m-d', strtotime('+1 day'))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.loan_start_date','name' => 'form.loan_start_date','type' => 'date','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.loan_start_date')),'required' => true,'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(date('Y-m-d', strtotime('+1 day')))]); ?>
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

                            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live' => 'form.loan_end_date','name' => 'form.loan_end_date','type' => 'date','label' => __('loan.fields.loan_end_date'),'required' => true,'min' => $form['loan_start_date'] ?? date('Y-m-d', strtotime('+2 days'))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.loan_end_date','name' => 'form.loan_end_date','type' => 'date','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.loan_end_date')),'required' => true,'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($form['loan_start_date'] ?? date('Y-m-d', strtotime('+2 days')))]); ?>
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
                        </div>
                    </fieldset>
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

            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 2): ?>
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
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-2-description">
                        <legend class="sr-only">
                            <?php echo e(__('loan.form.section_2_responsible_officer')); ?>

                        </legend>
                        
                        <div id="guest-loan-step-2-description"
                            class="bg-slate-800 -mx-6 -mt-6 px-6 py-3 mb-6 rounded-t-lg">
                            <h2 class="text-lg font-semibold text-slate-100">
                                <?php echo e(__('loan.form.section_2_responsible_officer')); ?>

                            </h2>
                            <p class="text-sm text-slate-300 mt-1"><?php echo e(__('loan.form.required_fields_note')); ?></p>
                        </div>

                        
                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'form.is_responsible_officer','name' => 'form.is_responsible_officer','label' => __('loan.fields.is_responsible_officer'),'helpText' => __('loan.help.is_responsible_officer')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.is_responsible_officer','name' => 'form.is_responsible_officer','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.is_responsible_officer')),'helpText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.help.is_responsible_officer'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal43da204543437953b216481011f1ac88)): ?>
<?php $attributes = $__attributesOriginal43da204543437953b216481011f1ac88; ?>
<?php unset($__attributesOriginal43da204543437953b216481011f1ac88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal43da204543437953b216481011f1ac88)): ?>
<?php $component = $__componentOriginal43da204543437953b216481011f1ac88; ?>
<?php unset($__componentOriginal43da204543437953b216481011f1ac88); ?>
<?php endif; ?>

                        <!--[if BLOCK]><![endif]--><?php if(!$form['is_responsible_officer']): ?>
                            
                            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.responsible_officer_name','name' => 'form.responsible_officer_name','label' => __('loan.fields.responsible_officer_name'),'required' => true,'placeholder' => __('loan.placeholders.responsible_officer_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.responsible_officer_name','name' => 'form.responsible_officer_name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.responsible_officer_name')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.responsible_officer_name'))]); ?>
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

                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.responsible_officer_position','name' => 'form.responsible_officer_position','label' => __('loan.fields.position_grade'),'required' => true,'placeholder' => __('loan.placeholders.position')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.responsible_officer_position','name' => 'form.responsible_officer_position','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.position_grade')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.position'))]); ?>
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

                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.responsible_officer_phone','name' => 'form.responsible_officer_phone','type' => 'tel','label' => __('loan.fields.phone'),'required' => true,'placeholder' => __('loan.placeholders.phone')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.responsible_officer_phone','name' => 'form.responsible_officer_phone','type' => 'tel','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.phone')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.phone'))]); ?>
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
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </fieldset>
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

            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 3): ?>
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
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-3-description">
                        <legend class="sr-only">
                            <?php echo e(__('loan.form.section_3_equipment_list')); ?>

                        </legend>
                        
                        <div id="guest-loan-step-3-description"
                            class="bg-slate-800 -mx-6 -mt-6 px-6 py-3 mb-6 rounded-t-lg">
                            <h2 class="text-lg font-semibold text-slate-100">
                                <?php echo e(__('loan.form.section_3_equipment_list')); ?>

                            </h2>
                            <p class="text-sm text-slate-300 mt-1"><?php echo e(__('loan.form.select_equipment_note')); ?></p>
                        </div>

                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-700">
                                <thead class="bg-slate-800">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                            <?php echo e(__('loan.table.no')); ?>

                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                            <?php echo e(__('loan.table.equipment_type')); ?>

                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                            <?php echo e(__('loan.table.quantity')); ?>

                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                            <?php echo e(__('loan.table.notes')); ?>

                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-slate-900/50 divide-y divide-slate-700">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $form['equipment_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-100">
                                                <?php echo e($index + 1); ?>

                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['wire:model.live' => 'form.equipment_items.'.e($index).'.equipment_type','name' => 'form.equipment_items.'.e($index).'.equipment_type','placeholder' => __('loan.placeholders.select_equipment')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.equipment_items.'.e($index).'.equipment_type','name' => 'form.equipment_items.'.e($index).'.equipment_type','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.select_equipment'))]); ?>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $equipmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $attributes = $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $component = $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live' => 'form.equipment_items.'.e($index).'.quantity','name' => 'form.equipment_items.'.e($index).'.quantity','type' => 'number','min' => '1','placeholder' => __('loan.placeholders.quantity')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.equipment_items.'.e($index).'.quantity','name' => 'form.equipment_items.'.e($index).'.quantity','type' => 'number','min' => '1','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.quantity'))]); ?>
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
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.equipment_items.'.e($index).'.notes','name' => 'form.equipment_items.'.e($index).'.notes','placeholder' => __('loan.placeholders.notes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.equipment_items.'.e($index).'.notes','name' => 'form.equipment_items.'.e($index).'.notes','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.notes'))]); ?>
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
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="flex justify-between items-center pt-4">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','wire:click' => 'addEquipmentRow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','wire:click' => 'addEquipmentRow']); ?>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <?php echo e(__('loan.actions.add_equipment')); ?>

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

                            <!--[if BLOCK]><![endif]--><?php if(count($form['equipment_items']) > 1): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'danger','wire:click' => 'removeEquipmentRow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'danger','wire:click' => 'removeEquipmentRow']); ?>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                    <?php echo e(__('loan.actions.remove_equipment')); ?>

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

                        
                        <div class="mt-8 p-4 bg-slate-800 rounded-lg">
                            <h3 class="text-sm font-semibold text-slate-100 mb-4">
                                <?php echo e(__('loan.form.section_4_applicant_confirmation')); ?>

                            </h3>
                            <p class="text-sm text-slate-300 mb-4">
                                <?php echo e(__('loan.form.confirmation_statement')); ?>

                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        <?php echo e(__('loan.fields.date')); ?>

                                    </label>
                                    <input type="text" value="<?php echo e(date('d/m/Y')); ?>" readonly
                                        class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        <?php echo e(__('loan.fields.signature')); ?>

                                        <span
                                            class="text-slate-400 text-xs">(<?php echo e(__('loan.help.if_applicable')); ?>)</span>
                                    </label>
                                    <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model.live.debounce.300ms' => 'form.applicant_signature','name' => 'form.applicant_signature','placeholder' => __('loan.placeholders.signature')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'form.applicant_signature','name' => 'form.applicant_signature','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.placeholders.signature'))]); ?>
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
                                </div>
                            </div>
                        </div>
                    </fieldset>
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

            
            <!--[if BLOCK]><![endif]--><?php if($currentStep === 4): ?>
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
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-4-description">
                        <legend class="sr-only">
                            <?php echo e(__('loan.form.section_5_approval')); ?>

                        </legend>
                        
                        <div id="guest-loan-step-4-description"
                            class="bg-slate-800 -mx-6 -mt-6 px-6 py-3 mb-6 rounded-t-lg">
                            <h2 class="text-lg font-semibold text-slate-100">
                                <?php echo e(__('loan.form.section_5_approval')); ?>

                            </h2>
                            <p class="text-sm text-slate-300 mt-1">
                                <?php echo e(__('loan.form.approval_note')); ?>

                            </p>
                        </div>

                        
                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-blue-400 mr-3 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-100 mb-2">
                                        <?php echo e(__('loan.form.approval_process_title')); ?>

                                    </h3>
                                    <p class="text-sm text-blue-300">
                                        <?php echo e(__('loan.form.approval_process_description')); ?>

                                    </p>
                                </div>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    <?php echo e(__('loan.fields.approval_status')); ?>

                                </label>
                                <input type="text" value="<?php echo e(__('loan.status.pending_approval')); ?>" readonly
                                    class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    <?php echo e(__('loan.fields.submission_date')); ?>

                                </label>
                                <input type="text" value="<?php echo e(date('d/m/Y')); ?>" readonly
                                    class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                            </div>
                        </div>

                        
                        <div class="mt-8 p-6 bg-slate-800 rounded-lg">
                            <h3 class="text-lg font-semibold text-slate-100 mb-4">
                                <?php echo e(__('loan.form.review_summary')); ?>

                            </h3>

                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-slate-400">
                                        <?php echo e(__('loan.fields.applicant_name')); ?></dt>
                                    <dd class="mt-1 text-sm text-slate-100"><?php echo e($form['applicant_name']); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-400">
                                        <?php echo e(__('loan.fields.division_unit')); ?></dt>
                                    <dd class="mt-1 text-sm text-slate-100">
                                        <?php echo e($divisions->firstWhere('id', $form['division_id'])?->name ?? '-'); ?>

                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-400"><?php echo e(__('loan.fields.loan_period')); ?>

                                    </dt>
                                    <dd class="mt-1 text-sm text-slate-100">
                                        <?php echo e(date('d/m/Y', strtotime($form['loan_start_date']))); ?> -
                                        <?php echo e(date('d/m/Y', strtotime($form['loan_end_date']))); ?>

                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-400">
                                        <?php echo e(__('loan.fields.total_equipment')); ?></dt>
                                    <dd class="mt-1 text-sm text-slate-100">
                                        <?php echo e(count(array_filter($form['equipment_items'], fn($item) => !empty($item['equipment_type'])))); ?>

                                        <?php echo e(__('loan.units.items')); ?>

                                    </dd>
                                </div>
                            </dl>
                        </div>

                        
                        <div class="mt-6">
                            <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'form.accept_terms','name' => 'form.accept_terms','label' => __('loan.fields.accept_terms'),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'form.accept_terms','name' => 'form.accept_terms','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('loan.fields.accept_terms')),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal43da204543437953b216481011f1ac88)): ?>
<?php $attributes = $__attributesOriginal43da204543437953b216481011f1ac88; ?>
<?php unset($__attributesOriginal43da204543437953b216481011f1ac88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal43da204543437953b216481011f1ac88)): ?>
<?php $component = $__componentOriginal43da204543437953b216481011f1ac88; ?>
<?php unset($__componentOriginal43da204543437953b216481011f1ac88); ?>
<?php endif; ?>
                        </div>
                    </fieldset>
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

            
            <div class="mt-8 flex justify-between items-center">
                <!--[if BLOCK]><![endif]--><?php if($currentStep > 1): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','wire:click' => 'previousStep']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','wire:click' => 'previousStep']); ?>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        <?php echo e(__('loan.actions.previous')); ?>

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
                <?php else: ?>
                    <div></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if($currentStep < 4): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'primary','wire:click' => 'nextStep']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'primary','wire:click' => 'nextStep']); ?>
                        <?php echo e(__('loan.actions.next')); ?>

                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
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
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'success','loading' => $submitting,'disabled' => !$form['accept_terms']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'success','loading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($submitting),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$form['accept_terms'])]); ?>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <?php echo e(__('loan.actions.submit_application')); ?>

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
        </form>

        
        <div class="mt-8 p-6 bg-slate-900/70 backdrop-blur-sm rounded-lg border border-slate-800">
            <h2 class="text-lg font-semibold text-slate-100 mb-4">
                <?php echo e(__('loan.help.need_assistance')); ?>

            </h2>
            <p class="text-sm text-slate-300 mb-4">
                <?php echo e(__('loan.help.contact_info')); ?>

            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex items-center text-sm text-slate-400">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span><?php echo e(__('loan.help.email')); ?></span>
                </div>
                <div class="flex items-center text-sm text-slate-400">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span><?php echo e(__('loan.help.phone')); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/guest-loan-application.blade.php ENDPATH**/ ?>