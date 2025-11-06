

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label',
    'value' => '1',
    'checked' => false,
    'required' => false,
    'disabled' => false,
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
    'value' => '1',
    'checked' => false,
    'required' => false,
    'disabled' => false,
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

    $isChecked = old($name, $checked) ? true : false;
?>

<div class="mb-4">
    <div class="flex items-start">
        <div class="flex items-center h-5 min-h-[44px]">
            <input type="checkbox" name="<?php echo e($name); ?>" id="<?php echo e($inputId); ?>" value="<?php echo e($value); ?>"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors duration-200"
                <?php if($isChecked): ?> checked <?php endif; ?>
                <?php if($required): ?> required aria-required="true" <?php endif; ?>
                <?php if($disabled): ?> disabled <?php endif; ?>
                <?php if($hasError): ?> aria-invalid="true" aria-describedby="<?php echo e($errorId); ?>" <?php endif; ?>
                <?php if($helpId && !$hasError): ?> aria-describedby="<?php echo e($helpId); ?>" <?php endif; ?>
                <?php echo e($attributes->except(['id', 'class'])); ?> />
        </div>
        <div class="ml-3 text-sm">
            <label for="<?php echo e($inputId); ?>" class="font-medium text-gray-700 cursor-pointer">
                <?php echo e($label); ?>

                <!--[if BLOCK]><![endif]--><?php if($required): ?>
                    <span class="text-danger" aria-label="<?php echo e(__('required')); ?>">*</span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </label>
            <!--[if BLOCK]><![endif]--><?php if($helpText): ?>
                <p id="<?php echo e($helpId); ?>" class="text-gray-600 mt-1"><?php echo e($helpText); ?></p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

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
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/form/checkbox.blade.php ENDPATH**/ ?>