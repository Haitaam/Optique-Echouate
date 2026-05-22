@props([
    'label' => null,
    'type' => 'text',
    'model' => null,
    'error' => null,
])

<div>
    @if ($label)
        <label class="block text-sm font-medium text-heroi-text mb-1.5">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        x-model="{{ $model }}"
        {{ $attributes->merge(['class' => 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-heroi-text-muted focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition-all duration-200']) }}
    >
    @if ($error)
        <p class="mt-1 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
