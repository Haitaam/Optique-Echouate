<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6" x-data="productGrid" @mouseleave="clearHovered()">
    @forelse ($products as $product)
        @php $_encoded = base64_encode($product->toJson()) @endphp
        <div data-product="{{ $product->id }}
            class="relative transition-all duration-500 ease-out cursor-pointer"
            @mouseenter="setHovered({{ $product->id }})"
            @touchstart="setHovered({{ $product->id }}); setTimeout(() => clearHovered(), 3000)"
            :class="hoveredId === {{ $product->id }} ? 'z-20 sm:scale-105 sm:-translate-y-2' : 'z-10 scale-100 translate-y-0'"
            onclick="window.dispatchEvent(new CustomEvent('product-detail-open', {detail: JSON.parse(atob('{{ $_encoded }}'))}))"
        >
            <div
                class="relative overflow-hidden rounded-2xl transition-all duration-500 ease-out"
                :class="hoveredId === {{ $product->id }}
                    ? 'shadow-2xl shadow-orange-500/20 ring-1 ring-orange-500/30 bg-white/5'
                    : 'shadow-lg shadow-black/20 bg-white/[0.02] hover:bg-white/[0.04]'"
            >
                {{-- Image Container --}}
                <div class="product-image-wrap">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
                </div>

                {{-- Overlay details on hover --}}
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-all duration-500 ease-out flex flex-col justify-end p-5"
                    :class="hoveredId === {{ $product->id }} ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                >
                    <div class="space-y-2 translate-y-2 transition-transform duration-500 ease-out" :class="hoveredId === {{ $product->id }} ? 'translate-y-0' : 'translate-y-4'">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-medium text-orange-300 bg-orange-500/20 px-2.5 py-0.5 rounded-full backdrop-blur-sm">{{ $product->brand }}</span>
                            <span class="text-xs text-white/70 bg-white/10 px-2.5 py-0.5 rounded-full backdrop-blur-sm">{{ $product->frame_shape }}</span>
                            <span class="text-xs text-white/70 bg-white/10 px-2.5 py-0.5 rounded-full backdrop-blur-sm">{{ $product->material }}</span>
                        </div>
                        <h3 class="text-white font-semibold text-base drop-shadow-sm">{{ $product->name }}</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-white drop-shadow-sm">{{ number_format($product->price, 0, ',', ' ') }} MAD</span>
                            <span class="inline-flex items-center text-xs text-orange-300 bg-orange-500/20 px-3 py-1 rounded-full backdrop-blur-sm">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $product->color }}
                            </span>
                            <button onclick="event.stopPropagation(); window.addToCart('{{ $_encoded }}')"
                                class="px-3 py-1.5 rounded-lg bg-orange-500/20 border border-orange-500/30 text-orange-300 hover:bg-orange-500/30 hover:text-orange-200 text-xs font-medium transition-all flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Ajouter
                            </button>
                            <button onclick="event.stopPropagation(); window.openFacePreview('{{ $_encoded }}', {
                                round: {percentage: 85, explanation: 'Convient parfaitement aux visages ronds'},
                                oval: {percentage: 90, explanation: 'Idéal pour les visages ovales'},
                                square: {percentage: 70, explanation: 'Bon équilibre pour les visages carrés'},
                                heart: {percentage: 75, explanation: 'Flatte les visages en cœur'},
                                diamond: {percentage: 65, explanation: 'Complète les visages en diamant'},
                            })"
                                class="text-xs text-purple-300 hover:text-purple-200 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Essayer
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Default visible info --}}
                <div class="p-4 sm:p-5 transition-all duration-500 ease-out" :class="hoveredId === {{ $product->id }} ? 'opacity-0' : 'opacity-100'">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-heroi-text-muted uppercase tracking-wider">{{ $product->brand }}</span>
                        <span class="text-xs text-heroi-text-muted">{{ $product->frame_shape }}</span>
                    </div>
                    <h3 class="font-semibold text-white text-sm sm:text-base mb-1 truncate">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-base font-bold hero-gradient-text">{{ number_format($product->price, 0, ',', ' ') }} MAD</span>
                        <span class="w-4 h-4 rounded-full border border-white/10 block flex-shrink-0" style="background: {{ match(strtolower($product->color)) { 'gold' => '#FFD700', 'rose gold' => '#B76E79', 'silver' => '#C0C0C0', 'gunmetal' => '#2C3539', 'black' => '#000', 'matte black' => '#1a1a1a', 'tortoise' => '#8B6914', 'crystal' => '#E8E8E8', 'white' => '#fff', 'blue' => '#3B82F6', 'red' => '#EF4444', default => '#f97316' } }}"></span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-20">
            <svg class="w-16 h-16 mx-auto text-heroi-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-heroi-text-muted text-lg">{{ __('messages.products.no_results') }}</p>
            <p class="text-heroi-text-muted text-sm mt-2">{{ __('messages.products.adjust_filters') }}</p>
        </div>
    @endforelse
</div>

@if ($products->hasPages())
    <div class="mt-8">
        {{ $products->onEachSide(1)->links() }}
    </div>
@endif
