@extends('layouts.app')

@section('title', 'Mes commandes')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative">
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
            <div class="grid gap-4">
                @foreach ($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="glass-strong rounded-2xl overflow-hidden hover:bg-white/[0.03] transition-all block">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-white font-bold text-lg">#{{ $order->id }}</span>
                                    <span class="text-heroi-text-muted text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $order->badge_class }}">
                                    {{ $order->status_icon }} {{ $order->status_label }}
                                </span>
                            </div>

                            @if($order->relationLoaded('orderItems') && $order->orderItems->isNotEmpty())
                            <div class="flex items-center gap-3 mb-3 overflow-x-auto pb-1">
                                @foreach($order->orderItems->take(4) as $item)
                                <div class="w-14 h-14 rounded-xl bg-white/5 border border-white/5 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                    @if($item->product && $item->product->image)
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="max-w-full max-h-full object-contain mix-blend-multiply">
                                    @else
                                    <svg class="w-6 h-6 text-heroi-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    @endif
                                </div>
                                @endforeach
                                @if($order->orderItems->count() > 4)
                                <div class="w-14 h-14 rounded-xl bg-white/5 border border-white/5 flex-shrink-0 flex items-center justify-center">
                                    <span class="text-xs text-heroi-text-muted font-medium">+{{ $order->orderItems->count() - 4 }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-heroi-text-muted">{{ $order->orderItems->sum('quantity') ?? $order->order_items_count }} article(s)</span>
                                <span class="text-white font-semibold">{{ number_format($order->total_price, 2, ',', ' ') }} MAD</span>
                            </div>
                            @if($order->payment_method)
                            <div class="mt-2 text-xs text-heroi-text-muted">
                                {{ \App\Http\Controllers\CheckoutController::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}
                            </div>
                            @endif
                        </div>
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
