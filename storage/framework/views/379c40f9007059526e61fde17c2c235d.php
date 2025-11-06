<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100">
                <?php echo e(__('Permohonan')); ?> <?php echo e($application->application_number); ?>

            </h1>
            <p class="text-slate-400"><?php echo e($application->purpose); ?></p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full bg-blue-900/30 px-3 py-1 text-sm font-medium text-blue-400 border border-blue-800">
                <?php echo e($application->status->label()); ?>

            </span>

            <!--[if BLOCK]><![endif]--><?php if($application->isGuestSubmission() && $application->applicant_email === auth()->user()->email): ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['wire:click' => 'claim']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'claim']); ?>
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
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','tag' => 'a','href' => ''.e(route('loan.authenticated.extend', $application)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','tag' => 'a','href' => ''.e(route('loan.authenticated.extend', $application)).'']); ?>
                <?php echo e(__('Mohon Lanjutan')); ?>

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
    </header>

    <section class="grid gap-6 lg:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'lg:col-span-2 space-y-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2 space-y-4']); ?>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Nama Pemohon')); ?></p>
                    <p class="text-base text-slate-100"><?php echo e($application->applicant_name); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Emel Pemohon')); ?></p>
                    <p class="text-base text-slate-100"><?php echo e($application->applicant_email); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Bahagian')); ?></p>
                    <p class="text-base text-slate-100"><?php echo e($application->division?->name ?? __('Tidak dinyatakan')); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Gred')); ?></p>
                    <p class="text-base text-slate-100"><?php echo e($application->grade); ?></p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Tarikh Mula')); ?></p>
                    <p class="text-base text-slate-100"><?php echo e($application->loan_start_date?->translatedFormat('d M Y')); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Tarikh Tamat')); ?></p>
                    <p class="text-base text-slate-100"><?php echo e($application->loan_end_date?->translatedFormat('d M Y')); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400"><?php echo e(__('Nilai Keseluruhan')); ?></p>
                    <p class="text-base text-slate-100">RM <?php echo e(number_format((float) $application->total_value, 2)); ?></p>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-slate-400"><?php echo e(__('Lokasi Penggunaan')); ?></p>
                <p class="text-base text-slate-100"><?php echo e($application->location); ?></p>
            </div>

            <div>
                <p class="text-sm font-medium text-slate-400"><?php echo e(__('Arahan Khas')); ?></p>
                <p class="text-base text-slate-100">
                    <?php echo e($application->special_instructions ?? __('Tiada arahan khas.')); ?>

                </p>
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
            <h2 class="text-lg font-semibold text-slate-100 mb-4"><?php echo e(__('Garis Masa')); ?></h2>

            <ol class="relative border-l border-emerald-800 pl-6 space-y-6">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border border-emerald-700 bg-slate-900">
                            <span class="class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'h-3 w-3 rounded-full',
                                'bg-emerald-500' => $event['completed'] || $event['current'],
                                'bg-slate-900 border border-emerald-700' => ! $event['completed'] && ! $event['current'],
                            ]); ?>""></span>
                        </span>

                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'rounded-lg border p-4 transition shadow-sm',
                            'border-emerald-800 bg-emerald-900/30' => $event['current'],
                            'border-slate-800 bg-slate-900/70 backdrop-blur-sm' => ! $event['current'],
                        ]); ?>">
                            <h3 class="text-sm font-semibold text-slate-100"><?php echo e($event['label']); ?></h3>
                            <p class="mt-2 text-sm text-slate-300"><?php echo e($event['description']); ?></p>
                            <!--[if BLOCK]><![endif]--><?php if($event['time']): ?>
                                <p class="mt-3 text-xs text-slate-500 uppercase tracking-wide">
                                    <?php echo e($event['time']); ?>

                                </p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </ol>
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

    <section class="grid gap-6 lg:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'lg:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2']); ?>
            <h2 class="text-lg font-semibold text-slate-100 mb-4"><?php echo e(__('Aset Dipinjam')); ?></h2>

            <div class="overflow-hidden rounded-lg border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Aset')); ?>

                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Kuantiti')); ?>

                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                <?php echo e(__('Nilai (RM)')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/70 backdrop-blur-sm">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $application->loanItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-100">
                                    <?php echo e($item->asset?->name ?? __('Aset Umum')); ?>

                                    <span class="block text-xs text-slate-400"><?php echo e($item->asset?->asset_tag); ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    <?php echo e($item->quantity); ?>

                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    <?php echo e(number_format((float) $item->total_value, 2)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-slate-400">
                                    <?php echo e(__('Tiada aset direkodkan.')); ?>

                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
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
            <h2 class="text-lg font-semibold text-slate-100 mb-4"><?php echo e(__('Maklumat Kelulusan')); ?></h2>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-400"><?php echo e(__('Pegawai Kelulusan')); ?></dt>
                    <dd class="text-slate-100"><?php echo e($application->approved_by_name ?? __('Sedang diproses')); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400"><?php echo e(__('Emel Pegawai')); ?></dt>
                    <dd class="text-slate-100"><?php echo e($application->approver_email ?? __('-')); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400"><?php echo e(__('Diluluskan Pada')); ?></dt>
                    <dd class="text-slate-100"><?php echo e($application->approved_at?->translatedFormat('d M Y, h:i A') ?? __('-')); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400"><?php echo e(__('Catatan Kelulusan')); ?></dt>
                    <dd class="text-slate-100"><?php echo e($application->approval_remarks ?? __('Tiada catatan tambahan.')); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400"><?php echo e(__('Status Semasa')); ?></dt>
                    <dd class="text-slate-100"><?php echo e($application->status->label()); ?></dd>
                </div>
            </dl>
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
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/livewire/loans/loan-details.blade.php ENDPATH**/ ?>