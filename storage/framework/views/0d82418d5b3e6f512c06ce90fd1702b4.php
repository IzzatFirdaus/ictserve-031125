

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'tabs' => [],
    'activeTab' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'tabs' => [],
    'activeTab' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'border-b border-gray-200 dark:border-gray-700'])); ?> role="tablist">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button
                type="button"
                role="tab"
                id="tab-<?php echo e($tab['id']); ?>"
                aria-selected="<?php echo e($activeTab === $tab['id'] ? 'true' : 'false'); ?>"
                aria-controls="panel-<?php echo e($tab['id']); ?>"
                wire:click="$set('activeTab', '<?php echo e($tab['id']); ?>')"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]',
                    'border-blue-500 text-blue-600 dark:text-blue-400' => $activeTab === $tab['id'],
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== $tab['id']
                ]); ?>"
            >
                <!--[if BLOCK]><![endif]--><?php if(isset($tab['icon'])): ?>
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <!--[if BLOCK]><![endif]--><?php switch($tab['icon']):
                            case ('home'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                <?php break; ?>
                            <?php case ('check-circle'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <?php break; ?>
                            <?php case ('clock'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <?php break; ?>
                            <?php case ('document-text'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                <?php break; ?>
                            <?php default: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                    </svg>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <span><?php echo e($tab['label']); ?></span>

                <!--[if BLOCK]><![endif]--><?php if(isset($tab['badge']) && $tab['badge'] > 0): ?>
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'ml-2 py-0.5 px-2 rounded-full text-xs font-medium',
                        'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' => $activeTab === $tab['id'],
                        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => $activeTab !== $tab['id']
                    ]); ?>">
                        <?php echo e($tab['badge']); ?>

                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </nav>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/navigation/tabs.blade.php ENDPATH**/ ?>