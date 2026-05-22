<nav
    x-data="navbar"
    x-init="init()"
    :class="scrolled ? 'glass-strong shadow-2xl' : 'bg-transparent'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300 px-4 sm:px-6 lg:px-8"
    @if(app()->getLocale() === 'ar') dir="rtl" @endif
>
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 sm:h-20">
        <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold hero-gradient-text tracking-tight">
            Optique Échouate
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('products.index') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.shop') }}</a>
            <a href="{{ route('quiz') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.quiz') }}</a>
            <a href="{{ route('appointments') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.book') }}</a>
            <a href="{{ route('eye-health') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.eye_health') }}</a>

           
            <a href="{{ route('quiz') }}" class="hero-gradient text-white px-5 py-2 rounded-full text-sm font-medium hover-lift glow-orange transition-all duration-300">
                {{ __('messages.nav.find_your_fit') }}
            </a>
        </div>

        <button @click="mobileOpen = !mobileOpen" class="md:hidden text-white p-2">
            <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="md:hidden glass-strong rounded-2xl p-4 mb-4 space-y-3">
        <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.shop') }}</a>
        <a href="{{ route('quiz') }}" class="block px-4 py-2 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.quiz') }}</a>
        <a href="{{ route('appointments') }}" class="block px-4 py-2 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.book') }}</a>
        <a href="{{ route('eye-health') }}" class="block px-4 py-2 text-sm text-heroi-text-muted hover:text-white rounded-xl hover:bg-white/5 transition-all">{{ __('messages.nav.eye_health') }}</a>

        <a href="{{ route('quiz') }}" class="block text-center hero-gradient text-white px-5 py-3 rounded-full text-sm font-medium">{{ __('messages.nav.find_your_fit') }}</a>
    </div>
</nav>
