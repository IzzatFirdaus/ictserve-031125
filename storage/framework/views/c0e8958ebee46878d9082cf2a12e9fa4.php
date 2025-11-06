

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label',
    'options' => [],
    'value' => '',
    'required' => false,
    'disabled' => false,
    'placeholder' => null,
    'helpText' => '',
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
    'name',
    'label',
    'options' => [],
    'value' => '',
    'required' => false,
    'disabled' => false,
    'placeholder' => null,
    'helpText' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $inputId = $attributes->get('id', $name);
    $helpId = $helpText ? "{$inputId}-help" : null;
    $errorId = "{$inputId}-error";
    $hasError = $errors->has($name);

    $selectClasses =
        'block w-full rounded-md shadow-sm transition-colors duration-200 min-h-[44px] px-4 py-2 text-base ' .
        ($hasError
            ? 'border-danger text-red-900 focus:outline-none focus:ring-2 focus:ring-danger focus:border-danger'
            : 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none');
?>

<div class="mb-4">
    <!--[if BLOCK]><![endif]--><?php if(isset($label) && !($attributes->has('hide-label') && $attributes->get('hide-label') == true)): ?>
    <label for="<?php echo e($inputId); ?>" class="block text-sm font-medium text-gray-700 mb-2">
        <?php echo e($label); ?>

        <!--[if BLOCK]><![endif]--><?php if($required): ?>
            <span class="text-danger" aria-label="<?php echo e(__('required')); ?>">*</span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($helpText): ?>
        <p id="<?php echo e($helpId); ?>" class="text-sm text-gray-600 mb-2"><?php echo e($helpText); ?></p>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <select name="<?php echo e($name); ?>" id="<?php echo e($inputId); ?>" class="<?php echo e($selectClasses); ?>"
        <?php if($required): ?> required aria-required="true" <?php endif; ?>
        <?php if($disabled): ?> disabled <?php endif; ?>
        <?php if($hasError): ?> aria-invalid="true" aria-describedby="<?php echo e($errorId); ?>" <?php endif; ?>
        <?php if($helpId && !$hasError): ?> aria-describedby="<?php echo e($helpId); ?>" <?php endif; ?>
        <?php echo e($attributes->except(['id', 'class'])); ?>>
        <!--[if BLOCK]><![endif]--><?php if($placeholder): ?>
            <option value=""><?php echo e($placeholder); ?></option>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($optionValue); ?>" <?php echo e(old($name, $value) == $optionValue ? 'selected' : ''); ?>>
                <?php echo e($optionLabel); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </select>

    <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p id="<?php echo e($errorId); ?>" class="mt-2 text-sm text-danger" role="alert">
            <span class="font-medium"><?php echo e(__('Error:')); ?></span> <?php echo e($message); ?>

        </p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/form/select.blade.php ENDPATH**/ ?>