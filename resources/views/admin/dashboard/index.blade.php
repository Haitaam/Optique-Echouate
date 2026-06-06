@extends('layouts.app')

@section('title', 'Admin — Dashboard')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Dashboard</h1>
            <p class="text-sm text-heroi-text-muted mt-1">Vue d'ensemble de votre boutique</p>
        </div>

        <div x-data="liveAdmin"
             data-pending="{{ $pendingOrders }}"
             data-notifs="{{ App\Models\Notification::unread()->count() }}"
             data-total="{{ $totalOrders }}"
             data-revenue="{{ number_format($totalRevenue, 2, ',', ' ') }} MAD"
             data-revenue-today="{{ number_format($revenueToday, 2, ',', ' ') }} MAD">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-heroi-text-muted text-sm font-medium">Commandes en attente</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-white" x-text="pendingOrders">{{ $pendingOrders }}</div>
                    <a href="{{ route('admin.orders.pending') }}" class="text-xs text-amber-400 hover:text-amber-300 transition-colors mt-2 inline-block">Voir →</a>
                </div>

                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-heroi-text-muted text-sm font-medium">Produits</span>
                        <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $productCount }}</div>
                    <a href="{{ route('admin.glasses.index') }}" class="text-xs text-orange-400 hover:text-orange-300 transition-colors mt-2 inline-block">Gérer →</a>
                </div>

                <div class="admin-card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-heroi-text-muted text-sm font-medium">Total commandes</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-white" x-text="totalOrders">{{ $totalOrders }}</div>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors mt-2 inline-block">Voir →</a>
                </div>

                <div class="admin-card p-6 mb-8">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-heroi-text-muted text-sm font-medium">Revenu total</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-white" x-text="totalRevenue">{{ number_format($totalRevenue, 2, ',', ' ') }}</div>
                    <span class="text-xs text-heroi-text-muted mt-2 inline-block">MAD</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="admin-card p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-heroi-text-muted">Revenu aujourd'hui</p>
                            <p class="text-lg font-bold text-white"><span x-text="revenueToday">{{ number_format($revenueToday, 2, ',', ' ') }}</span> <span class="text-xs text-heroi-text-muted">MAD</span></p>
                        </div>
                    </div>
                </div>
            <div class="admin-card p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-heroi-text-muted">Revenu ce mois</p>
                        <p class="text-lg font-bold text-white">{{ number_format($revenueMonth, 2, ',', ' ') }} <span class="text-xs text-heroi-text-muted">MAD</span></p>
                    </div>
                </div>
            </div>
            <div class="admin-card p-4 {{ $lowStockProducts > 0 ? 'border-amber-500/30' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $lowStockProducts > 0 ? 'bg-amber-500/20' : 'bg-white/5' }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 {{ $lowStockProducts > 0 ? 'text-amber-400' : 'text-heroi-text-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-heroi-text-muted">Stock faible</p>
                        <p class="text-lg font-bold text-white">{{ $lowStockProducts }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-card p-4 {{ $outOfStockProducts > 0 ? 'border-red-500/30' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $outOfStockProducts > 0 ? 'bg-red-500/20' : 'bg-white/5' }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 {{ $outOfStockProducts > 0 ? 'text-red-400' : 'text-heroi-text-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-heroi-text-muted">Rupture de stock</p>
                        <p class="text-lg font-bold text-white">{{ $outOfStockProducts }}</p>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="admin-card p-6 lg:col-span-2">
                <h3 class="text-sm font-semibold text-white mb-4">Commandes par statut</h3>
                <div class="space-y-3">
                    @php
                        $statusColors = [
                            App\Models\Order::STATUS_PENDING => ['bg-amber-500', 'text-amber-300'],
                            App\Models\Order::STATUS_CONFIRMED => ['bg-emerald-500', 'text-emerald-300'],
                            App\Models\Order::STATUS_PREPARING => ['bg-blue-500', 'text-blue-300'],
                            App\Models\Order::STATUS_SHIPPED => ['bg-purple-500', 'text-purple-300'],
                            App\Models\Order::STATUS_DELIVERED => ['bg-green-500', 'text-green-300'],
                            App\Models\Order::STATUS_CANCELLED => ['bg-red-500', 'text-red-300'],
                        ];
                        $maxCount = max($ordersByStatus) ?: 1;
                    @endphp
                    @foreach(App\Models\Order::STATUS_LABELS as $s => $label)
                        @php $count = $ordersByStatus[$s] ?? 0; $pct = round(($count / $maxCount) * 100); @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="{{ $statusColors[$s][1] ?? 'text-heroi-text-muted' }} font-medium">{{ $label }}</span>
                                <span class="text-white font-semibold">{{ $count }}</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-white/5 overflow-hidden">
                                <div class="h-full rounded-full {{ $statusColors[$s][0] ?? 'bg-white/20' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Qualité des images</h3>
                <div class="flex items-center gap-6">
                    <div class="relative w-28 h-28">
                        <svg class="w-28 h-28 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="2.5"/>
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgb(34,197,94)" stroke-width="2.5"
                                stroke-dasharray="{{ $productCount > 0 ? ($validImages / $productCount) * 100 : 0 }} {{ $productCount > 0 ? 100 - ($validImages / $productCount) * 100 : 100 }}"
                                stroke-linecap="round"/>
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgb(239,68,68)" stroke-width="2.5"
                                stroke-dasharray="{{ $productCount > 0 ? ($brokenImages / $productCount) * 100 : 0 }} 100"
                                stroke-dashoffset="{{ $productCount > 0 ? -($validImages / $productCount) * 100 : 0 }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-white">{{ $validImages }}</span>
                            <span class="text-[10px] text-heroi-text-muted">valides</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs text-heroi-text-muted">{{ $validImages }} valides</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-xs text-heroi-text-muted">{{ $brokenImages }} cassées</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <span class="text-xs text-heroi-text-muted">{{ $imagesOnDisk }} sur disque</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white">Commandes en attente</h3>
                    <a href="{{ route('admin.orders.pending') }}" class="text-xs text-orange-400 hover:text-orange-300 transition-colors">Voir tout →</a>
                </div>
                @php $recentPending = App\Models\Order::pendingConfirmation()->latest()->take(5)->get(); @endphp
                @if($recentPending->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentPending as $po)
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-white/5 transition-colors">
                                <div>
                                    <p class="text-sm text-white font-medium">#{{ $po->id }} — {{ $po->name }}</p>
                                    <p class="text-xs text-heroi-text-muted">{{ $po->phone }} · {{ $po->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20">🟡 En attente</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-heroi-text-muted text-center py-4">Aucune commande en attente</p>
                @endif
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Répartition par marque</h3>
                <div class="space-y-2.5">
                    @php $brandsTotal = $brands->count(); $brandCounts = $brandCounts ?? collect(); @endphp
                    @foreach ($brands->take(6) as $brand)
                        @php
                            $count = $brandCounts[$brand] ?? 0;
                            $pct = $productCount > 0 ? round(($count / $productCount) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-heroi-text-muted">{{ $brand }}</span>
                                <span class="text-white font-medium">{{ $count }}</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-white/5 overflow-hidden">
                                <div class="h-full rounded-full bg-orange-500/60" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($brandsTotal > 6)
                        <p class="text-xs text-heroi-text-muted text-center pt-1">+{{ $brandsTotal - 6 }} autres marques</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white">Produits récents</h3>
                    <a href="{{ route('admin.glasses.index') }}" class="text-xs text-orange-400 hover:text-orange-300 transition-colors">Voir tout →</a>
                </div>
                <div class="space-y-2">
                    @forelse($recentProducts as $product)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition-colors">
                            <div class="w-9 h-9 rounded-lg overflow-hidden bg-white/5 flex-shrink-0 flex items-center justify-center">
                                @if($product->imageExists())
                                    <img src="{{ $product->image }}" alt="" class="max-w-full max-h-full object-contain mix-blend-multiply">
                                @else
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white truncate">{{ $product->name }}</p>
                                <p class="text-xs text-heroi-text-muted truncate">{{ $product->brand }} · {{ number_format($product->price, 2, ',', ' ') }} MAD</p>
                            </div>
                            <a href="{{ route('admin.glasses.edit', $product) }}" class="text-xs text-heroi-text-muted hover:text-white transition-colors flex-shrink-0">Modifier</a>
                        </div>
                    @empty
                        <p class="text-sm text-heroi-text-muted text-center py-4">Aucun produit</p>
                    @endforelse
                </div>
            </div>

            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white">Avis récents</h3>
                    <a href="{{ route('admin.testimonials.index') }}" class="text-xs text-orange-400 hover:text-orange-300 transition-colors">Voir tout →</a>
                </div>
                <div class="space-y-2">
                    @forelse($recentTestimonials as $testimonial)
                        <div class="p-3 rounded-lg hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-6 h-6 rounded-full bg-orange-500/20 flex items-center justify-center text-xs font-medium text-orange-300">{{ $testimonial->avatar_initials ?: substr($testimonial->name, 0, 2) }}</span>
                                <span class="text-sm text-white font-medium">{{ $testimonial->name }}</span>
                                <span class="text-xs text-heroi-text-muted">{{ $testimonial->role }}</span>
                            </div>
                            <p class="text-xs text-heroi-text-muted line-clamp-2">{{ $testimonial->text }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-heroi-text-muted text-center py-4">Aucun avis</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="admin-card p-6">
            <h2 class="text-sm font-semibold text-white mb-4">Actions rapides</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('admin.orders.pending') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 hover:bg-amber-500/20 transition-all text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Vérifier commandes
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 transition-all text-sm">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Toutes les commandes
                </a>
                <a href="{{ route('admin.glasses.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 transition-all text-sm">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajouter un produit
                </a>
                <a href="{{ route('admin.glasses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 transition-all text-sm">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Gérer les produits
                </a>
            </div>
        </div>
    </div>
@endsection
