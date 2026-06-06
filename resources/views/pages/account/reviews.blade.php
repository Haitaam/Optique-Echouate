@extends('layouts.app')

@section('title', 'Mes avis')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mes avis</h1>
            <p class="text-heroi-text-muted">Gérez vos avis sur les produits que vous avez achetés.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-emerald-300 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20">
                <p class="text-red-300 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if ($reviews->isEmpty())
            <div class="glass-strong rounded-3xl p-8 text-center mb-8">
                <svg class="w-16 h-16 mx-auto text-heroi-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <p class="text-heroi-text-muted text-lg font-medium">Aucun avis pour le moment</p>
                <p class="text-heroi-text-muted/60 text-sm mt-2">Vous n'avez encore donné aucun avis.</p>
            </div>
        @else
            <div class="space-y-4 mb-8">
                @foreach ($reviews as $review)
                    <div class="glass-strong rounded-2xl p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-white font-semibold">{{ $review->product->name }}</h3>
                                <p class="text-xs text-heroi-text-muted">{{ $review->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-white/10' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                @endfor
                            </div>
                        </div>
                        @if ($review->title)
                            <h4 class="text-white text-sm font-medium mb-1">{{ $review->title }}</h4>
                        @endif
                        <p class="text-sm text-heroi-text-muted">{{ $review->body }}</p>
                        @if (!$review->is_approved)
                            <p class="text-xs text-amber-400 mt-2">En attente de modération</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if ($reviewableOrders->isNotEmpty())
            <div class="glass-strong rounded-3xl p-8">
                <h2 class="text-xl font-bold text-white mb-6">Donner un avis sur un produit</h2>
                <form method="POST" action="{{ route('account.reviews.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-heroi-text-muted mb-1.5">Commande</label>
                        <select name="order_id" id="review-order-select" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-orange-500/50">
                            <option value="">Sélectionner une commande</option>
                            @foreach ($reviewableOrders as $order)
                                <option value="{{ $order->id }}">Commande #{{ $order->id }} — {{ $order->created_at->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-heroi-text-muted mb-1.5">Produit</label>
                        <select name="product_id" id="review-product-select" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-orange-500/50">
                            <option value="">Sélectionner d'abord une commande</option>
                        </select>
                        @error('product_id')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-heroi-text-muted mb-1.5">Note</label>
                        <div class="flex items-center gap-1" x-data="{ rating: 0 }">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                    class="p-1 transition-colors">
                                    <svg :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-white/10'"
                                        class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating" value="0">
                        </div>
                        @error('rating')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-heroi-text-muted mb-1.5">Titre (optionnel)</label>
                        <input type="text" name="title" maxlength="255"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50">
                    </div>
                    <div>
                        <label class="block text-sm text-heroi-text-muted mb-1.5">Votre avis</label>
                        <textarea name="body" rows="4" required maxlength="2000"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50"></textarea>
                        @error('body')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/30">
                        Envoyer mon avis
                    </button>
                </form>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('review-order-select')?.addEventListener('change', function() {
    const orderId = this.value;
    const productSelect = document.getElementById('review-product-select');

    if (!orderId) {
        productSelect.innerHTML = '<option value="">Sélectionner d\'abord une commande</option>';
        return;
    }

    fetch('/account/orders/' + orderId + '/items')
        .then(r => r.json())
        .then(data => {
            productSelect.innerHTML = '<option value="">Choisir un produit</option>';
            if (data.items && Array.isArray(data.items)) {
                data.items.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.product_id || item.id;
                    opt.textContent = '#' + (item.product_id || item.id) + ' — ' + (item.name || 'Produit');
                    productSelect.appendChild(opt);
                });
            }
        })
        .catch(() => {
            productSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        });
});
</script>
@endpush
