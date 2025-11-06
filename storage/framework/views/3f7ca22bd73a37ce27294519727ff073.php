<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'ICTServe')); ?></title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100">
        <?php if (isset($component)) { $__componentOriginal8e96cb30d753cea0c0f163f6295be009 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e96cb30d753cea0c0f163f6295be009 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.skip-links','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.skip-links'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
        <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-announcements"></div>
        <div aria-live="assertive" aria-atomic="true" class="sr-only" id="aria-error-announcements"></div>
        
        <div class="min-h-screen flex flex-col bg-slate-950">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('navigation.portal-navigation', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-2642359099-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

            <main id="main-content" role="main" tabindex="-1" class="flex-1 py-6 focus:outline-none">
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                    <?php if(isset($slot)): ?>
                        <?php echo e($slot); ?>

                    <?php else: ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    <?php endif; ?>
                </div>
            </main>

            <footer class="border-t border-slate-800 bg-slate-900" role="contentinfo">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <p>&copy; <?php echo e(now()->year); ?> <?php echo e(__('footer.ministry_name')); ?>. <?php echo e(__('footer.all_rights_reserved')); ?>.</p>
                    <div class="flex items-center gap-4">
                        <span><?php echo e(__('footer.wcag_compliant')); ?></span>
                        <span aria-hidden="true">•</span>
                        <span><?php echo e(__('footer.pdpa_compliant')); ?></span>
                    </div>
                </div>
            </footer>
        </div>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/layouts/portal.blade.php ENDPATH**/ ?>