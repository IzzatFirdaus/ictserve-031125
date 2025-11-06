

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'pending',
    'type' => 'default', // default, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'icon' => true,
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
    'status' => 'pending',
    'type' => 'default', // default, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'icon' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Status type mapping
    $statusTypes = [
        'success' => ['color' => 'success', 'icon' => '✓', 'text' => __('Success')],
        'approved' => ['color' => 'success', 'icon' => '✓', 'text' => __('Approved')],
        'active' => ['color' => 'success', 'icon' => '●', 'text' => __('Active')],
        'completed' => ['color' => 'success', 'icon' => '✓', 'text' => __('Completed')],

        'pending' => ['color' => 'warning', 'icon' => '⏱', 'text' => __('Pending')],
        'in_progress' => ['color' => 'warning', 'icon' => '◐', 'text' => __('In Progress')],
        'assigned' => ['color' => 'warning', 'icon' => '→', 'text' => __('Assigned')],

        'rejected' => ['color' => 'danger', 'icon' => '✕', 'text' => __('Rejected')],
        'declined' => ['color' => 'danger', 'icon' => '✕', 'text' => __('Declined')],
        'cancelled' => ['color' => 'danger', 'icon' => '✕', 'text' => __('Cancelled')],
        'overdue' => ['color' => 'danger', 'icon' => '⚠', 'text' => __('Overdue')],

        'draft' => ['color' => 'default', 'icon' => '○', 'text' => __('Draft')],
        'closed' => ['color' => 'default', 'icon' => '●', 'text' => __('Closed')],
    ];

    $currentStatus = $statusTypes[strtolower($status)] ?? $statusTypes['pending'];
    $colorType = $type !== 'default' ? $type : $currentStatus['color'];

    // WCAG 2.2 AA compliant color classes
    $colorClasses = [
        'success' => 'bg-green-100 text-green-800 border-green-300',
        'warning' => 'bg-orange-100 text-orange-900 border-orange-300',
        'danger' => 'bg-red-100 text-red-900 border-red-300',
        'info' => 'bg-blue-100 text-blue-800 border-blue-300',
        'default' => 'bg-gray-100 text-gray-800 border-gray-300',
    ];

    // Size classes
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];

    $baseClasses = 'inline-flex items-center gap-1.5 font-medium rounded-md border';
    $classes = implode(' ', [
        $baseClasses,
        $colorClasses[$colorType] ?? $colorClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ]);
?>

<span
    <?php echo e($attributes->merge(['class' => $classes])); ?>

    role="status"
    aria-label="<?php echo e($currentStatus['text']); ?>"
>
    <!--[if BLOCK]><![endif]--><?php if($icon): ?>
        <span aria-hidden="true" class="flex-shrink-0"><?php echo e($currentStatus['icon']); ?></span>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <span><?php echo e($slot->isEmpty() ? $currentStatus['text'] : $slot); ?></span>
</span>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/data/status-badge.blade.php ENDPATH**/ ?>