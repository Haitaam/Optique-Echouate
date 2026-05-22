@props(['shape' => 'oval', 'productImage' => null])

@php
$shapes = [
    'round' => 'M 120 80 C 120 35 170 20 220 80 C 270 140 270 200 220 250 C 170 300 120 285 120 240 C 120 200 70 140 120 80 Z',
    'oval' => 'M 170 60 C 200 30 250 40 270 90 C 290 140 290 200 270 240 C 250 280 200 290 170 260 C 140 230 110 160 130 110 C 150 60 140 50 170 60 Z',
    'square' => 'M 140 70 L 260 70 L 280 180 L 270 260 L 130 260 L 120 180 Z',
    'heart' => 'M 170 60 C 220 20 290 70 270 130 C 250 190 200 240 200 280 C 200 240 150 190 130 130 C 110 70 120 30 170 60 Z',
    'diamond' => 'M 200 40 L 280 150 L 200 270 L 120 150 Z',
];
$path = $shapes[$shape] ?? $shapes['oval'];
$colors = [
    'round' => ['fill' => '#fef3c7', 'stroke' => '#f59e0b'],
    'oval' => ['fill' => '#fce7f3', 'stroke' => '#ec4899'],
    'square' => ['fill' => '#dbeafe', 'stroke' => '#3b82f6'],
    'heart' => ['fill' => '#fce7f3', 'stroke' => '#ef4444'],
    'diamond' => ['fill' => '#ede9fe', 'stroke' => '#8b5cf6'],
];
$color = $colors[$shape] ?? $colors['oval'];
$label = ['round' => 'Rond', 'oval' => 'Ovale', 'square' => 'Carré', 'heart' => 'Cœur', 'diamond' => 'Diamant'][$shape] ?? $shape;
@endphp

<div {{ $attributes->merge(['class' => 'relative flex flex-col items-center']) }}>
    <svg viewBox="0 0 400 320" class="w-full h-auto max-w-[220px] mx-auto drop-shadow-lg">
        <defs>
            <filter id="glow-{{ $shape }}">
                <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                <feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge>
            </filter>
            <clipPath id="face-clip-{{ $shape }}">
                <path d="{{ $path }}"/>
            </clipPath>
        </defs>

        <path d="{{ $path }}" fill="{{ $color['fill'] }}" stroke="{{ $color['stroke'] }}" stroke-width="2.5" opacity="0.9"/>

        @if($productImage)
        <g clip-path="url(#face-clip-{{ $shape }})" opacity="0.35">
            <image href="{{ $productImage }}" x="60" y="40" width="280" height="200" preserveAspectRatio="xMidYMid slice"/>
        </g>
        @endif

        <line x1="120" y1="100" x2="280" y2="100" stroke="#d1d5db" stroke-width="0.5" stroke-dasharray="4,4" opacity="0.5"/>
        <line x1="100" y1="160" x2="300" y2="160" stroke="#d1d5db" stroke-width="0.5" stroke-dasharray="4,4" opacity="0.5"/>

        <ellipse cx="165" cy="85" rx="12" ry="10" fill="#1f2937" opacity="0.8"/>
        <ellipse cx="235" cy="85" rx="12" ry="10" fill="#1f2937" opacity="0.8"/>
        <circle cx="165" cy="85" r="3" fill="white" opacity="0.7"/>
        <circle cx="235" cy="85" r="3" fill="white" opacity="0.7"/>

        <path d="M 180 140 Q 200 155 220 140" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>

        @if($productImage)
        <g filter="url(#glow-{{ $shape }})" opacity="0.85">
            <path d="M 130 75 L 140 60 Q 150 50 170 55 L 180 65" fill="none" stroke="#1f2937" stroke-width="4" stroke-linecap="round" opacity="0.3"/>
            <path d="M 270 75 L 260 60 Q 250 50 230 55 L 220 65" fill="none" stroke="#1f2937" stroke-width="4" stroke-linecap="round" opacity="0.3"/>
            <rect x="130" y="65" width="140" height="45" rx="8" fill="none" stroke="#f59e0b" stroke-width="3" opacity="0.5" filter="url(#glow-{{ $shape })"/>
        </g>
        @endif

        <text x="200" y="305" text-anchor="middle" font-family="system-ui" font-size="16" font-weight="600" fill="#374151">{{ $label }}</text>
    </svg>
</div>
