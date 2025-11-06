

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'footer' => null,
    'padding' => true,
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
    'title' => null,
    'footer' => null,
    'padding' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white rounded-lg shadow-md overflow-hidden <?php echo e($attributes->get('class', '')); ?>"
    <?php echo e($attributes->except('class')); ?>>
    <!--[if BLOCK]><![endif]--><?php if($title): ?>
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900"><?php echo e($title); ?></h2>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="<?php echo e($padding ? 'p-6' : ''); ?>">
        <?php echo e($slot); ?>

    </div>

    <!--[if BLOCK]><![endif]--><?php if($footer): ?>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <?php echo e($footer); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/ui/card.blade.php ENDPATH**/ ?>