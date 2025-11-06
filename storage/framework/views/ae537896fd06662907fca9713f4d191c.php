<div class="space-y-6">
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100"><?php echo e(__('Rekod Permohonan Aset')); ?></h1>
            <p class="text-slate-400"><?php echo e(__('Semak semua permohonan pinjaman ICT anda termasuk status semasa.')); ?></p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'status','wire:model.live' => 'status','class' => 'sm:w-48','label' => ''.e(__('Status')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','wire:model.live' => 'status','class' => 'sm:w-48','label' => ''.e(__('Status')).'']); ?>
                <option value=""><?php echo e(__('Semua Status')); ?></option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = \App\Enums\LoanStatus::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($statusOption->value); ?>"><?php echo e($statusOption->label()); ?></option>
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

            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['name' => 'search','wire:model.live.debounce.300ms' => 'search','placeholder' => ''.e(__('Cari nombor permohonan atau tujuan...')).'','class' => 'sm:w-64']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','wire:model.live.debounce.300ms' => 'search','placeholder' => ''.e(__('Cari nombor permohonan atau tujuan...')).'','class' => 'sm:w-64']); ?>
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
    </header>

    <div class="overflow-hidden rounded-lg border border-slate-800">
        <table class="min-w-full divide-y divide-slate-800">
            <thead class="bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        <?php echo e(__('Permohonan')); ?>

                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        <?php echo e(__('Status')); ?>

                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        <?php echo e(__('Tempoh')); ?>

                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        <?php echo e(__('Bahagian')); ?>

                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-300">
                        <?php echo e(__('Tindakan')); ?>

                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 bg-slate-900/70 backdrop-blur-sm">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <div class="font-medium">
                                <a href="<?php echo e(route('loan.authenticated.show', $application)); ?>" class="text-blue-400 hover:text-blue-300">
                                    <?php echo e($application->application_number); ?>

                                </a>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">
                                <?php echo e($application->purpose); ?>

                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                <?php echo e(__('Dihantar pada')); ?> <?php echo e($application->created_at?->translatedFormat('d M Y')); ?>

                            </p>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <?php
                                $statusColor = match ($application->status->color()) {
                                    'green' => 'bg-emerald-900/30 text-emerald-400 border border-emerald-800',
                                    'blue' => 'bg-blue-900/30 text-blue-400 border border-blue-800',
                                    'yellow' => 'bg-amber-900/30 text-amber-400 border border-amber-800',
                                    'orange' => 'bg-orange-900/30 text-orange-400 border border-orange-800',
                                    'red' => 'bg-rose-900/30 text-rose-400 border border-rose-800',
                                    'purple' => 'bg-purple-900/30 text-purple-400 border border-purple-800',
                                    'teal' => 'bg-teal-900/30 text-teal-400 border border-teal-800',
                                    'amber' => 'bg-amber-900/30 text-amber-400 border border-amber-800',
                                    'lime' => 'bg-lime-900/30 text-lime-400 border border-lime-800',
                                    'emerald' => 'bg-emerald-900/30 text-emerald-400 border border-emerald-800',
                                    'gray' => 'bg-slate-800 text-slate-400 border border-slate-700',
                                    default => 'bg-slate-800 text-slate-400 border border-slate-700',
                                };
                            ?>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php echo e($statusColor); ?>">
                                <?php echo e($application->status->label()); ?>

                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <p><?php echo e($application->loan_start_date?->translatedFormat('d M Y')); ?></p>
                            <p class="text-xs text-slate-400">— <?php echo e($application->loan_end_date?->translatedFormat('d M Y')); ?></p>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <?php echo e($application->division?->name ?? __('Tidak dinyatakan')); ?>

                        </td>
                        <td class="px-4 py-4 text-right text-sm text-slate-100">
                            <!--[if BLOCK]><![endif]--><?php if(is_null($application->user_id) && $application->applicant_email === auth()->user()->email): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['size' => 'xs','variant' => 'secondary','wire:click' => 'claim('.e($application->id).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'xs','variant' => 'secondary','wire:click' => 'claim('.e($application->id).')']); ?>
                                    <?php echo e(__('Tuntut Permohonan')); ?>

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
                                <a href="<?php echo e(route('loan.authenticated.show', $application)); ?>" class="text-blue-400 hover:text-blue-300">
                                    <?php echo e(__('Lihat Butiran')); ?>

                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-400">
                            <?php echo e(__('Tiada permohonan ditemui.')); ?>

                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div>
        <?php echo e($this->applications->links()); ?>

    </div>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/loans/loan-history.blade.php ENDPATH**/ ?>