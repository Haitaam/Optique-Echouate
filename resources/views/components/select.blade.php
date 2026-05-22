@props([
    'label' => null,
    'model' => null,
    'options' => [],
    'placeholder' => 'All',
])

<div>
    @if ($label)
        <label class="block text-sm font-medium text-heroi-text mb-1.5">{{ $label }}</label>
    @endif
    <select
        x-model="{{ $model }}"
        {{ $attributes->merge(['class' => 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-heroi-text-muted focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition-all duration-200 appearance-none cursor-pointer']) }}
    >
        <option value="" class="bg-[#1a1a3a]">{{ $placeholder }}</option>
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" class="bg-[#1a1a3a]">{{ $label }}</option>
        @endforeach
    </select>
</div>
