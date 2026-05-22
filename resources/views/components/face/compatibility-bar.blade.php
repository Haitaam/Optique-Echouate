@props(['percentage' => 0, 'label' => '', 'color' => 'orange'])

@php
$colors = match(true) {
    $percentage >= 80 => ['from' => '#22c55e', 'to' => '#16a34a', 'text' => 'text-green-600'],
    $percentage >= 60 => ['from' => '#f59e0b', 'to' => '#d97706', 'text' => 'text-amber-600'],
    $percentage >= 40 => ['from' => '#f97316', 'to' => '#ea580c', 'text' => 'text-orange-600'],
    default => ['from' => '#ef4444', 'to' => '#dc2626', 'text' => 'text-red-600'],
};
$colorFrom = $colors['from'];
$colorTo = $colors['to'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label)
    <div class="flex justify-between items-center mb-1.5">
        <span class="text-sm font-medium text-gray-300">{{ $label }}</span>
        <span class="text-sm font-bold {{ $colors['text'] }}">{{ $percentage }}%</span>
    </div>
    @endif
    <div class="w-full h-2.5 bg-white/10 rounded-full overflow-hidden backdrop-blur-sm">
        <div
            class="h-full rounded-full transition-all duration-1000 ease-out"
            style="width: {{ $percentage }}%; background: linear-gradient(90deg, {{ $colorFrom }}, {{ $colorTo }});"
            x-init="$el.style.width = '0%'; setTimeout(() => $el.style.width = '{{ $percentage }}%', 100)"
        ></div>
    </div>
</div>
