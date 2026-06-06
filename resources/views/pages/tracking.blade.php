@extends('layouts.app')

@section('title', 'Suivre ma commande')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Suivre ma commande</h1>
            <p class="text-heroi-text-muted">Connectez-vous et saisissez votre numéro de commande pour suivre son statut.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-emerald-300 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="glass-strong rounded-3xl p-6 sm:p-8">
            <form method="GET" action="{{ route('order.tracking') }}" class="space-y-4">
                <div>
                    <label for="order_id" class="block text-sm font-medium text-white mb-1.5">Numéro de commande</label>
                    <input type="text" name="order_id" id="order_id" value="{{ request('order_id') }}"
                        placeholder="Ex: 001"
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                </div>
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all shadow-lg shadow-orange-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Rechercher
                </button>
            </form>
        </div>

        @if(isset($order))
            <div class="mt-8 space-y-4">
                <div class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-emerald-300 font-medium">Commande trouvée</p>
                            <p class="text-emerald-200/60 text-sm">N° {{ $order->id }} — {{ $order->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div x-data="liveOrderStatus"
                     data-order-id="{{ $order->id }}"
                     data-status="{{ $order->status }}"
                     data-label="{{ $order->status_label }}"
                     data-badge-class="{{ $order->badge_class }}"
                     data-icon="{{ $order->status_icon }}"
                     class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-heroi-text-muted mb-1">Client</p>
                        <p class="text-sm text-white font-medium">{{ $order->name }}</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-heroi-text-muted mb-1">Téléphone</p>
                        <p class="text-sm text-white font-medium" dir="ltr">{{ $order->phone }}</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-heroi-text-muted mb-1">Statut</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                              :class="orderData.badgeClass"
                              x-text="(orderData.icon || '') + ' ' + orderData.label">
                        </span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-heroi-text-muted mb-1">Total</p>
                        <p class="text-sm font-bold hero-gradient-text">{{ number_format($order->total_price, 2, ',', ' ') }} MAD</p>
                    </div>
                </div>

                @if($order->city || $order->address)
                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                    <p class="text-xs text-heroi-text-muted mb-1">Adresse de livraison</p>
                    <p class="text-sm text-white">{{ $order->address }}{{ $order->city ? ', ' . $order->city : '' }}</p>
                </div>
                @endif

                @if($order->payment_method)
                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                    <p class="text-xs text-heroi-text-muted mb-1">Mode de paiement</p>
                    <p class="text-sm text-white font-medium">{{ $paymentMethods[$order->payment_method] ?? $order->payment_method }}</p>
                    @if($order->payment_method === 'bank_transfer')
                        <div class="mt-2 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-sm">
                            <p class="text-amber-300 font-medium mb-1">Informations de virement</p>
                            <p class="text-amber-200/70 text-xs leading-relaxed">
                                Banque : BMCE Bank (Bank of Africa)<br>
                                Titulaire : OPTIQUE ECHOUATE<br>
                                RIB : 011 130 00XXXXXXXXXXXXXXX<br>
                                Ville : Casablanca
                            </p>
                        </div>
                    @elseif($order->payment_method === 'card')
                        <div class="mt-2 p-3 rounded-lg bg-blue-500/10 border border-blue-500/20 text-sm">
                            <p class="text-blue-300 font-medium mb-1">Paiement par carte</p>
                            <p class="text-blue-200/70 text-xs leading-relaxed">
                                Le paiement par carte sera disponible prochainement.<br>
                                Pour l'instant, optez pour le paiement à la livraison.
                            </p>
                        </div>
                    @else
                        <div class="mt-2 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-sm">
                            <p class="text-emerald-300 font-medium">Paiement à la livraison</p>
                            <p class="text-emerald-200/70 text-xs mt-1">Vous paierez à la réception de votre commande.</p>
                        </div>
                    @endif
                </div>
                @endif

                @if(is_array($order->items) && count($order->items) > 0)
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                        <p class="text-xs text-heroi-text-muted mb-3">Articles commandés</p>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @if(!empty($item['image']))
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] ?? 'Article' }}" class="w-10 h-10 rounded-lg object-contain bg-white/5">
                                        @endif
                                        <div>
                                            <p class="text-sm text-white">{{ $item['name'] ?? 'Article #' . ($item['product_id'] ?? '') }}</p>
                                            <p class="text-xs text-heroi-text-muted">x{{ $item['quantity'] ?? 1 }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-white font-medium">{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2, ',', ' ') }} MAD</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($order->notes)
                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                    <p class="text-xs text-heroi-text-muted mb-1">Notes</p>
                    <p class="text-sm text-white">{{ $order->notes }}</p>
                </div>
                @endif

                @if($order->payment_method === 'cod' && !in_array($order->status, ['delivered', 'cancelled']))
                <a href="https://wa.me/212600092985?text=Bonjour%2C%20je%20souhaite%20confirmer%20ma%20commande%20%23{{ $order->id }}"
                    target="_blank"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-500 text-white hover:bg-emerald-400 text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Confirmer par WhatsApp
                </a>
                @endif

                @if(!in_array($order->status, ['cancelled', 'delivered']))
                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                    <p class="text-xs text-heroi-text-muted mb-4">Progression</p>
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
                    <div class="flex items-center gap-1">
                        @foreach($steps as $statusKey => $stepLabel)
                            @php
                                $stepIndex = array_search($statusKey, array_keys($steps));
                                $isComplete = $stepIndex <= $currentIndex;
                                $isCurrent = $stepIndex === $currentIndex;
                            @endphp
                            <div class="flex-1 flex flex-col items-center">
                                <div class="flex items-center w-full">
                                    <div class="h-1 flex-1 rounded-full {{ $stepIndex <= $currentIndex ? 'bg-emerald-500' : 'bg-white/10' }} {{ $stepIndex === 0 ? 'rounded-l-full' : '' }} {{ $stepIndex === count($steps) - 1 ? 'rounded-r-full' : '' }}"></div>
                                </div>
                                <div class="flex flex-col items-center mt-1.5">
                                    <span class="w-3 h-3 rounded-full {{ $isComplete ? 'bg-emerald-400' : ($isCurrent ? 'bg-amber-400 animate-pulse' : 'bg-white/20') }} shadow-lg {{ $isComplete ? 'shadow-emerald-500/30' : '' }}"></span>
                                    <span class="text-xs mt-1 {{ $isComplete ? 'text-emerald-300' : ($isCurrent ? 'text-white' : 'text-heroi-text-muted') }} text-center leading-tight">{{ $stepLabel }}</span>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="w-1"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @elseif($order->status === 'delivered')
                <div class="bg-white/5 rounded-xl p-4 border border-emerald-500/20 text-center">
                    <p class="text-emerald-300 font-medium text-lg">✅ Livrée</p>
                    <p class="text-heroi-text-muted text-sm mt-1">Votre commande a été livrée avec succès. Merci de votre confiance !</p>
                </div>
                @elseif($order->status === 'cancelled')
                <div class="bg-white/5 rounded-xl p-4 border border-red-500/20 text-center">
                    <p class="text-red-300 font-medium text-lg">🔴 Annulée</p>
                    <p class="text-heroi-text-muted text-sm mt-1">Cette commande a été annulée. Contactez-nous pour plus d'informations.</p>
                </div>
                @endif
            </div>
        @elseif(isset($notFound) && $notFound)
            <div class="mt-8 p-6 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-amber-300 font-medium">Commande non trouvée</p>
                <p class="text-amber-200/70 text-sm mt-1">Vérifiez le numéro de commande et le téléphone, puis réessayez.</p>
            </div>
        @endif

        <div class="mt-8 text-center">
            <p class="text-heroi-text-muted text-sm">
                Vous avez besoin d'aide ?
                <a href="https://wa.me/212600092985" target="_blank" class="text-orange-400 hover:text-orange-300 transition-colors">Contactez-nous sur WhatsApp</a>
            </p>
            <p class="text-heroi-text-muted text-sm mt-2">
                <a href="{{ route('account.orders') }}" class="text-orange-400 hover:text-orange-300 transition-colors">Voir toutes mes commandes</a>
            </p>
        </div>
    </div>
</section>
@endsection
