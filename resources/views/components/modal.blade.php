@props([
    'open' => false,
    'title' => null,
    'maxWidth' => 'max-w-lg',
])

<div
    x-show="{{ $open }}"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[80] flex items-center justify-center p-4"
>
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="{{ $open }} = false"></div>
    <div
        class="relative glass-strong rounded-2xl w-full {{ $maxWidth }} p-6 shadow-2xl"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
    >
        @if ($title)
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
                <button @click="{{ $open }} = false" class="text-heroi-text-muted hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
