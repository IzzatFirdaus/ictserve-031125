@props([
'variant' => 'primary',
'size' => 'md',
])

@php
$baseClasses = 'inline-flex items-center font-medium rounded-full';

$variants = [
'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300',
'success' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-300',
'warning' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-300',
'danger' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-300',
'gray' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
];

$sizes = [
'sm' => 'px-2.5 py-0.5 text-xs',
'md' => 'px-3 py-0.5 text-sm',
'lg' => 'px-4 py-1 text-base',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
