@extends('layouts.app')

@section('title', 'Optique Échouate — Lunettes Premium')

@section('content')
    @include('partials.hero')

    {{-- Featured Collection --}}
    <section class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white">{{ __('messages.featured.title') }}</h2>
                    <p class="mt-2 text-heroi-text-muted">{{ __('messages.featured.subtitle') }}</p>
                </div>
                <x-button href="{{ route('products.index') }}" variant="ghost" size="sm">
                    {{ __('messages.featured.view_all') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </x-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" x-data="productGrid" @mouseleave="clearHovered()">
                @foreach ($featured as $product)
                    @php $_encoded = base64_encode($product->toJson()) @endphp
                    <div
                        class="relative transition-all duration-500 ease-out cursor-pointer"
                        @mouseenter="setHovered({{ $product->id }})"
                        @touchstart="setHovered({{ $product->id }}); setTimeout(() => clearHovered(), 3000)"
                        :class="hoveredId === {{ $product->id }} ? 'z-20 sm:scale-105 sm:-translate-y-2' : 'z-10 scale-100 translate-y-0'"
                        onclick="window.dispatchEvent(new CustomEvent('product-detail-open', {detail: JSON.parse(atob('{{ $_encoded }}'))}))"
                    >
                        <div
                            class="relative overflow-hidden rounded-2xl transition-all duration-500 ease-out"
                            :class="hoveredId === {{ $product->id }}
                                ? 'shadow-2xl shadow-orange-500/20 ring-1 ring-orange-500/30 bg-white/5'
                                : 'shadow-lg shadow-black/20 bg-white/[0.02] hover:bg-white/[0.04]'"
                        >
                            <div class="product-image-wrap">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-all duration-500 ease-out flex flex-col justify-end p-5"
                                :class="hoveredId === {{ $product->id }} ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                                <div class="space-y-2 translate-y-2 transition-transform duration-500 ease-out"
                                    :class="hoveredId === {{ $product->id }} ? 'translate-y-0' : 'translate-y-4'">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-medium text-orange-300 bg-orange-500/20 px-2.5 py-0.5 rounded-full">{{ $product->brand }}</span>
                                        <span class="text-xs text-white/70 bg-white/10 px-2.5 py-0.5 rounded-full">{{ $product->frame_shape }}</span>
                                        <span class="text-xs text-white/70 bg-white/10 px-2.5 py-0.5 rounded-full">{{ $product->material }}</span>
                                    </div>
                                    <h3 class="text-white font-semibold text-base">{{ $product->name }}</h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-white">{{ number_format($product->price, 0, ',', ' ') }} MAD</span>
                                        <span class="text-xs text-orange-300 bg-orange-500/20 px-3 py-1 rounded-full">{{ $product->color }}</span>
                                        <button onclick="event.stopPropagation(); window.addToCart('{{ $_encoded }}')"
                                            class="px-3 py-1.5 rounded-lg bg-orange-500/20 border border-orange-500/30 text-orange-300 hover:bg-orange-500/30 hover:text-orange-200 text-xs font-medium transition-all flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Ajouter
                                        </button>
                                        <button onclick="event.stopPropagation(); window.openFacePreview('{{ $_encoded }}', {
                                            round: {percentage: 85, explanation: 'Convient parfaitement aux visages ronds'},
                                            oval: {percentage: 90, explanation: 'Idéal pour les visages ovales'},
                                            square: {percentage: 70, explanation: 'Bon équilibre pour les visages carrés'},
                                            heart: {percentage: 75, explanation: 'Flatte les visages en cœur'},
                                            diamond: {percentage: 65, explanation: 'Complète les visages en diamant'},
                                        })"
                                            class="text-xs text-purple-300 hover:text-purple-200 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Essayer
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 sm:p-5 transition-all duration-500 ease-out"
                                :class="hoveredId === {{ $product->id }} ? 'opacity-0' : 'opacity-100'">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs text-heroi-text-muted uppercase tracking-wider">{{ $product->brand }}</span>
                                    <span class="text-xs text-heroi-text-muted">{{ $product->frame_shape }}</span>
                                </div>
                                <h3 class="font-semibold text-white text-sm sm:text-base mb-1 truncate">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-base font-bold hero-gradient-text">{{ number_format($product->price, 0, ',', ' ') }} MAD</span>
                                    <span class="w-4 h-4 rounded-full border border-white/10 block flex-shrink-0" style="background: {{ match(strtolower($product->color)) { 'gold' => '#FFD700', 'rose gold' => '#B76E79', 'silver' => '#C0C0C0', 'gunmetal' => '#2C3539', 'black' => '#000', 'matte black' => '#1a1a1a', 'tortoise' => '#8B6914', 'crystal' => '#E8E8E8', 'white' => '#fff', 'blue' => '#3B82F6', 'red' => '#EF4444', default => '#f97316' } }}"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.services')

    {{-- CTA Section --}}
    <section class="py-20 relative">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass-strong rounded-3xl p-8 sm:p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/15 rounded-full blur-[80px]"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-amber-500/10 rounded-full blur-[80px]"></div>
                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">{{ __('messages.cta.title') }}</h2>
                    <p class="text-heroi-text-muted mb-8 max-w-md mx-auto">{{ __('messages.cta.subtitle') }}</p>
                    <x-button href="{{ route('appointments') }}" variant="primary" size="lg" class="shadow-2xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('messages.cta.button') }}
                    </x-button>
                </div>
            </div>
        </div>
    </section>

    @include('partials.testimonials')

    {{-- Visitor Opinion Form --}}
    <section class="py-16 relative">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-strong rounded-3xl p-6 sm:p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-orange-500/10 rounded-full blur-[60px]"></div>
                <div class="relative">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-2">Donnez votre avis</h2>
                    <p class="text-heroi-text-muted text-sm text-center mb-6">Votre opinion nous aide à nous améliorer</p>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('avis.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input type="text" name="name" placeholder="Votre nom *" value="{{ old('name') }}" required
                                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <input type="text" name="work" placeholder="Votre profession" value="{{ old('work') }}"
                                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                            </div>
                        </div>
                        <div>
                            <textarea name="text" rows="3" placeholder="Votre opinion *" required
                                class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all resize-none">{{ old('text') }}</textarea>
                            @error('text') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-center">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all shadow-lg shadow-orange-500/25">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Envoyer mon avis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
