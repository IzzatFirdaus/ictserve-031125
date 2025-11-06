
<div class="space-y-6">
    
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100"><?php echo e(__('Tiket Saya')); ?></h1>
            <p class="text-slate-400"><?php echo e(__('Jejaki semua tiket helpdesk yang pernah dihantar.')); ?></p>
        </div>
    </header>

    
    <section aria-label="<?php echo e(__('Ringkasan Statistik')); ?>">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
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
                <div class="text-center">
                    <p class="text-2xl font-semibold text-slate-100"><?php echo e($this->ticketStats['total']); ?></p>
                    <p class="mt-1 text-xs text-slate-400"><?php echo e(__('Jumlah Tiket')); ?></p>
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
                <div class="text-center">
                    <p class="text-2xl font-semibold text-blue-400"><?php echo e($this->ticketStats['open']); ?></p>
                    <p class="mt-1 text-xs text-slate-400"><?php echo e(__('Aktif')); ?></p>
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
                <div class="text-center">
                    <p class="text-2xl font-semibold text-green-400"><?php echo e($this->ticketStats['resolved']); ?></p>
                    <p class="mt-1 text-xs text-slate-400"><?php echo e(__('Selesai')); ?></p>
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
                <div class="text-center">
                    <p class="text-2xl font-semibold text-purple-400"><?php echo e($this->ticketStats['guest']); ?></p>
                    <p class="mt-1 text-xs text-slate-400"><?php echo e(__('Tetamu')); ?></p>
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
                <div class="text-center">
                    <p class="text-2xl font-semibold text-orange-400"><?php echo e($this->ticketStats['authenticated']); ?></p>
                    <p class="mt-1 text-xs text-slate-400"><?php echo e(__('Disahkan')); ?></p>
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
    </section>

    
    <section aria-label="<?php echo e(__('Penapis dan Carian')); ?>">
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
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                
                <div class="lg:col-span-2">
                    <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['name' => 'search','wire:model.live.debounce.300ms' => 'search','label' => ''.e(__('Cari tiket')).'','placeholder' => ''.e(__('Cari nombor tiket, subjek, atau penerangan...')).'','ariaLabel' => ''.e(__('Cari tiket')).'','hideLabel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','wire:model.live.debounce.300ms' => 'search','label' => ''.e(__('Cari tiket')).'','placeholder' => ''.e(__('Cari nombor tiket, subjek, atau penerangan...')).'','aria-label' => ''.e(__('Cari tiket')).'','hide-label' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'statusFilter','wire:model.live' => 'statusFilter','label' => ''.e(__('Tapis mengikut status')).'','ariaLabel' => ''.e(__('Tapis mengikut status')).'','hideLabel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'statusFilter','wire:model.live' => 'statusFilter','label' => ''.e(__('Tapis mengikut status')).'','aria-label' => ''.e(__('Tapis mengikut status')).'','hide-label' => true]); ?>
                    <option value="all"><?php echo e(__('Semua Status')); ?></option>
                    <option value="open"><?php echo e(__('Aktif')); ?></option>
                    <option value="pending"><?php echo e(__('Menunggu Saya')); ?></option>
                    <option value="resolved"><?php echo e(__('Selesai')); ?></option>
                    <option value="closed"><?php echo e(__('Ditutup')); ?></option>
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

                
                <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'submissionTypeFilter','wire:model.live' => 'submissionTypeFilter','label' => ''.e(__('Tapis mengikut jenis penghantaran')).'','ariaLabel' => ''.e(__('Tapis mengikut jenis penghantaran')).'','hideLabel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'submissionTypeFilter','wire:model.live' => 'submissionTypeFilter','label' => ''.e(__('Tapis mengikut jenis penghantaran')).'','aria-label' => ''.e(__('Tapis mengikut jenis penghantaran')).'','hide-label' => true]); ?>
                    <option value="all"><?php echo e(__('Semua Jenis')); ?></option>
                    <option value="guest"><?php echo e(__('Tetamu')); ?></option>
                    <option value="authenticated"><?php echo e(__('Disahkan')); ?></option>
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

                
                <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'categoryFilter','wire:model.live' => 'categoryFilter','label' => ''.e(__('Tapis mengikut kategori')).'','ariaLabel' => ''.e(__('Tapis mengikut kategori')).'','hideLabel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'categoryFilter','wire:model.live' => 'categoryFilter','label' => ''.e(__('Tapis mengikut kategori')).'','aria-label' => ''.e(__('Tapis mengikut kategori')).'','hide-label' => true]); ?>
                    <option value=""><?php echo e(__('Semua Kategori')); ?></option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
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
            </div>

            
            <div class="mt-4 flex items-center gap-4 border-t border-slate-800 pt-4">
                <span class="text-sm font-medium text-slate-300"><?php echo e(__('Susun mengikut:')); ?></span>
                <div class="flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'sortBy','wire:model.live' => 'sortBy','class' => 'w-40','label' => ''.e(__('Pilih medan untuk susun')).'','ariaLabel' => ''.e(__('Pilih medan untuk susun')).'','hideLabel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sortBy','wire:model.live' => 'sortBy','class' => 'w-40','label' => ''.e(__('Pilih medan untuk susun')).'','aria-label' => ''.e(__('Pilih medan untuk susun')).'','hide-label' => true]); ?>
                        <option value="created_at"><?php echo e(__('Tarikh Dicipta')); ?></option>
                        <option value="updated_at"><?php echo e(__('Tarikh Kemaskini')); ?></option>
                        <option value="status"><?php echo e(__('Status')); ?></option>
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

                    <button wire:click="$set('sortDirection', '<?php echo e($sortDirection === 'asc' ? 'desc' : 'asc'); ?>')"
                        class="inline-flex items-center rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-300 shadow-sm transition-colors hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950"
                        aria-label="<?php echo e($sortDirection === 'asc' ? __('Susun menurun') : __('Susun menaik')); ?>">
                        <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </button>
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
    </section>

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success']); ?>
            <?php echo e(session('success')); ?>

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

    <?php if(session()->has('error')): ?>
        <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'error']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'error']); ?>
            <?php echo e(session('error')); ?>

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

    
    <section aria-label="<?php echo e(__('Senarai Tiket')); ?>">
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
            <div class="overflow-hidden rounded-lg border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Tiket')); ?>

                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Jenis')); ?>

                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Kategori')); ?>

                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Status')); ?>

                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Pegawai')); ?>

                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Kemaskini')); ?>

                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Tindakan')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/70 backdrop-blur-sm">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-4 text-sm text-slate-100">
                                    <div class="font-medium">
                                        <a href="<?php echo e(route('helpdesk.authenticated.ticket.show', $ticket)); ?>"
                                            class="text-blue-400 hover:text-blue-300">
                                            <?php echo e($ticket->ticket_number); ?>

                                        </a>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-400">
                                        <?php echo e($ticket->subject); ?>

                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('Dicipta pada')); ?> <?php echo e($ticket->created_at?->translatedFormat('d M Y')); ?>

                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <!--[if BLOCK]><![endif]--><?php if(is_null($ticket->user_id)): ?>
                                        <span
                                            class="inline-flex items-center rounded-full bg-purple-900/30 px-2.5 py-0.5 text-xs font-medium text-purple-400 border border-purple-800">
                                            <?php echo e(__('Tetamu')); ?>

                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center rounded-full bg-orange-900/30 px-2.5 py-0.5 text-xs font-medium text-orange-400 border border-orange-800">
                                            <?php echo e(__('Disahkan')); ?>

                                        </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-300">
                                    <?php echo e($ticket->category?->name ?? __('Tiada')); ?>

                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-400 border border-blue-800">
                                        <?php echo e(\Illuminate\Support\Str::headline($ticket->status)); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-300">
                                    <?php echo e($ticket->assignedUser?->name ?? __('Belum Ditugaskan')); ?>

                                </td>
                                <td class="px-4 py-4 text-sm text-slate-400">
                                    <?php echo e($ticket->updated_at?->diffForHumans()); ?>

                                </td>
                                <td class="px-4 py-4 text-right text-sm">
                                    <?php if(is_null($ticket->user_id) && $ticket->guest_email === auth()->user()->email): ?>
                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['wire:click' => 'claim('.e($ticket->id).')','size' => 'xs','variant' => 'secondary','ariaLabel' => ''.e(__('Tuntut tiket :number', ['number' => $ticket->ticket_number])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'claim('.e($ticket->id).')','size' => 'xs','variant' => 'secondary','aria-label' => ''.e(__('Tuntut tiket :number', ['number' => $ticket->ticket_number])).'']); ?>
                                            <?php echo e(__('Tuntut')); ?>

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
                                        <a href="<?php echo e(route('helpdesk.authenticated.ticket.show', $ticket)); ?>"
                                            class="text-blue-400 hover:text-blue-300"
                                            aria-label="<?php echo e(__('Lihat butiran tiket :number', ['number' => $ticket->ticket_number])); ?>">
                                            <?php echo e(__('Butiran')); ?>

                                        </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2"><?php echo e(__('Tiada tiket untuk paparan.')); ?></p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('Cuba ubah penapis atau carian anda.')); ?></p>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($this->tickets->hasPages()): ?>
                <div class="mt-4">
                    <?php echo e($this->tickets->links()); ?>

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
    </section>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/helpdesk/my-tickets.blade.php ENDPATH**/ ?>