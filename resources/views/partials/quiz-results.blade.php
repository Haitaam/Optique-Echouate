<div class="space-y-6">
    <div class="text-center mb-8">
        <h3 class="text-2xl sm:text-3xl font-bold text-white">{{ __('messages.quiz.results_title') }}</h3>
        <p class="text-heroi-text-muted mt-2">{{ __('messages.quiz.results_subtitle') }}</p>
    </div>

    @forelse ($results as $result)
        @php $product = $result['product']; @endphp
        <x-card padding="p-0" class="overflow-hidden group cursor-pointer" onclick="window.dispatchEvent(new CustomEvent('product-detail-open', {detail: @js($product->toArray())}))">
            <div class="flex flex-col sm:flex-row">
                <div class="product-image-side">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
                </div>
                <div class="flex-1 p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-xs text-heroi-text-muted uppercase tracking-wider">{{ $product->brand }}</span>
                                <h4 class="text-lg font-semibold text-white mt-1">{{ $product->name }}</h4>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold hero-gradient-text">{{ $result['percentage'] }}%</div>
                                <div class="text-xs text-heroi-text-muted">{{ __('messages.product.match') }}</div>
                            </div>
                        </div>
                        <p class="text-sm text-heroi-text-muted leading-relaxed">{{ $result['reasoning'] }}</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach ($result['matches'] as $match)
                                <span class="px-3 py-1 text-xs rounded-full bg-orange-500/10 text-orange-300 border border-orange-500/20">{{ $match }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                        <span class="text-xl font-bold hero-gradient-text">{{ number_format($product->price * 10, 0, ',', ' ') }} MAD</span>
                    </div>
                </div>
            </div>
        </x-card>
    @empty
        <div class="text-center py-12">
            <p class="text-heroi-text-muted">{{ __('messages.quiz.no_matches') }}</p>
        </div>
    @endforelse

    <div class="text-center mt-8">
        <x-button variant="secondary" @click="restartQuiz()">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            {{ __('messages.quiz.retake') }}
        </x-button>
    </div>
</div>
