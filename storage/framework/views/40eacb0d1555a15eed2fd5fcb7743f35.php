<?php

use Livewire\Volt\Component;

?>

<?php
    $user = auth()->user();
    $portalLinks = $this->getAvailableLinks();
    $isCurrentRoute = fn(string $route): bool => request()->routeIs($route);
?>

<header class="bg-slate-900 text-slate-100 border-b border-slate-800" role="banner" aria-label="<?php echo e(__('common.site_header')); ?>">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        
        <div class="flex items-center gap-8">
            
            <a href="<?php echo e(route('staff.dashboard')); ?>"
                class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 rounded-md transition-colors duration-150"
                wire:navigate
                aria-label="<?php echo e(__('common.site_home')); ?>">
                <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'h-8 w-auto text-slate-100']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-8 w-auto text-slate-100']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                <span class="text-lg font-semibold hidden sm:block"><?php echo e(config('app.name', 'ICTServe')); ?></span>
            </a>

            
            <nav id="sidebar-navigation"
                class="hidden md:flex items-center gap-6 text-sm font-medium"
                role="navigation"
                aria-label="<?php echo e(__('common.main_navigation')); ?>"
                tabindex="-1">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $portalLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route($link['route'])); ?>"
                        wire:navigate
                        class="px-1 pb-1 border-b-2 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 rounded-sm <?php echo e($isCurrentRoute($link['route']) ? 'border-blue-500 text-white font-semibold' : 'border-transparent text-slate-300 hover:text-white hover:border-slate-500'); ?>"
                        <?php if($isCurrentRoute($link['route'])): ?> aria-current="page" <?php endif; ?>>
                        <?php echo e($link['label']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </nav>
        </div>

        
        <div class="flex items-center gap-4" id="user-menu">
            
            <?php if (isset($component)) { $__componentOriginalaf52e745e6407b09b46e9ed30e99055a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf52e745e6407b09b46e9ed30e99055a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.accessibility.language-switcher','data' => ['variant' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('accessibility.language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'dark']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaf52e745e6407b09b46e9ed30e99055a)): ?>
<?php $attributes = $__attributesOriginalaf52e745e6407b09b46e9ed30e99055a; ?>
<?php unset($__attributesOriginalaf52e745e6407b09b46e9ed30e99055a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaf52e745e6407b09b46e9ed30e99055a)): ?>
<?php $component = $__componentOriginalaf52e745e6407b09b46e9ed30e99055a; ?>
<?php unset($__componentOriginalaf52e745e6407b09b46e9ed30e99055a); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                 <?php $__env->slot('trigger', null, []); ?> 
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-slate-800 text-slate-200 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors duration-150 min-h-[44px]"
                        aria-haspopup="menu"
                        aria-label="<?php echo e(__('staff.nav.user_menu')); ?>: ' . ($user?->name ?? __('common.user')) }}">
                        <span><?php echo e($user?->name ?? __('staff.nav.user_menu')); ?></span>
                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                 <?php $__env->endSlot(); ?>

                 <?php $__env->slot('content', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('staff.profile'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('staff.profile')),'wire:navigate' => true]); ?>
                        <?php echo e(__('staff.nav.profile')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route('logout')).'','onclick' => 'event.preventDefault(); this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('logout')).'','onclick' => 'event.preventDefault(); this.closest(\'form\').submit();']); ?>
                            <?php echo e(__('staff.nav.logout')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                    </form>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
        </div>
    </div>

    
    <nav x-data="{ mobileMenuOpen: false }"
        class="md:hidden border-t border-slate-800"
        role="navigation"
        aria-label="<?php echo e(__('common.mobile_navigation')); ?>">

        
        <button type="button"
            class="w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-slate-200 bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-colors duration-150 min-h-[44px]"
            @click="mobileMenuOpen = !mobileMenuOpen"
            :aria-expanded="mobileMenuOpen.toString()"
            aria-controls="mobile-menu"
            aria-label="<?php echo e(__('staff.nav.toggle_menu')); ?>">
            <span><?php echo e(__('staff.nav.menu')); ?></span>
            <svg class="h-5 w-5 transition-transform duration-150"
                :class="{ '-rotate-180': mobileMenuOpen }"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        
        <div id="mobile-menu"
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="bg-slate-900 border-t border-slate-800"
            style="display: none;">

            
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $portalLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($link['route'])); ?>"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex px-4 py-3 text-sm min-h-[44px] items-center focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150 <?php echo e($isCurrentRoute($link['route']) ? 'text-white bg-slate-800 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>"
                    <?php if($isCurrentRoute($link['route'])): ?> aria-current="page" <?php endif; ?>>
                    <?php echo e($link['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

            
            <a href="<?php echo e(route('staff.profile')); ?>"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="flex px-4 py-3 text-sm min-h-[44px] items-center text-slate-300 hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150">
                <?php echo e(__('staff.nav.profile')); ?>

            </a>

            
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="border-t border-slate-800">
                <?php echo csrf_field(); ?>
                <button type="submit"
                    class="w-full text-left px-4 py-3 text-sm min-h-[44px] flex items-center text-slate-300 hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150">
                    <?php echo e(__('staff.nav.logout')); ?>

                </button>
            </form>
        </div>
    </nav>
</header><?php /**PATH C:\XAMPP\htdocs\ictserve-031125\resources\views\livewire/navigation/portal-navigation.blade.php ENDPATH**/ ?>