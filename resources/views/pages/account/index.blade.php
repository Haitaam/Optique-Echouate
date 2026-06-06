@extends('layouts.app')

@section('title', 'Mon compte')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mon compte</h1>
            <p class="text-heroi-text-muted">Bienvenue, {{ auth('customer')->user()->name }}.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-emerald-300 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('order_confirmed'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-emerald-300 text-sm font-medium">Votre commande a été confirmée ! Consultez son statut ci-dessous.</p>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('account.orders') }}" class="glass-strong rounded-2xl p-6 text-center hover:bg-white/[0.03] transition-all">
                <svg class="w-8 h-8 mx-auto text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-white font-medium">Mes commandes</p>
                <p class="text-heroi-text-muted text-sm">{{ $ordersCount }} commande(s)</p>
            </a>
            <a href="{{ route('account.reviews') }}" class="glass-strong rounded-2xl p-6 text-center hover:bg-white/[0.03] transition-all">
                <svg class="w-8 h-8 mx-auto text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <p class="text-white font-medium">Mes avis</p>
                <p class="text-heroi-text-muted text-sm">Donnez votre avis</p>
            </a>
            <a href="{{ route('account.addresses') }}" class="glass-strong rounded-2xl p-6 text-center hover:bg-white/[0.03] transition-all">
                <svg class="w-8 h-8 mx-auto text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-white font-medium">Mes adresses</p>
                <p class="text-heroi-text-muted text-sm">Gérer mes adresses</p>
            </a>
        </div>

        @if ($latestOrders->count() > 0)
        <div class="glass-strong rounded-3xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Dernières commandes</h2>
                <a href="{{ route('account.orders') }}" class="text-sm text-orange-400 hover:text-orange-300 transition-colors">Voir tout →</a>
            </div>
            <div class="space-y-3">
                @foreach ($latestOrders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                        <div>
                            <p class="text-sm text-white font-medium">#{{ $order->id }} — {{ $order->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-heroi-text-muted">{{ number_format($order->total_price, 2, ',', ' ') }} MAD · {{ $order->status_label }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $order->badge_class }}">
                            {{ $order->status_icon }} {{ $order->status_label }}
                        </span>
                    </a>
                @endforeach
            </div>
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
