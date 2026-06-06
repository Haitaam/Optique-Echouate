@extends('layouts.app')

@section('title', 'Admin — Avis produits')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-white">Avis produits</h1>
                <p class="text-sm text-heroi-text-muted mt-1">Gérez les avis des clients sur les produits.</p>
            </div>
            <span class="text-sm text-heroi-text-muted">{{ $reviews->total() }} avis</span>
        </div>

        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-emerald-300 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if ($reviews->isEmpty())
            <div class="admin-card p-12 text-center">
                <p class="text-heroi-text-muted">Aucun avis pour le moment.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($reviews as $review)
                    <div class="admin-card p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm font-semibold text-white">{{ $review->customer?->name ?? 'Anonyme' }}</span>
                                    <div class="flex items-center gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-white/10' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-heroi-text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if ($review->title)
                                    <h4 class="text-white text-sm font-medium mb-1">{{ $review->title }}</h4>
                                @endif
                                <p class="text-sm text-heroi-text-muted">{{ $review->body }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs text-heroi-text-muted">Produit: <a href="#" class="text-orange-400 hover:text-orange-300">{{ $review->product?->name ?? 'Supprimé' }}</a></span>
                                    @if ($review->order)
                                        <span class="text-xs text-heroi-text-muted">Commande #{{ $review->order_id }}</span>
                                    @endif
                                    @if (!$review->is_approved)
                                        <span class="text-xs text-amber-400">En attente</span>
                                    @else
                                        <span class="text-xs text-emerald-400">Approuvé</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if (!$review->is_approved)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white hover:bg-emerald-400 text-xs font-medium transition-all">
                                            Approuver
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer cet avis ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs font-medium transition-all">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection
