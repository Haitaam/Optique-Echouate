<div
    x-data="pageLoader"
    x-init="init()"
    x-show="visible"
    x-transition:leave="transition-opacity duration-500 ease-out"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[99999] flex items-center justify-center"
    style="background: rgba(5, 5, 10, 0.88); backdrop-filter: blur(16px);"
>
    <div class="relative flex flex-col items-center gap-5">
        {{-- Outer ring glow --}}
        <div class="absolute w-24 h-24 rounded-full animate-loader-ring opacity-40"></div>

        {{-- Middle ring --}}
        <div class="absolute w-20 h-20 rounded-full border border-orange-500/10 animate-loader-ring-reverse"></div>

        {{-- Logo container --}}
        <div class="relative w-16 h-16 rounded-full bg-[#0a0a0a] border border-white/[0.06] flex items-center justify-center overflow-hidden shadow-2xl animate-loader-logo">
            <img src="/optique-echouate.png" alt="Optique Échouate"
                class="w-10 h-10 object-contain">
        </div>

        {{-- Brand text --}}
        <div class="flex flex-col items-center gap-1">
            <span class="text-sm font-semibold hero-gradient-text tracking-wide animate-loader-text">Optique Échouate</span>
            <span class="text-[10px] text-heroi-text-muted/40 uppercase tracking-[0.2em] animate-loader-text" style="animation-delay: 200ms">Premium Eyewear</span>
        </div>
    </div>
</div>
