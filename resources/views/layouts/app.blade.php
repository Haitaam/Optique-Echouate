<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" @if(app()->getLocale() === 'ar') dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Optique Échouate') — Lunettes Premium</title>
    <meta name="description" content="@yield('meta_description', 'Découvrez des lunettes de luxe premium chez Optique Échouate. Quiz de style intelligent, soins oculaires experts et prise de rendez-vous.')">
    @php $_favicon = App\Models\Setting::get('favicon', '/optique-echouate.png'); @endphp
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $_favicon }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ $_favicon }}">
    <link rel="shortcut icon" href="{{ $_favicon }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="livePoll" class="min-h-screen antialiased">
    <x-loader />

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
        @if (isset($hideNav))
            @php $notifInitialCount = App\Models\Notification::unread()->count(); @endphp
            <div x-data="notifPanel" x-init="init({{ $notifInitialCount }})" data-read-all-url="{{ route('admin.notifications.read-all') }}" class="fixed top-2 right-4 z-[60]">
                <button @click="toggle()" class="relative p-2 rounded-lg hover:bg-white/5 transition-colors" :class="open ? 'bg-white/10' : ''">
                    <svg class="w-5 h-5 text-heroi-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount" class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full"></span>
                </button>
                <div x-show="open" x-cloak @click.outside="close()" class="absolute right-0 mt-2 w-80 rounded-xl bg-[#151520] border border-white/10 shadow-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-white/5 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white">Notifications</h3>
                        <button x-show="unreadCount > 0" @click="readAll()" class="text-xs text-orange-400 hover:text-orange-300 transition-colors" type="button">Tout marquer lu</button>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <template x-for="n in notifications" :key="n.id">
                            <a :href="n.url" @click.prevent="markRead(n)"
                                class="block px-4 py-3 hover:bg-white/5 transition-colors border-b border-white/5 last:border-b-0"
                                :class="n.read ? '' : 'bg-orange-500/5 border-l-2 border-l-orange-500'">
                                <p class="text-sm font-medium" :class="n.read ? 'text-heroi-text-muted' : 'text-white'" x-text="n.title"></p>
                                <p x-show="n.body" class="text-xs text-heroi-text-muted mt-0.5" x-text="n.body"></p>
                                <p class="text-[10px] text-heroi-text-muted/60 mt-1" x-text="n.created_at"></p>
                            </a>
                        </template>
                        <p x-show="notifications.length === 0" class="text-sm text-heroi-text-muted text-center py-8">Aucune notification</p>
                    </div>
                    <div class="border-t border-white/5 px-4 py-2">
                        <a href="{{ route('admin.notifications.index') }}" class="block text-center text-xs text-orange-400 hover:text-orange-300 transition-colors font-medium">Voir toutes les notifications</a>
                    </div>
                </div>
            </div>
            <div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-heroi-bg">
                <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
                <div class="fixed inset-y-0 left-0 z-50 w-64 transition-transform duration-300 ease-in-out lg:hidden"
                     style="transform: translateX(-100%)"
                     :style="sidebarOpen ? 'transform: translateX(0)' : 'transform: translateX(-100%)'">
                    <div class="h-full bg-[#0a0a0f] border-r border-white/5">
                        @include('admin.partials.admin-nav')
                    </div>
                </div>
                <div class="hidden lg:block flex-shrink-0">
                    <div class="h-screen sticky top-0">
                        @include('admin.partials.admin-nav')
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="lg:hidden sticky top-0 z-30 flex items-center gap-3 px-4 h-12 border-b border-white/5 bg-[#0a0a0f]">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 rounded-lg hover:bg-white/5 transition-colors">
                            <svg class="w-5 h-5 text-heroi-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <span class="text-sm font-medium text-white/80">Administration</span>
                    </div>
                    @yield('content')
                </div>
            </div>
        @else
            @yield('content')
        @endif
    </main>

    @if (!isset($hideNav))
        @php
            $_siteName = App\Models\Setting::get('site_name', 'Optique Échouate');
            $_profession = App\Models\Setting::get('profession', 'Opticien - Optométriste');
            $_city = App\Models\Setting::get('city', 'Asilah');
            $_phone = App\Models\Setting::get('contact_phone', '0782655266');
            $_email = App\Models\Setting::get('contact_email', 'echouateoptique@gmail.com');
            $_hours = App\Models\Setting::get('working_hours', 'Du lundi au samedi : 10h - 21h');
            $_mapsUrl = App\Models\Setting::get('maps_url', 'https://maps.app.goo.gl/fXQCFf24qHRWArKN8?g_st=ipc');
        @endphp
        <footer x-data="liveFooter"
             data-site-name="{{ $_siteName }}"
             data-profession="{{ $_profession }}"
             data-city="{{ $_city }}"
             data-phone="{{ $_phone }}"
             data-email="{{ $_email }}"
             data-hours="{{ $_hours }}"
             data-maps-url="{{ $_mapsUrl }}"
             class="border-t border-white/5 bg-[#050510]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-xl font-bold hero-gradient-text mb-4" x-text="siteName">{{ $_siteName }}</h3>
                        <p class="text-heroi-text-muted text-sm leading-relaxed max-w-md"><span x-text="profession">{{ $_profession }}</span> — <span x-text="city">{{ $_city }}</span></p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-3">{{ __('messages.footer.quick_links') }}</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('products.index') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.shop') }}</a></li>
                            <li><a href="{{ route('appointments') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.book') }}</a></li>
                            <li><a href="{{ route('eye-health') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.eye_health') }}</a></li>
                            <li><a href="{{ auth('customer')->check() ? route('order.tracking') : route('login') }}" class="text-sm text-heroi-text-muted hover:text-white transition-colors">{{ __('messages.nav.track_order') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-3">Contact</h4>
                        <ul class="space-y-3">
                            <li>
                                <a x-bind:href="'tel:' + phone" href="tel:{{ $_phone }}" class="inline-flex items-center gap-2 text-sm text-heroi-text-muted hover:text-orange-400 transition-colors">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span x-text="phone">{{ $_phone }}</span>
                                </a>
                            </li>
                            <li>
                                <a x-bind:href="'mailto:' + email" href="mailto:{{ $_email }}" class="inline-flex items-center gap-2 text-sm text-heroi-text-muted hover:text-orange-400 transition-colors">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span x-text="email">{{ $_email }}</span>
                                </a>
                            </li>
                            <li class="inline-flex items-center gap-2 text-sm text-heroi-text-muted">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="hours">{{ $_hours }}</span>
                            </li>
                            <li>
                                <a x-bind:href="mapsUrl" href="{{ $_mapsUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 text-sm text-heroi-text-muted hover:text-orange-400 transition-colors">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Nous trouver
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-white/5 text-center space-y-2">
                    <p class="text-xs text-heroi-text-muted">&copy; {{ date('Y') }} <span x-text="siteName">{{ $_siteName }}</span>. {{ __('messages.footer.rights') }}</p>
                    <p class="text-xs text-heroi-text-muted">
                        Développé par
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
        <x-cart-panel />
    @endif

    <script>
        window.addToCart = function(encoded) {
            try {
                window.dispatchEvent(new CustomEvent('cart-add', { detail: JSON.parse(atob(encoded)) }));
                window.dispatchEvent(new CustomEvent('cart-toggle'));
            } catch(e) { console.error('addToCart', e); }
        };
        window.openFacePreview = function(encoded, faceData) {
            try {
                window.dispatchEvent(new CustomEvent('face-preview-open', {
                    detail: { product: JSON.parse(atob(encoded)), faceData: faceData }
                }));
            } catch(e) { console.error('openFacePreview', e); }
        };
    </script>

    @stack('scripts')
</body>
</html>
