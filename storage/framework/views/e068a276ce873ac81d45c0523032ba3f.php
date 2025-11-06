

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
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
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $baseClasses =
        'inline-flex items-center justify-center px-4 py-2 rounded-md font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-[3px] focus:ring-offset-2 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed';

    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-600',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
        'success' => 'bg-green-700 text-white hover:bg-green-800 focus:ring-green-700',
        'danger' => 'bg-danger-dark text-white hover:bg-red-800 focus:ring-red-700',
        'warning' => 'bg-orange-600 text-white hover:bg-orange-700 focus:ring-orange-600',
        'ghost' => 'bg-transparent text-blue-600 hover:bg-blue-50 focus:ring-blue-600',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
?>

<!--[if BLOCK]><![endif]--><?php if($href): ?>
    <a href="<?php echo e($href); ?>" class="<?php echo e($classes); ?>" <?php echo e($attributes); ?>>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>" class="<?php echo e($classes); ?>" <?php if($disabled || $loading): ?> disabled <?php endif; ?>
        <?php echo e($attributes); ?>>
        <!--[if BLOCK]><![endif]--><?php if($loading): ?>
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php echo e($slot); ?>

    </button>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/ui/button.blade.php ENDPATH**/ ?>