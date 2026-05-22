@props([
    'padding' => 'p-5',
    'hover' => true,
])

<div {{ $attributes->merge(['class' => 'glass rounded-2xl overflow-hidden transition-all duration-300 ' . ($hover ? 'hover-lift glow-orange' : '') . ' ' . $padding]) }}>
    {{ $slot }}
</div>
