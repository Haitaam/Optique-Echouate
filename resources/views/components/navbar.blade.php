<nav
    x-data="navbar"
    x-init="init()"
    :class="scrolled ? 'glass-strong shadow-2xl' : 'bg-transparent'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300 px-4 sm:px-6 lg:px-8"
    @if(app()->getLocale() === 'ar') dir="rtl" @endif
>
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 sm:h-20">
        <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold hero-gradient-text tracking-tight flex-shrink-0">
            Optique Échouate
        </a>

        <div class="hidden lg:flex items-center gap-6 xl:gap-8 absolute left-1/2 -translate-x-1/2">
            <a href="{{ route('products.index') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors whitespace-nowrap">{{ __('messages.nav.shop') }}</a>
            <a href="{{ route('quiz') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors whitespace-nowrap">{{ __('messages.nav.quiz') }}</a>
            <a href="{{ route('eye-health') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors whitespace-nowrap">{{ __('messages.nav.eye_health') }}</a>
            <a href="{{ route('appointments') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors whitespace-nowrap">{{ __('messages.nav.book') }}</a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <button @click="toggleSearch()" class="p-2 text-heroi-text-muted hover:text-white transition-colors" aria-label="{{ __('messages.nav.search') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>

            <button x-data="cartBadge" @click="toggle()" class="p-2 text-heroi-text-muted hover:text-white transition-colors relative" aria-label="{{ __('messages.nav.cart') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span x-show="count > 0" x-cloak x-text="count" class="absolute -top-1 -right-1 w-4.5 h-4.5 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center leading-none">0</span>
            </button>

            <div class="hidden lg:relative lg:block" x-data="accountDropdown">
                <button @click="open = !open" @click.outside="open = false" class="p-2 text-heroi-text-muted hover:text-white transition-colors" aria-label="{{ __('messages.nav.account') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute right-0 mt-2 w-52 rounded-xl border border-white/10 bg-heroi-bg-alt shadow-2xl z-20 overflow-hidden">
                    @auth('customer')
                        <div class="px-4 py-3 border-b border-white/5">
                            <p class="text-sm text-white font-medium truncate">{{ auth('customer')->user()->name }}</p>
                            <p class="text-xs text-heroi-text-muted truncate">{{ auth('customer')->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('messages.nav.profile') }}
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            {{ __('messages.nav.my_orders') }}
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            {{ __('messages.nav.my_wishlist') }}
                        </a>
                        <a href="{{ route('account.info') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Mes informations
                        </a>
                        <a href="{{ route('account.addresses') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors border-b border-white/5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('messages.nav.my_addresses') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('messages.nav.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            {{ __('messages.nav.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            {{ __('messages.nav.register') }}
                        </a>
                    @endauth
                </div>
            </div>

            <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-white" aria-label="{{ __('messages.nav.menu') }}">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Search Bar --}}
    <div x-show="searchOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="pb-4 -mt-1 relative" @keydown="onKeydown($event)" @click.outside="closeSearch()">
        <div class="relative flex gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-heroi-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="searchQuery" @keyup.enter="doSearch()" @input="searchLive()" @focus="hasSearched && searchLive()"
                    placeholder="{{ __('messages.nav.search_placeholder') }}"
                    class="w-full pl-12 pr-10 py-3 rounded-xl bg-white/10 border border-white/20 text-white text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                <svg x-show="searching" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-heroi-text-muted animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </div>
            <button @click="doSearch()" class="px-4 py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Confirmer
            </button>
        </div>

        {{-- Dropdown --}}
        <div x-show="showDropdown" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2"
            class="absolute left-0 right-24 top-full mt-1 rounded-xl border border-white/10 bg-heroi-bg-alt shadow-2xl z-50 overflow-hidden max-h-[70vh] overflow-y-auto">

            {{-- Loading --}}
            <div x-show="searching && !searchProducts.length" class="flex items-center gap-3 px-5 py-6 text-heroi-text-muted">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span class="text-sm">Recherche...</span>
            </div>

            {{-- Empty State --}}
            <div x-show="!searching && hasSearched && searchQuery.trim().length >= 2 && !hasResults" class="px-5 py-8 text-center">
                <svg class="w-12 h-12 mx-auto text-heroi-text-muted/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-heroi-text-muted text-sm font-medium">Aucun produit trouvé</p>
                <p class="text-heroi-text-muted/50 text-xs mt-1">Essayez un autre mot-clé</p>
            </div>

            {{-- Recent Searches (when query is empty) --}}
            <div x-show="!searchQuery.trim() && recentSearches.length" class="px-4 py-3 border-b border-white/5">
                <p class="text-xs font-medium text-heroi-text-muted/60 uppercase tracking-wider mb-2">Recherches récentes</p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(s, i) in recentSearches" :key="i">
                        <a :href="'/products?search=' + encodeURIComponent(s)"
                            class="px-3 py-1.5 rounded-lg bg-white/5 text-xs text-heroi-text-muted hover:text-white hover:bg-white/10 transition-all">
                            <span x-text="s"></span>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Results --}}
            <template x-for="(item, idx) in flatResults" :key="idx">
                <div>
                    {{-- Section Header --}}
                    <div x-show="item.type === 'header'"
                        class="px-4 py-2 text-xs font-semibold text-heroi-text-muted/60 uppercase tracking-wider bg-white/[0.02] border-b border-white/5"
                        x-text="item.label">
                    </div>

                    {{-- Product Result --}}
                    <a x-show="item.type === 'product'"
                        :data-search-idx="idx"
                        :class="highlightIndex === idx ? 'bg-orange-500/10 border-orange-500/20' : 'border-transparent'"
                        :href="'/products?search=' + encodeURIComponent(item.name)"
                        class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors border-l-2">
                        <img :src="item.image" :alt="item.name" class="w-10 h-10 rounded-lg object-cover bg-white/5 flex-shrink-0" loading="lazy">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate" x-text="item.name"></p>
                            <p class="text-xs text-heroi-text-muted" x-text="item.brand"></p>
                        </div>
                        <span class="text-sm font-semibold text-orange-400 flex-shrink-0" x-text="item.price_formatted"></span>
                    </a>

                    {{-- Brand Result --}}
                    <a x-show="item.type === 'brand'"
                        :data-search-idx="idx"
                        :class="highlightIndex === idx ? 'bg-orange-500/10 border-orange-500/20' : 'border-transparent'"
                        :href="'/products?brand=' + encodeURIComponent(item.name)"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 transition-colors border-l-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate" x-text="item.name"></p>
                            <p class="text-xs text-heroi-text-muted" x-text="item.count + ' produit(s)'"></p>
                        </div>
                    </a>

                    {{-- Category Result --}}
                    <a x-show="item.type === 'category'"
                        :data-search-idx="idx"
                        :class="highlightIndex === idx ? 'bg-orange-500/10 border-orange-500/20' : 'border-transparent'"
                        :href="'/products?category=' + item.id"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 transition-colors border-l-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate" x-text="item.name"></p>
                            <p class="text-xs text-heroi-text-muted" x-text="item.count + ' produit(s)'"></p>
                        </div>
                    </a>
                </div>
            </template>

            {{-- View All --}}
            <div x-show="!searching && hasSearched && searchQuery.trim().length >= 2 && searchTotal > 0"
                class="border-t border-white/5 px-4 py-3">
                <a :href="'/products?search=' + encodeURIComponent(searchQuery.trim())"
                    class="block text-center text-sm text-orange-400 hover:text-orange-300 font-medium transition-colors">
                    Voir tous les résultats (<span x-text="searchTotal"></span>)
                </a>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="lg:hidden glass-strong rounded-2xl p-4 mb-4 space-y-1 max-h-[80vh] overflow-y-auto">
        <a href="{{ route('products.index') }}" class="block px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.shop') }}</a>
        <a href="{{ route('quiz') }}" class="block px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.quiz') }}</a>
        <a href="{{ route('eye-health') }}" class="block px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.eye_health') }}</a>
        <a href="{{ route('appointments') }}" class="block px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.book') }}</a>

        <hr class="border-white/5 my-3">

        @auth('customer')
            <a href="{{ route('order.tracking') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('messages.nav.track_order') }}
            </a>
            <hr class="border-white/5 my-3">
            <div class="px-4 py-2">
                <p class="text-sm text-white font-medium">{{ auth('customer')->user()->name }}</p>
                <p class="text-xs text-heroi-text-muted">{{ auth('customer')->user()->email }}</p>
            </div>
            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ __('messages.nav.profile') }}
            </a>
            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('messages.nav.my_orders') }}
            </a>
            <a href="{{ route('account.wishlist') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                {{ __('messages.nav.my_wishlist') }}
            </a>
            <a href="{{ route('account.reviews') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                {{ __('messages.nav.my_reviews') }}
            </a>
            <a href="{{ route('account.addresses') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('messages.nav.my_addresses') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('messages.nav.logout') }}
                </button>
            </form>
        @else
            <hr class="border-white/5 my-3">
            <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                {{ __('messages.nav.login') }}
            </a>
            <a href="{{ route('register') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                {{ __('messages.nav.register') }}
            </a>
        @endauth
    </div>
</nav>
