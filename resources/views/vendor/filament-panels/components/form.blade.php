@props(['method' => 'post'])
<form {{ $attributes->merge(['method' => $method, 'class' => 'space-y-6']) }}>
    {{ $slot }}
</form>
