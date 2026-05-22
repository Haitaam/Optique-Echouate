<div
    x-data="productDetail"
    x-show="open"
    x-cloak
    @keydown.escape.window="close()"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
>
    <div x-show="open" x-transition.opacity.duration.300ms class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="close()"></div>

    <div
        x-show="open"
        x-transition.duration.400ms
        class="relative z-10 w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl border border-white/20 shadow-2xl"
        style="background: linear-gradient(135deg, rgba(17,17,17,0.97), rgba(30,30,30,0.95)); backdrop-filter: blur(32px);"
    >
        <button @click="close()" class="absolute top-5 right-5 z-20 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="p-6 sm:p-8">
            <template x-if="product">
                <div>
                    {{-- Image --}}
                    <div class="product-image-modal rounded-2xl mb-6">
                        <img x-bind:src="product.image" x-bind:alt="product.name">
                    </div>

                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-xs text-orange-400 font-medium uppercase tracking-wider" x-text="product.brand"></span>
                            <h2 class="text-2xl font-bold text-white mt-0.5" x-text="product.name"></h2>
                        </div>
                        <span class="text-2xl font-bold hero-gradient-text" x-text="formatPrice(product.price)"></span>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-2 mb-5">
                        <template x-if="product.is_luxury">
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">Luxe</span>
                        </template>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-orange-500/10 text-orange-300 border border-orange-500/20" x-text="product.frame_shape"></span>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-white/10 text-gray-300 border border-white/10" x-text="product.material"></span>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-white/10 text-gray-300 border border-white/10" x-text="product.gender"></span>
                    </div>

                    {{-- Description --}}
                    <p class="text-sm text-gray-400 leading-relaxed mb-5" x-text="product.description"></p>

                    {{-- Color + specs grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-xs text-gray-500">Couleur</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="w-4 h-4 rounded-full border border-white/20 block" x-bind:style="'background: ' + colorSwatch(product.color)"></span>
                                <span class="text-sm font-medium text-white capitalize" x-text="product.color"></span>
                            </div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-xs text-gray-500">Matière</p>
                            <p class="text-sm font-medium text-white mt-1" x-text="product.material"></p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-xs text-gray-500">Forme</p>
                            <p class="text-sm font-medium text-white mt-1 capitalize" x-text="product.frame_shape"></p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-xs text-gray-500">Marque</p>
                            <p class="text-sm font-medium text-white mt-1" x-text="product.brand"></p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-xs text-gray-500">Genre</p>
                            <p class="text-sm font-medium text-white mt-1" x-text="product.gender"></p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <p class="text-xs text-gray-500">Prix</p>
                            <p class="text-sm font-bold text-orange-400 mt-1" x-text="formatPrice(product.price)"></p>
                        </div>
                    </div>

                    {{-- Style tags --}}
                    <template x-if="product.style_tags && product.style_tags.length">
                        <div class="mb-5">
                            <p class="text-xs text-gray-500 mb-2">Mots-clés</p>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="tag in product.style_tags" :key="tag">
                                    <span class="px-2.5 py-1 text-xs rounded-full bg-white/5 text-gray-400 border border-white/5" x-text="tag"></span>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Categories --}}
                    <template x-if="product.categories && product.categories.length">
                        <div class="flex flex-wrap gap-2 pt-4 border-t border-white/5">
                            <template x-for="cat in product.categories" :key="cat.id">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20" x-text="cat.name"></span>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
