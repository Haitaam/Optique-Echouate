@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
$base = 'inline-flex items-center justify-center font-medium transition-all duration-300 rounded-full hover-lift focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-heroi-bg disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'hero-gradient text-white glow-orange',
    'secondary' => 'glass-strong text-white hover:bg-white/10',
    'ghost' => 'text-heroi-text-muted hover:text-white hover:bg-white/5',
    'outline' => 'border border-white/10 text-white hover:border-orange-500/50 hover:bg-orange-500/10',
];

$sizes = [
    'sm' => 'px-4 py-1.5 text-xs',
    'md' => 'px-6 py-2.5 text-sm',
    'lg' => 'px-8 py-3 text-base',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
