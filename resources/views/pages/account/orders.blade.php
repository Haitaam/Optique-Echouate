@extends('layouts.app')

@section('title', 'Mes commandes')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mes commandes</h1>
            <p class="text-heroi-text-muted">Consultez l'historique de vos commandes.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-emerald-300 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('temp_password'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center">
                <p class="text-amber-300 text-sm font-medium mb-1">🔑 Compte client créé</p>
                <p class="text-amber-200/80 text-xs">Un email avec vos identifiants vous a été envoyé.</p>
                <p class="text-amber-200/80 text-xs mt-1">Mot de passe temporaire : <code class="text-white font-mono bg-white/10 px-2 py-0.5 rounded text-sm">{{ session('temp_password') }}</code></p>
                <p class="text-amber-200/60 text-xs mt-1">Conservez-le pour vous connecter ultérieurement.</p>
            </div>
        @endif

        @if ($orders->count() > 0)
            <div class="space-y-3">
                @foreach ($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="glass-strong rounded-2xl p-5 block hover:bg-white/[0.03] transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="text-white font-bold text-lg">#{{ $order->id }}</span>
                                <span class="text-heroi-text-muted text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $order->badge_class }}">
                                {{ $order->status_icon }} {{ $order->status_label }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-heroi-text-muted">{{ ($order->order_items_count ?? 0) > 0 ? $order->order_items_count . ' article(s)' : '' }}</span>
                            <span class="text-white font-semibold">{{ number_format($order->total_price, 2, ',', ' ') }} MAD</span>
                        </div>
                        @if($order->payment_method)
                        <div class="mt-2 text-xs text-heroi-text-muted">
                            {{ \App\Http\Controllers\CheckoutController::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}
                        </div>
                        @endif
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="glass-strong rounded-3xl p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-heroi-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-heroi-text-muted text-lg font-medium">Aucune commande</p>
                <p class="text-heroi-text-muted/60 text-sm mt-2">Passez votre première commande pour la voir apparaître ici.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all">Découvrir la collection</a>
            </div>
        @endif
    </div>
</section>
@endsection
