@extends('layouts.app')

@section('title', 'Commande #' . $order->id)

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        @if (session('order_confirmed'))
            <div class="mb-6 p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <svg class="w-12 h-12 mx-auto text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-emerald-300 text-lg font-semibold mb-1">Commande confirmée !</p>
                <p class="text-emerald-200/80 text-sm">{{ session('success') }}</p>
                @if (session('temp_password'))
                <div class="mt-3 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 inline-block text-left">
                    <p class="text-amber-300 text-xs font-medium mb-1">🔑 Compte client créé</p>
                    <p class="text-amber-200/70 text-xs">Un email avec vos identifiants vous a été envoyé.</p>
                    <p class="text-amber-200/70 text-xs mt-1">Mot de passe temporaire : <code class="text-white font-mono bg-white/10 px-2 py-0.5 rounded">{{ session('temp_password') }}</code></p>
                </div>
                @endif
            </div>
        @elseif (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-emerald-300 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mb-8">
            <a href="{{ route('account.orders') }}" class="text-orange-400 hover:text-orange-300 text-sm transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à mes commandes
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-white">Commande #{{ $order->id }}</h1>
                <p class="text-heroi-text-muted text-sm mt-1">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-medium {{ $order->badge_class }} w-fit">
                {{ $order->status_icon }} {{ $order->status_label }}
            </span>
        </div>

        @php
            $steps = [
                App\Models\Order::STATUS_PENDING => 'En attente de confirmation',
                App\Models\Order::STATUS_CONFIRMED => 'Confirmée',
                App\Models\Order::STATUS_PREPARING => 'En préparation',
                App\Models\Order::STATUS_SHIPPED => 'Expédiée',
                App\Models\Order::STATUS_DELIVERED => 'Livrée',
            ];
            $currentIndex = array_search($order->status, array_keys($steps));
            if ($currentIndex === false) $currentIndex = -1;
        @endphp

        @if(!in_array($order->status, ['cancelled', 'delivered']))
        <div class="glass-strong rounded-2xl p-6 mb-6">
            <h2 class="text-sm font-semibold text-white mb-5">Suivi de commande</h2>
            <div class="relative">
                @foreach($steps as $statusKey => $stepLabel)
                    @php
                        $stepIndex = array_search($statusKey, array_keys($steps));
                        $isComplete = $stepIndex <= $currentIndex;
                        $isCurrent = $stepIndex === $currentIndex;
                    @endphp
                    <div class="flex items-start gap-4 pb-6 last:pb-0 relative">
                        @if(!$loop->last)
                        <div class="absolute left-[11px] top-6 bottom-0 w-0.5 {{ $stepIndex < $currentIndex ? 'bg-emerald-500' : 'bg-white/10' }}"></div>
                        @endif
                        <div class="relative flex-shrink-0">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $isComplete ? 'bg-emerald-500' : ($isCurrent ? 'bg-amber-500' : 'bg-white/10') }}">
                                @if($isComplete)
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @elseif($isCurrent)
                                <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                                @else
                                <div class="w-2 h-2 bg-white/20 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5">
                            <p class="text-sm {{ $isComplete ? 'text-emerald-300' : ($isCurrent ? 'text-white font-medium' : 'text-heroi-text-muted') }}">{{ $stepLabel }}</p>
                            @if($isCurrent)
                            <p class="text-xs text-heroi-text-muted mt-0.5">En cours</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @elseif($order->status === 'delivered')
        <div class="glass-strong rounded-2xl p-6 mb-6 text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-emerald-300 text-lg font-semibold">Livrée avec succès</p>
            <p class="text-heroi-text-muted text-sm mt-1">Merci de votre confiance !</p>
        </div>
        @elseif($order->status === 'cancelled')
        <div class="glass-strong rounded-2xl p-6 mb-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-500/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-red-300 text-lg font-semibold">Commande annulée</p>
            <p class="text-heroi-text-muted text-sm mt-1">Contactez-nous pour plus d'informations.</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="glass-strong rounded-2xl p-5">
                <h3 class="text-xs font-semibold text-heroi-text-muted uppercase tracking-wider mb-3">Détails de la commande</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-heroi-text-muted">Numéro</span>
                        <span class="text-white font-medium">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-heroi-text-muted">Date</span>
                        <span class="text-white font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-heroi-text-muted">Paiement</span>
                        <span class="text-white font-medium">{{ $paymentMethods[$order->payment_method] ?? $order->payment_method }}</span>
                    </div>
                    @if($order->notes)
                    <div class="pt-2 border-t border-white/5">
                        <span class="text-heroi-text-muted text-sm block mb-1">Notes</span>
                        <span class="text-white text-sm">{{ $order->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="glass-strong rounded-2xl p-5">
                <h3 class="text-xs font-semibold text-heroi-text-muted uppercase tracking-wider mb-3">Adresse de livraison</h3>
                <div class="space-y-2">
                    <p class="text-white text-sm font-medium">{{ $order->name }}</p>
                    @if($order->phone)
                    <p class="text-heroi-text-muted text-sm">{{ $order->phone }}</p>
                    @endif
                    @if($order->email)
                    <p class="text-heroi-text-muted text-sm">{{ $order->email }}</p>
                    @endif
                    @if($order->address || $order->city)
                    <p class="text-heroi-text-muted text-sm">{{ $order->address }}{{ $order->city ? ', ' . $order->city : '' }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="glass-strong rounded-2xl overflow-hidden mb-6">
            <div class="p-5 border-b border-white/5">
                <h2 class="text-sm font-semibold text-white">Articles commandés</h2>
            </div>
            @php $detailItems = $order->orderItems ?? collect(); @endphp
            @if($detailItems->isNotEmpty())
                <div class="divide-y divide-white/5">
                    @foreach($detailItems as $item)
                    <div class="p-4 sm:p-5 flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-white/5 border border-white/5 flex-shrink-0 flex items-center justify-center overflow-hidden">
                            @if($item->product && $item->product->image)
                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="max-w-full max-h-full object-contain mix-blend-multiply">
                            @else
                            <svg class="w-8 h-8 text-heroi-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white font-medium truncate">{{ $item->product?->name ?? 'Article #' . $item->product_id }}</p>
                            @if($item->product?->brand)
                            <p class="text-xs text-heroi-text-muted">{{ $item->product->brand }}</p>
                            @endif
                            <p class="text-xs text-heroi-text-muted mt-1">Quantité : {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm text-heroi-text-muted">{{ number_format($item->price, 2, ',', ' ') }} MAD</p>
                            <p class="text-sm text-white font-semibold">{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} MAD</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="p-5 bg-white/[0.02] border-t border-white/5">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-white font-semibold">Total</span>
                        <span class="text-lg font-bold hero-gradient-text">{{ number_format($order->total_price, 2, ',', ' ') }} MAD</span>
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-heroi-text-muted text-sm">Aucun article trouvé.</div>
            @endif
        </div>

        @if($order->payment_method === 'bank_transfer')
        <div class="glass-strong rounded-2xl p-5 mb-6 border border-amber-500/20">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <p class="text-amber-300 font-medium text-sm mb-1">Informations de virement bancaire</p>
                    <p class="text-amber-200/70 text-xs leading-relaxed">
                        Banque : BMCE Bank (Bank of Africa)<br>
                        Titulaire : OPTIQUE ECHOUATE<br>
                        RIB : 011 130 00XXXXXXXXXXXXXXX<br>
                        Ville : Casablanca
                    </p>
                    <p class="text-amber-200/60 text-xs mt-2">Effectuez le virement du montant total et votre commande sera traitée dès réception.</p>
                </div>
            </div>
        </div>
        @endif

        <a href="https://wa.me/212600092985?text=Bonjour%2C%20je%20souhaite%20confirmer%20ma%20commande%20%23{{ $order->id }}"
            target="_blank"
            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-500 text-white hover:bg-emerald-400 text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Nous contacter sur WhatsApp
        </a>
    </div>
</section>
@endsection
