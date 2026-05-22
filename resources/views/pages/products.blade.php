@extends('layouts.app')

@section('title', 'Boutique — Optique Échouate')

@section('content')
    <div class="pt-24 sm:pt-28 pb-20" x-data="productFilter" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-white">{{ __('messages.products.title') }}</h1>
                <p class="mt-2 text-heroi-text-muted">{{ __('messages.products.subtitle') }}</p>
            </div>

            <div class="glass-strong rounded-2xl p-4 sm:p-6 mb-8">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            x-model="filters.search"
                            placeholder="{{ __('messages.products.search_placeholder') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-10 py-2.5 text-sm text-white placeholder-heroi-text-muted focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition-all duration-200"
                        >
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-heroi-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button x-show="filters.search" @click="filters.search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-heroi-text-muted hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:flex lg:flex-wrap gap-3">
                        <x-select model="filters.brand" :placeholder="__('messages.products.all_brands')" :options="$brands->mapWithKeys(fn($b) => [$b => $b])"/>
                        <x-select model="filters.color" :placeholder="__('messages.products.all_colors')" :options="$colors->mapWithKeys(fn($c) => [$c => $c])"/>
                        <x-select model="filters.frame_shape" :placeholder="__('messages.products.all_shapes')" :options="$shapes->mapWithKeys(fn($s) => [$s => $s])"/>
                        <x-select model="filters.gender" :placeholder="__('messages.products.all_genders')" :options="$genders->mapWithKeys(fn($g) => [$g => $g])"/>
                        <x-select model="filters.material" :placeholder="__('messages.products.all_materials')" :options="$materials->mapWithKeys(fn($m) => [$m => $m])"/>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                    <div class="text-sm text-heroi-text-muted">
                        <span x-text="document.querySelectorAll('#product-grid [data-product]').length || '...'"></span> {{ __('messages.products.products_count') }}
                    </div>
                    <button @click="filters = { brand: '', color: '', frame_shape: '', gender: '', material: '', price_min: '', price_max: '', search: '' }; fetchProducts()" class="text-xs text-orange-400 hover:text-orange-300 transition-colors">
                        {{ __('messages.products.clear_filters') }}
                    </button>
                </div>
            </div>

            <div id="product-grid" class="relative">
                <div x-show="loading" x-cloak class="absolute inset-0 z-10 flex items-center justify-center bg-heroi-bg/60 backdrop-blur-sm rounded-2xl">
                    <div class="flex items-center gap-3 text-heroi-text-muted">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span class="text-sm">{{ __('messages.products.updating') }}</span>
                    </div>
                </div>
                @include('partials.product-grid', ['products' => $products])
            </div>
        </div>
    </div>
@endsection
