

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'helpText' => '',
    'autocomplete' => '',
    'minlength' => null,
    'maxlength' => null,
    'pattern' => null,
    'hideLabel' => false,
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
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'helpText' => '',
    'autocomplete' => '',
    'minlength' => null,
    'maxlength' => null,
    'pattern' => null,
    'hideLabel' => false,
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

    $inputClasses =
        'block w-full rounded-md shadow-sm transition-colors duration-200 min-h-[44px] px-4 py-2 text-base ' .
        ($hasError
            ? 'border-danger text-red-900 placeholder-red-300 focus:outline-none focus:ring-2 focus:ring-danger focus:border-danger'
            : 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none');
?>

<div class="mb-4">
    
    <!--[if BLOCK]><![endif]--><?php if(isset($label)): ?>
    <label for="<?php echo e($inputId); ?>" class="block text-sm font-medium text-gray-700 mb-2 <?php if($hideLabel): ?> sr-only <?php endif; ?>">
        <?php echo e($label); ?>

        <!--[if BLOCK]><![endif]--><?php if($required): ?>
            <span class="text-danger" aria-label="<?php echo e(__('required')); ?>">*</span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($helpText): ?>
        <p id="<?php echo e($helpId); ?>" class="text-sm text-gray-600 mb-2">
            <?php echo e($helpText); ?>

        </p>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <input type="<?php echo e($type); ?>" name="<?php echo e($name); ?>" id="<?php echo e($inputId); ?>" value="<?php echo e(old($name, $value)); ?>"
        class="<?php echo e($inputClasses); ?>" <?php if($required): ?> required aria-required="true" <?php endif; ?>
        <?php if($disabled): ?> disabled <?php endif; ?> <?php if($readonly): ?> readonly <?php endif; ?>
        <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
        <?php if($autocomplete): ?> autocomplete="<?php echo e($autocomplete); ?>" <?php endif; ?>
        <?php if($minlength): ?> minlength="<?php echo e($minlength); ?>" <?php endif; ?>
        <?php if($maxlength): ?> maxlength="<?php echo e($maxlength); ?>" <?php endif; ?>
        <?php if($pattern): ?> pattern="<?php echo e($pattern); ?>" <?php endif; ?>
        <?php if($hasError): ?> aria-invalid="true" aria-describedby="<?php echo e($errorId); ?>" <?php endif; ?>
        <?php if($helpId && !$hasError): ?> aria-describedby="<?php echo e($helpId); ?>" <?php endif; ?>
        <?php echo e($attributes->except(['id', 'class'])); ?> />

    
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
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/form/input.blade.php ENDPATH**/ ?>