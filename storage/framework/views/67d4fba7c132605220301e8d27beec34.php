

<div class="py-6">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <h1 class="text-3xl font-bold text-slate-100">
            <?php echo e(__('profile.title')); ?>

        </h1>
        <p class="mt-2 text-sm text-slate-400">
            <?php echo e(__('profile.description')); ?>

        </p>
    </div>

    
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="profile-announcements"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
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
             <?php $__env->slot('header', null, []); ?> 
                <h2 class="text-xl font-semibold text-slate-100">
                    <?php echo e(__('profile.information_title')); ?>

                </h2>
                <p class="mt-1 text-sm text-slate-400">
                    <?php echo e(__('profile.information_description')); ?>

                </p>
             <?php $__env->endSlot(); ?>

            <form wire:submit.prevent="updateProfile" class="space-y-6">
                
                <!--[if BLOCK]><![endif]--><?php if($profileUpdateSuccess): ?>
                    <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'success','dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success','dismissible' => true]); ?>
                        <?php echo e(__('profile.update_success')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($profileError): ?>
                    <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'error','dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'error','dismissible' => true]); ?>
                        <?php echo e($profileError); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model' => 'name','id' => 'name','name' => 'name','type' => 'text','label' => __('profile.name'),'placeholder' => __('profile.name_placeholder'),'required' => true,'autocomplete' => 'name']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'name','id' => 'name','name' => 'name','type' => 'text','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.name')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.name_placeholder')),'required' => true,'autocomplete' => 'name']); ?>
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

                    <div>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model' => 'phone','id' => 'phone','name' => 'phone','type' => 'tel','label' => __('profile.phone'),'placeholder' => __('profile.phone_placeholder'),'autocomplete' => 'tel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'phone','id' => 'phone','name' => 'phone','type' => 'tel','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.phone')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.phone_placeholder')),'autocomplete' => 'tel']); ?>
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

                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            <?php echo e(__('profile.email')); ?>

                        </label>
                        <input type="email" id="email" value="<?php echo e($email); ?>" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-400 cursor-not-allowed"
                            aria-describedby="email-readonly" />
                        <p id="email-readonly" class="mt-1 text-xs text-slate-400">
                            <?php echo e(__('profile.email_readonly')); ?>

                        </p>
                    </div>

                    <div>
                        <label for="staff_id" class="block text-sm font-medium text-slate-300 mb-2">
                            <?php echo e(__('profile.staff_id')); ?>

                        </label>
                        <input type="text" id="staff_id" value="<?php echo e($staff_id); ?>" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-400 cursor-not-allowed" />
                    </div>

                    <div>
                        <label for="grade" class="block text-sm font-medium text-slate-300 mb-2">
                            <?php echo e(__('profile.grade')); ?>

                        </label>
                        <input type="text" id="grade" value="<?php echo e($grade); ?>" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-400 cursor-not-allowed" />
                    </div>

                    <div>
                        <label for="division" class="block text-sm font-medium text-slate-300 mb-2">
                            <?php echo e(__('profile.division')); ?>

                        </label>
                        <input type="text" id="division" value="<?php echo e($division); ?>" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-400 cursor-not-allowed" />
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-slate-300 mb-2">
                            <?php echo e(__('profile.position')); ?>

                        </label>
                        <input type="text" id="position" value="<?php echo e($position); ?>" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-400 cursor-not-allowed" />
                    </div>
                </div>

                
                <div class="flex justify-end">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','wire:loading.attr' => 'disabled','wire:target' => 'updateProfile']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','wire:loading.attr' => 'disabled','wire:target' => 'updateProfile']); ?>
                        <span wire:loading.remove wire:target="updateProfile">
                            <?php echo e(__('profile.save_changes')); ?>

                        </span>
                        <span wire:loading wire:target="updateProfile">
                            <?php echo e(__('profile.saving')); ?>

                        </span>
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
            </form>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('header', null, []); ?> 
                <h2 class="text-xl font-semibold text-slate-100">
                    <?php echo e(__('profile.notifications_title')); ?>

                </h2>
                <p class="mt-1 text-sm text-slate-400">
                    <?php echo e(__('profile.notifications_description')); ?>

                </p>
             <?php $__env->endSlot(); ?>

            <div class="space-y-4">
                
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
                        <?php echo e(__('profile.helpdesk_notifications')); ?>

                    </h3>
                    <div class="space-y-3">
                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.ticket_updates','wire:change' => 'updateNotificationPreferences','id' => 'ticket_updates','name' => 'ticket_updates','label' => __('profile.ticket_updates'),'description' => __('profile.ticket_updates_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.ticket_updates','wire:change' => 'updateNotificationPreferences','id' => 'ticket_updates','name' => 'ticket_updates','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.ticket_updates')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.ticket_updates_desc'))]); ?>
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

                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.ticket_assignments','wire:change' => 'updateNotificationPreferences','id' => 'ticket_assignments','name' => 'ticket_assignments','label' => __('profile.ticket_assignments'),'description' => __('profile.ticket_assignments_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.ticket_assignments','wire:change' => 'updateNotificationPreferences','id' => 'ticket_assignments','name' => 'ticket_assignments','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.ticket_assignments')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.ticket_assignments_desc'))]); ?>
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

                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.ticket_comments','wire:change' => 'updateNotificationPreferences','id' => 'ticket_comments','name' => 'ticket_comments','label' => __('profile.ticket_comments'),'description' => __('profile.ticket_comments_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.ticket_comments','wire:change' => 'updateNotificationPreferences','id' => 'ticket_comments','name' => 'ticket_comments','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.ticket_comments')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.ticket_comments_desc'))]); ?>
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

                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.sla_alerts','wire:change' => 'updateNotificationPreferences','id' => 'sla_alerts','name' => 'sla_alerts','label' => __('profile.sla_alerts'),'description' => __('profile.sla_alerts_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.sla_alerts','wire:change' => 'updateNotificationPreferences','id' => 'sla_alerts','name' => 'sla_alerts','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.sla_alerts')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.sla_alerts_desc'))]); ?>
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
                </div>

                
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
                        <?php echo e(__('profile.loan_notifications')); ?>

                    </h3>
                    <div class="space-y-3">
                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.loan_updates','wire:change' => 'updateNotificationPreferences','id' => 'loan_updates','name' => 'loan_updates','label' => __('profile.loan_updates'),'description' => __('profile.loan_updates_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.loan_updates','wire:change' => 'updateNotificationPreferences','id' => 'loan_updates','name' => 'loan_updates','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.loan_updates')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.loan_updates_desc'))]); ?>
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

                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.loan_approvals','wire:change' => 'updateNotificationPreferences','id' => 'loan_approvals','name' => 'loan_approvals','label' => __('profile.loan_approvals'),'description' => __('profile.loan_approvals_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.loan_approvals','wire:change' => 'updateNotificationPreferences','id' => 'loan_approvals','name' => 'loan_approvals','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.loan_approvals')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.loan_approvals_desc'))]); ?>
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

                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.loan_reminders','wire:change' => 'updateNotificationPreferences','id' => 'loan_reminders','name' => 'loan_reminders','label' => __('profile.loan_reminders'),'description' => __('profile.loan_reminders_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.loan_reminders','wire:change' => 'updateNotificationPreferences','id' => 'loan_reminders','name' => 'loan_reminders','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.loan_reminders')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.loan_reminders_desc'))]); ?>
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
                </div>

                
                <div>
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
                        <?php echo e(__('profile.system_notifications')); ?>

                    </h3>
                    <div class="space-y-3">
                        <?php if (isset($component)) { $__componentOriginal43da204543437953b216481011f1ac88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43da204543437953b216481011f1ac88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.checkbox','data' => ['wire:model.live' => 'notificationPreferences.system_announcements','wire:change' => 'updateNotificationPreferences','id' => 'system_announcements','name' => 'system_announcements','label' => __('profile.system_announcements'),'description' => __('profile.system_announcements_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'notificationPreferences.system_announcements','wire:change' => 'updateNotificationPreferences','id' => 'system_announcements','name' => 'system_announcements','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.system_announcements')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.system_announcements_desc'))]); ?>
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
                </div>

                
                <div class="flex items-center text-sm text-slate-400">
                    <svg wire:loading wire:target="updateNotificationPreferences" class="animate-spin h-4 w-4 mr-2"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading wire:target="updateNotificationPreferences">
                        <?php echo e(__('profile.saving_preferences')); ?>

                    </span>
                    <span wire:loading.remove wire:target="updateNotificationPreferences">
                        <?php echo e(__('profile.preferences_auto_save')); ?>

                    </span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('header', null, []); ?> 
                <h2 class="text-xl font-semibold text-slate-100">
                    <?php echo e(__('profile.password_title')); ?>

                </h2>
                <p class="mt-1 text-sm text-slate-400">
                    <?php echo e(__('profile.password_description')); ?>

                </p>
             <?php $__env->endSlot(); ?>

            <form wire:submit.prevent="updatePassword" class="space-y-6">
                
                <!--[if BLOCK]><![endif]--><?php if($passwordUpdateSuccess): ?>
                    <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'success','dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success','dismissible' => true]); ?>
                        <?php echo e(__('profile.password_updated')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($passwordError): ?>
                    <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'error','dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'error','dismissible' => true]); ?>
                        <?php echo e($passwordError); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="space-y-4">
                    <div>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model' => 'current_password','id' => 'current_password','name' => 'current_password','type' => 'password','label' => __('profile.current_password'),'placeholder' => __('profile.current_password_placeholder'),'required' => true,'autocomplete' => 'current-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'current_password','id' => 'current_password','name' => 'current_password','type' => 'password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.current_password')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.current_password_placeholder')),'required' => true,'autocomplete' => 'current-password']); ?>
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

                    <div>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model' => 'password','id' => 'password','name' => 'password','type' => 'password','label' => __('profile.new_password'),'placeholder' => __('profile.new_password_placeholder'),'required' => true,'autocomplete' => 'new-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password','id' => 'password','name' => 'password','type' => 'password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.new_password')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.new_password_placeholder')),'required' => true,'autocomplete' => 'new-password']); ?>
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
                        <p class="mt-2 text-xs text-slate-400">
                            <?php echo e(__('profile.password_requirements')); ?>

                        </p>
                    </div>

                    <div>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['wire:model' => 'password_confirmation','id' => 'password_confirmation','name' => 'password_confirmation','type' => 'password','label' => __('profile.confirm_password'),'placeholder' => __('profile.confirm_password_placeholder'),'required' => true,'autocomplete' => 'new-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password_confirmation','id' => 'password_confirmation','name' => 'password_confirmation','type' => 'password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.confirm_password')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.confirm_password_placeholder')),'required' => true,'autocomplete' => 'new-password']); ?>
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

                
                <div class="flex justify-end">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','wire:loading.attr' => 'disabled','wire:target' => 'updatePassword']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','wire:loading.attr' => 'disabled','wire:target' => 'updatePassword']); ?>
                        <span wire:loading.remove wire:target="updatePassword">
                            <?php echo e(__('profile.update_password')); ?>

                        </span>
                        <span wire:loading wire:target="updatePassword">
                            <?php echo e(__('profile.updating_password')); ?>

                        </span>
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
            </form>
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
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Listen for Livewire events and announce to screen readers
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('profile-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });

            Livewire.on('preferences-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });

            Livewire.on('password-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/staff/user-profile.blade.php ENDPATH**/ ?>