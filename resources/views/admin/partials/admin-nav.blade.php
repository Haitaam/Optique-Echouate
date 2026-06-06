@php
    $currentRoute = Route::currentRouteName() ?? '';
    $routeMap = [
        'admin.dashboard' => 'dashboard',
        'admin.orders.index' => 'orders',
        'admin.orders.pending' => 'pending',
        'admin.orders.show' => 'orders',
        'admin.orders.edit' => 'orders',
        'admin.glasses.index' => 'glasses',
        'admin.glasses.edit' => 'glasses',
        'admin.glasses.create' => 'glasses',
        'admin.brands.index' => 'brands',
        'admin.brands.edit' => 'brands',
        'admin.brands.create' => 'brands',
        'admin.colors.index' => 'colors',
        'admin.colors.edit' => 'colors',
        'admin.colors.create' => 'colors',
        'admin.shapes.index' => 'shapes',
        'admin.shapes.edit' => 'shapes',
        'admin.shapes.create' => 'shapes',
        'admin.genders.index' => 'genders',
        'admin.genders.edit' => 'genders',
        'admin.genders.create' => 'genders',
        'admin.customers.index' => 'customers',
        'admin.customers.show' => 'customers',
        'admin.testimonials.index' => 'testimonials',
        'admin.testimonials.edit' => 'testimonials',
        'admin.testimonials.create' => 'testimonials',
        'admin.reviews.index' => 'reviews',
        'admin.settings.index' => 'settings',
        'admin.notifications.index' => 'dashboard',
    ];
    $activeNav = $routeMap[$currentRoute] ?? 'dashboard';

    $groups = [
        'dashboard' => [
            'title' => null,
            'items' => [
                'dashboard' => ['label' => 'Dashboard', 'icon' => 'chart', 'route' => 'admin.dashboard'],
            ],
        ],
        'orders' => [
            'title' => 'Commandes',
            'items' => [
                'orders' => ['label' => 'Toutes les commandes', 'icon' => 'bag', 'route' => 'admin.orders.index'],
                'pending' => ['label' => 'En attente', 'icon' => 'clock', 'route' => 'admin.orders.pending'],
            ],
        ],
        'products' => [
            'title' => 'Produits',
            'items' => [
                'glasses' => ['label' => 'Lunettes', 'icon' => 'eye', 'route' => 'admin.glasses.index'],
                'brands' => ['label' => 'Marques', 'icon' => 'tag', 'route' => 'admin.brands.index'],
                'colors' => ['label' => 'Couleurs', 'icon' => 'palette', 'route' => 'admin.colors.index'],
                'shapes' => ['label' => 'Formes', 'icon' => 'shape', 'route' => 'admin.shapes.index'],
                'genders' => ['label' => 'Genres', 'icon' => 'user', 'route' => 'admin.genders.index'],
            ],
        ],
        'customers' => [
            'title' => 'Clients',
            'items' => [
                'customers' => ['label' => 'Clients', 'icon' => 'users', 'route' => 'admin.customers.index'],
                'testimonials' => ['label' => 'Avis clients', 'icon' => 'star', 'route' => 'admin.testimonials.index'],
                'reviews' => ['label' => 'Avis produits', 'icon' => 'chat', 'route' => 'admin.reviews.index'],
            ],
        ],
        'settings' => [
            'title' => null,
            'items' => [
                'settings' => ['label' => 'Paramètres', 'icon' => 'cog', 'route' => 'admin.settings.index'],
            ],
        ],
    ];
@endphp
<aside class="w-64 bg-[#0a0a0f] border-r border-white/5 flex flex-col h-full overflow-y-auto">
    <div class="px-5 py-5 border-b border-white/5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
            <span class="text-lg font-bold hero-gradient-text">Optique</span>
            <span class="text-lg font-semibold text-white/60">Admin</span>
        </a>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-6">
        @foreach($groups as $groupKey => $group)
            <div>
                @if($group['title'])
                    <p class="px-3 text-[10px] font-semibold uppercase tracking-widest text-heroi-text-muted/50 mb-1.5">{{ $group['title'] }}</p>
                @endif
                <ul class="space-y-0.5">
                    @foreach($group['items'] as $key => $item)
                        @php
                            $isActive = $key === $activeNav;
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150
                                @if($isActive)
                                    text-orange-400 bg-orange-500/10 shadow-sm
                                @else
                                    text-heroi-text-muted hover:text-white hover:bg-white/5
                                @endif">
                                <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
                                    @if($item['icon'] === 'chart')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    @elseif($item['icon'] === 'bag')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    @elseif($item['icon'] === 'clock')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($item['icon'] === 'eye')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @elseif($item['icon'] === 'tag')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    @elseif($item['icon'] === 'palette')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                    @elseif($item['icon'] === 'shape')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                    @elseif($item['icon'] === 'user')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    @elseif($item['icon'] === 'users')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                                    @elseif($item['icon'] === 'star')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    @elseif($item['icon'] === 'chat')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    @elseif($item['icon'] === 'cog')
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    @endif
                                </span>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
    <div class="border-t border-white/5 px-3 py-3 space-y-0.5">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-heroi-text-muted hover:text-white hover:bg-white/5 transition-all duration-150">
            <svg class="w-4.5 h-4.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voir le site
        </a>
        <form method="POST" action="{{ route('admin.logout') }}" class="block">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-150">
                <svg class="w-4.5 h-4.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Déconnexion
            </button>
        </form>
    </div>
</aside>
