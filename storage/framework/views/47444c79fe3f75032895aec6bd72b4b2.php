

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'light']));

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

foreach (array_filter((['variant' => 'light']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentLocale = App::currentLocale();
    $languages = [
        'en' => [
            'label' => 'English',
            'abbr' => 'EN',
        ],
        'ms' => [
            'label' => 'Bahasa Melayu',
            'abbr' => 'MS',
        ],
    ];

    $buttonClasses = match ($variant) {
        'dark' => 'inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2 text-sm font-medium text-slate-200 bg-slate-800 border border-slate-700 rounded-md hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors duration-150',
        default => 'inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors duration-150',
    };

    $menuClasses = match ($variant) {
        'dark' => 'absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-slate-900 shadow-xl ring-1 ring-slate-700 focus:outline-none',
        default => 'absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none',
    };

    $baseOptionClasses = match ($variant) {
        'dark' => 'group flex items-center min-h-[44px] px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 hover:text-white focus:bg-slate-800 focus:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150',
        default => 'group flex items-center min-h-[44px] px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-600 transition-colors duration-150',
    };

    $activeOptionClasses = 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white';
?>

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @click.away="open = false" @keydown.escape.window="open = false">
    
    <button
        id="language-button"
        type="button"
        @click="open = !open"
        @keydown.enter="open = true"
        @keydown.space.prevent="open = true"
        @keydown.escape.window="open = false"
        class="<?php echo e($buttonClasses); ?> min-h-[48px] min-w-[48px] focus:ring-2 focus:ring-offset-2 focus:outline-none transition-colors duration-150"
        aria-haspopup="menu"
        :aria-expanded="open.toString()"
        aria-controls="language-menu"
        aria-label="<?php echo e(__('common.language_switcher')); ?>"
        title="<?php echo e(__('common.language_switcher')); ?>">
        
        <span>
            <?php echo e($languages[$currentLocale]['label'] ?? 'English'); ?>

        </span>
    </button>

    
    <div x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        id="language-menu"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="language-button"
        class="<?php echo e($menuClasses); ?>"
        style="display: none;">
        <div class="py-1" role="none">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('change-locale', $locale)); ?>"
                    @click="open = false"
                    @keydown.escape="open = false"
                    role="menuitemradio"
                    :aria-checked="<?php echo e($currentLocale === $locale ? 'true' : 'false'); ?>"
                    lang="<?php echo e($locale); ?>"
                    class="<?php echo e(trim($baseOptionClasses.' '.($currentLocale === $locale ? $activeOptionClasses : ''))); ?> min-h-[48px]"
                    <?php if($currentLocale === $locale): ?> aria-current="true" <?php endif; ?>>
                    <span class="font-medium"><?php echo e($language['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views/components/accessibility/language-switcher.blade.php ENDPATH**/ ?>