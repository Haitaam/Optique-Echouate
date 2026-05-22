<section class="relative min-h-screen flex items-center overflow-hidden pt-16 sm:pt-20 select-none"
    x-data="heroParallax()"
    @mousemove="handleMouse($event)">

    {{-- Cinematic dark canvas --}}
    <div class="absolute inset-0 bg-heroi-bg"></div>

    {{-- Ambient light that tracks the cursor --}}
    <div class="absolute inset-0 transition-opacity duration-700 pointer-events-none"
        :style="`background: radial-gradient(800px circle at ${glowX}% ${glowY}%, rgba(249,115,22,0.06) 0%, transparent 70%)`">
    </div>

    {{-- Subtle top-down vignette --}}
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,transparent_40%,rgba(0,0,0,0.6)_100%)] pointer-events-none"></div>

    {{-- Floating dust particles --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden will-change-transform">
        <div class="particle-dust" style="top:8%;left:5%;animation-delay:0s;width:2px;height:2px;background:rgba(249,115,22,0.3)"></div>
        <div class="particle-dust" style="top:20%;left:55%;animation-delay:1.2s;width:1.5px;height:1.5px;background:rgba(255,255,255,0.2)"></div>
        <div class="particle-dust" style="top:65%;left:12%;animation-delay:2.8s;width:2.5px;height:2.5px;background:rgba(249,115,22,0.2)"></div>
        <div class="particle-dust" style="top:35%;left:88%;animation-delay:0.6s;width:1px;height:1px;background:rgba(255,255,255,0.15)"></div>
        <div class="particle-dust" style="top:75%;left:70%;animation-delay:3.5s;width:2px;height:2px;background:rgba(249,115,22,0.25)"></div>
        <div class="particle-dust" style="top:12%;left:92%;animation-delay:1.8s;width:1.5px;height:1.5px;background:rgba(255,255,255,0.1)"></div>
        <div class="particle-dust" style="top:50%;left:3%;animation-delay:4.2s;width:2px;height:2px;background:rgba(249,115,22,0.15)"></div>
        <div class="particle-dust" style="top:85%;left:40%;animation-delay:0.3s;width:1px;height:1px;background:rgba(255,255,255,0.12)"></div>
    </div>

    {{-- Subtle grid overlay (Apple-like) --}}
    <div class="absolute inset-0 opacity-[0.015] pointer-events-none"
        style="background-image:linear-gradient(rgba(255,255,255,0.08) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.08) 1px,transparent 1px);background-size:72px 72px">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 w-full">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center min-h-[70vh]">

            {{-- LEFT: Text + CTA --}}
            <div class="order-2 lg:order-1 pt-8 lg:pt-0"
                :style="scrollTransform">
                <div x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 200)"
                    :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-1000 ease-out will-change-transform">

                    {{-- Premium badge --}}
                    <div class="inline-flex items-center gap-2.5 mb-6 px-4 py-1.5 rounded-full border border-white/[0.06] hero-badge-shimmer"
                        style="background:linear-gradient(135deg,rgba(255,255,255,0.03),rgba(249,115,22,0.04))">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-400"></span>
                        </span>
                        <span class="text-[11px] uppercase tracking-[0.15em] text-heroi-text-muted font-medium">{{ __('messages.hero.badge') }}</span>
                    </div>

                    {{-- Main headline --}}
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold leading-[1.05] tracking-tight">
                        <span class="text-white block">{{ __('messages.hero.title_1') }}</span>
                        <span class="hero-gradient-text block mt-1">{{ __('messages.hero.title_2') }}</span>
                    </h1>

                    {{-- Subtitle --}}
                    <p class="mt-5 text-base sm:text-lg text-heroi-text-muted/80 max-w-lg leading-relaxed font-light">
                        {{ __('messages.hero.subtitle') }}
                    </p>

                    {{-- CTA buttons --}}
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('quiz') }}"
                            class="group relative inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-orange-500 text-white font-medium text-sm overflow-hidden transition-all duration-500 hover:bg-orange-400 hover:shadow-2xl hover:shadow-orange-500/30 active:scale-[0.98]">
                            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                            <svg class="w-4 h-4 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <span class="relative">{{ __('messages.hero.cta_quiz') }}</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="group inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-white/10 text-heroi-text-muted hover:text-white hover:border-white/20 text-sm font-medium transition-all duration-300 active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>{{ __('messages.hero.cta_shop') }}</span>
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="mt-10 flex items-center gap-8 sm:gap-12">
                        <div class="text-left">
                            <p class="text-xl sm:text-2xl font-bold text-white tracking-tight">200+</p>
                            <p class="text-[11px] uppercase tracking-[0.08em] text-heroi-text-muted/50 mt-0.5">{{ __('messages.hero.stat_1') }}</p>
                        </div>
                        <div class="w-px h-8 bg-white/5"></div>
                        <div class="text-left">
                            <p class="text-xl sm:text-2xl font-bold text-white tracking-tight">15K+</p>
                            <p class="text-[11px] uppercase tracking-[0.08em] text-heroi-text-muted/50 mt-0.5">{{ __('messages.hero.stat_2') }}</p>
                        </div>
                        <div class="w-px h-8 bg-white/5"></div>
                        <div class="text-left">
                            <p class="text-xl sm:text-2xl font-bold text-white tracking-tight">98%</p>
                            <p class="text-[11px] uppercase tracking-[0.08em] text-heroi-text-muted/50 mt-0.5">{{ __('messages.hero.stat_3') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: 3D Glasses Showcase --}}
            <div class="order-1 lg:order-2 flex items-center justify-center lg:justify-end"
                :style="scrollTransform">

                {{-- Hidden dataset for Alpine --}}
                @php
                    $heroJson = $heroProducts->map(fn($p) => [
                        'id' => $p->id,
                        'image' => $p->image,
                        'name' => $p->name,
                        'brand' => $p->brand,
                        'price' => number_format($p->price * 10, 0, ',', ' '),
                        'color' => $p->color,
                        'frame_shape' => $p->frame_shape,
                    ]);
                @endphp
                <div data-hero-products
                    data-products='{{ $heroJson->toJson() }}'
                    class="hidden"></div>

                <div class="relative w-full max-w-[440px] aspect-square perspective-container"
                    :style="{ transform: productTransform }">

                    {{-- Primary ambient glow --}}
                    <div class="absolute inset-0 rounded-full opacity-20 blur-[100px] transition-all duration-1000 hero-glow-pulse"
                        style="background: radial-gradient(circle at 50% 50%, rgba(249,115,22,0.35), rgba(249,115,22,0.1), transparent 70%);">
                    </div>

                    {{-- Secondary cool glow --}}
                    <div class="absolute inset-[10%] rounded-full opacity-10 blur-[80px] animate-hero-glow-2"
                        style="background: radial-gradient(circle at 50% 50%, rgba(139,92,246,0.3), transparent 70%); animation-delay: -3s;">
                    </div>

                    {{-- Glassmorphism ring --}}
                    <div class="absolute inset-[5%] rounded-full border border-white/[0.04] backdrop-blur-[1px]"
                        style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.02), transparent 70%);">
                    </div>

                    {{-- Floating glasses carousel --}}
                    <div class="absolute inset-[8%] flex items-center justify-center">
                        <div class="relative w-full h-full flex items-center justify-center">

                            {{-- Slides --}}
                            <template x-for="(product, idx) in products" :key="product.id">
                                <div x-show="currentProduct === idx"
                                    x-transition:enter.duration.700ms.opacity
                                    x-transition:leave.duration.500ms.opacity
                                    class="absolute inset-0 flex items-center justify-center"
                                    style="will-change: transform, opacity;">

                                    {{-- Product image with cinematic effects --}}
                                    <div class="relative w-full h-full flex items-center justify-center hero-image-stage">
                                        {{-- Spotlight cone --}}
                                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[80%] h-[60%] bg-gradient-to-b from-orange-500/8 to-transparent rounded-full blur-3xl pointer-events-none"></div>

                                        {{-- The glasses --}}
                                        <img :src="product.image"
                                            :alt="product.name"
                                            class="hero-glasses-img"
                                            loading="eager">

                                        {{-- Dynamic lens reflection --}}
                                        <div class="absolute inset-0 overflow-hidden rounded-[35%] pointer-events-none hero-lens-reflect">
                                            <div class="absolute inset-0 animate-lens-shimmer"
                                                style="background: linear-gradient(105deg, transparent 25%, rgba(255,255,255,0.06) 40%, rgba(255,255,255,0.12) 50%, rgba(255,255,255,0.06) 60%, transparent 75%);">
                                            </div>
                                        </div>

                                        {{-- Bottom shadow pool --}}
                                        <div class="hero-shadow-pool"></div>
                                    </div>
                                </div>
                            </template>

                            {{-- Fallback if no JS / SSR --}}
                            <div class="absolute inset-0 flex items-center justify-center"
                                x-show="!products.length || products.length === 0"
                                x-cloak>
                                @if ($heroProducts->isNotEmpty())
                                    @php $first = $heroProducts->first(); @endphp
                                    <div class="relative w-full h-full flex items-center justify-center hero-image-stage">
                                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[80%] h-[60%] bg-gradient-to-b from-orange-500/8 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                                        <img src="{{ $first->image }}" alt="{{ $first->name }}" class="hero-glasses-img" loading="eager">
                                        <div class="absolute inset-0 overflow-hidden rounded-[35%] pointer-events-none hero-lens-reflect">
                                            <div class="absolute inset-0 animate-lens-shimmer"
                                                style="background: linear-gradient(105deg, transparent 25%, rgba(255,255,255,0.06) 40%, rgba(255,255,255,0.12) 50%, rgba(255,255,255,0.06) 60%, transparent 75%);">
                                            </div>
                                        </div>
                                        <div class="hero-shadow-pool"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Orbital ring accents --}}
                    <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 400 400" fill="none">
                        <circle cx="200" cy="200" r="185" stroke="rgba(249,115,22,0.04)" stroke-width="0.5" stroke-dasharray="3 8" class="animate-hero-rotate-ring" style="transform-origin: center; animation-duration: 30s;" />
                        <circle cx="200" cy="200" r="165" stroke="rgba(255,255,255,0.03)" stroke-width="0.5" stroke-dasharray="1 6" class="animate-hero-rotate-ring" style="transform-origin: center; animation-duration: 22s; animation-direction: reverse;" />
                        <circle cx="200" cy="200" r="145" stroke="rgba(139,92,246,0.03)" stroke-width="0.5" stroke-dasharray="2 10" class="animate-hero-rotate-ring" style="transform-origin: center; animation-duration: 35s;" />
                    </svg>

                    {{-- Corner light accents --}}
                    <div class="absolute top-6 right-8 w-1.5 h-1.5 rounded-full bg-orange-400/30 animate-pulse" style="animation-delay:0.5s;animation-duration:3s;"></div>
                    <div class="absolute bottom-6 left-8 w-1.5 h-1.5 rounded-full bg-purple-400/25 animate-pulse" style="animation-delay:2s;animation-duration:4s;"></div>
                    <div class="absolute top-[30%] -right-2 w-1 h-1 rounded-full bg-orange-300/20 animate-ping" style="animation-delay:1s;animation-duration:5s;"></div>
                </div>
            </div>
        </div>

        {{-- Product carousel dots + navigation --}}
        <div class="flex items-center justify-center gap-4 mt-6 sm:mt-8"
            x-show="products.length > 1"
            :style="scrollTransform">
            <button @click="prevProduct()"
                class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center text-heroi-text-muted hover:text-white hover:border-white/20 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="flex items-center gap-2">
                <template x-for="(product, idx) in products" :key="'dot-'+product.id">
                    <button @click="selectProduct(idx)"
                        :class="currentProduct === idx
                            ? 'w-6 h-1.5 rounded-full bg-orange-400'
                            : 'w-1.5 h-1.5 rounded-full bg-white/20 hover:bg-white/40'"
                        class="transition-all duration-500 ease-out cursor-pointer">
                    </button>
                </template>
            </div>
            <button @click="nextProduct()"
                class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center text-heroi-text-muted hover:text-white hover:border-white/20 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 pointer-events-none"
        :style="{ opacity: Math.max(0, 1 - scrollY / 200) }">
        <span class="text-[10px] uppercase tracking-[0.25em] text-white/15 font-medium">Scroll</span>
        <div class="w-px h-10 bg-gradient-to-b from-white/15 to-transparent"></div>
    </div>
</section>
