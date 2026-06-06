<div x-data="cart"
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false"
    data-checkout-error="{{ session('checkout_error') ? '1' : '0' }}"
    data-checkout-msg="{{ session('checkout_error') }}"
    class="fixed inset-0 z-[100]">
    <div x-show="open" x-transition.opacity.duration.200ms class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 h-full w-full max-w-md bg-heroi-bg-alt border-l border-white/10 shadow-2xl flex flex-col z-10">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
            <h2 class="text-lg font-semibold text-white">Mon Panier</h2>
            <button @click="open = false" class="p-1.5 text-heroi-text-muted hover:text-white transition-colors rounded-lg hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <template x-if="items.length === 0">
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <svg class="w-16 h-16 text-heroi-text-muted/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <p class="text-heroi-text-muted font-medium">Votre panier est vide</p>
                <p class="text-heroi-text-muted/60 text-sm mt-1">Découvrez notre collection et ajoutez vos montures préférées.</p>
                <a href="{{ route('products.index') }}" @click="open = false" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all">
                    Découvrir
                </a>
            </div>
        </template>

        <template x-if="items.length > 0">
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/5">
                        <div class="w-14 h-14 rounded-lg overflow-hidden bg-white/5 flex-shrink-0 flex items-center justify-center">
                            <img :src="item.image" :alt="item.name" class="max-w-full max-h-full object-contain mix-blend-multiply">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white font-medium truncate" x-text="item.name"></p>
                            <p class="text-xs text-heroi-text-muted" x-text="item.brand"></p>
                            <p class="text-sm font-bold hero-gradient-text mt-0.5" x-text="formatPrice(item.price)"></p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button @click="updateQuantity(item.id, item.quantity - 1)" class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white flex items-center justify-center text-sm transition-colors">−</button>
                            <span class="w-7 text-center text-sm text-white font-medium" x-text="item.quantity"></span>
                            <button @click="updateQuantity(item.id, item.quantity + 1)" class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white flex items-center justify-center text-sm transition-colors">+</button>
                        </div>
                        <button @click="remove(item.id)" class="p-1.5 text-heroi-text-muted hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="items.length > 0">
            <div class="border-t border-white/10 px-5 py-4 space-y-3">
                <template x-if="!showCheckoutForm">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-heroi-text-muted">Sous-total</span>
                            <span class="text-lg font-bold text-white" x-text="formatPrice(total)"></span>
                        </div>
                        <button @click="showCheckoutForm = true"
                            class="w-full py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all shadow-lg shadow-orange-500/25">
                            Commander
                        </button>
                        <button @click="open = false" class="w-full py-2.5 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                            Continuer mes achats
                        </button>
                    </div>
                </template>
                <template x-if="showCheckoutForm">
                    <form action="{{ route('checkout.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="items" :value="JSON.stringify(items.map(i => ({id: i.id, quantity: i.quantity})))">

                        <template x-if="checkoutError">
                            <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20">
                                <p class="text-sm text-red-300" x-text="checkoutError"></p>
                            </div>
                        </template>

                        <div>
                            <input type="text" name="name" required placeholder="Nom complet *"
                                class="w-full px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors">
                        </div>
                        <div>
                            <input type="tel" name="phone" required placeholder="Téléphone *"
                                class="w-full px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors">
                        </div>
                        <div>
                            <input type="text" name="city" required placeholder="Ville *"
                                class="w-full px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors">
                        </div>
                        <div>
                            <textarea name="address" required placeholder="Adresse complète *" rows="2"
                                class="w-full px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors resize-none"></textarea>
                        </div>
                        <div>
                            <input type="email" name="email" required placeholder="Email *"
                                class="w-full px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors">
                        </div>
                        <div>
                            <textarea name="notes" placeholder="Notes (optionnel)" rows="2"
                                class="w-full px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors resize-none"></textarea>
                        </div>

                        <input type="hidden" name="payment_method" value="bank_transfer">

                        <div class="pt-2 border-t border-white/5">
                            <p class="text-xs font-medium text-heroi-text-muted mb-2">Mode de paiement</p>
                            <div class="p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                <p class="text-sm text-amber-300 font-medium">Virement bancaire (BMCE Bank)</p>
                                <p class="text-xs text-amber-200/70 mt-0.5">Vous recevrez les informations de virement par email après confirmation de votre commande.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-sm text-heroi-text-muted">Total à payer</span>
                            <span class="text-lg font-bold text-white" x-text="formatPrice(total)"></span>
                        </div>
                        <button type="submit"
                            class="w-full py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/25">
                            Confirmer la commande
                        </button>
                        <button type="button" @click="showCheckoutForm = false"
                            class="w-full py-2 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                            Retour
                        </button>
                    </form>
                </template>
            </div>
        </template>
    </div>
</div>
