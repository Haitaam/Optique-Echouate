<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" @if(app()->getLocale() === 'ar') dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Optique Échouate') — Lunettes Premium</title>
    <meta name="description" content="@yield('meta_description', 'Découvrez des lunettes de luxe premium chez Optique Échouate. Quiz de style intelligent, soins oculaires experts et prise de rendez-vous.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    {{-- Toast Notification --}}
    <div x-data="toast" x-init="init()" class="fixed top-4 right-4 z-[100] space-y-2">
        <template x-teleport="body">
            <div
                x-show="visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-full opacity-0"
                class="fixed top-4 right-4 z-[100]"
            >
                <div class="glass-strong rounded-xl px-6 py-4 shadow-2xl border" :class="type === 'success' ? 'border-emerald-500/30' : 'border-red-500/30'">
                    <div class="flex items-center gap-3">
                        <svg x-show="type === 'success'" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="type === 'error'" class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <p class="text-sm font-medium text-white" x-text="message"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @if (!isset($hideNav))
        <x-navbar />
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-white/5 bg-[#050510]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-xl font-bold hero-gradient-text mb-4">Optique Échouate</h3>
                    <p class="text-heroi-text-muted text-sm leading-relaxed max-w-md">
                        {{ __('messages.footer.description') }}
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">{{ __('messages.footer.quick_links') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('products.index') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.shop') }}</a></li>
                        <li><a href="{{ route('quiz') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.quiz') }}</a></li>
                        <li><a href="{{ route('appointments') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.book') }}</a></li>
                        <li><a href="{{ route('eye-health') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.eye_health') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">{{ __('messages.footer.contact') }}</h4>
                    <ul class="space-y-2">
                        <li class="text-sm text-heroi-text-muted">{{ __('messages.appointments.email') }}</li>
                        <li class="text-sm text-heroi-text-muted">{{ __('messages.appointments.phone') }}</li>
                        <li class="text-sm text-heroi-text-muted">{{ __('messages.appointments.hours') }}</li>
                        <li>
                            <a href="{{ $gmapsUrl ?? '#' }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 text-sm text-heroi-text-muted hover:text-orange-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Nous trouver
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-white/5 text-center space-y-2">
                <p class="text-xs text-heroi-text-muted">&copy; {{ date('Y') }} Optique Échouate. {{ __('messages.footer.rights') }}</p>
                <p class="text-xs text-heroi-text-muted">
                    Developed by
                    <a href="https://wa.me/212600092985"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="text-orange-400 hover:text-orange-300 transition-colors font-medium">
                        EL AHMAR HAITAM
                    </a>
                </p>
            </div>
        </div>
    </footer>

    <x-whatsapp-float />

    <x-face.preview-modal />
    <x-product-detail-modal />

    @stack('scripts')
</body>
</html>
