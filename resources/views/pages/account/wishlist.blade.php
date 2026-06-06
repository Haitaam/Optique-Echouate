@extends('layouts.app')

@section('title', 'Mes favoris')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mes favoris</h1>
            <p class="text-heroi-text-muted">Les produits que vous avez ajoutés à vos favoris.</p>
        </div>

        @if ($items->isEmpty())
            <div class="glass-strong rounded-3xl p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-heroi-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <p class="text-heroi-text-muted text-lg font-medium">Aucun favori</p>
                <p class="text-heroi-text-muted/60 text-sm mt-2">Vous n'avez encore ajouté aucun produit à vos favoris.</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-6 px-6 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all">
                    Découvrir nos produits
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($items as $item)
                    <div class="glass-strong rounded-2xl overflow-hidden group relative">
                        <div class="product-image-wrap">
                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" loading="lazy">
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-heroi-text-muted uppercase tracking-wider">{{ $item->product->brand }}</span>
                                <span class="text-xs text-heroi-text-muted">{{ $item->product->frame_shape }}</span>
                            </div>
                            <h3 class="font-semibold text-white text-sm mb-1 truncate">{{ $item->product->name }}</h3>
                            <span class="text-base font-bold hero-gradient-text">{{ number_format($item->product->price, 2, ',', ' ') }} MAD</span>
                            <div class="flex items-center gap-2 mt-3">
                                <button onclick="window.addToCart('{{ base64_encode($item->product->toJson()) }}')"
                                    class="flex-1 px-3 py-2 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-xs font-semibold transition-all">
                                    Ajouter au panier
                                </button>
                                <form method="POST" action="{{ route('wishlist.destroy', $item->product) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-xl bg-white/5 border border-white/10 text-red-400 hover:text-red-300 hover:bg-white/10 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
