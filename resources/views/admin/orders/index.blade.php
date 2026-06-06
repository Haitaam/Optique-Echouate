@extends('layouts.app')

@section('title', 'Admin — Commandes')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Commandes</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $orders->total() }}</span> commandes
                    @if($pendingCount ?? 0 > 0)
                        <span class="inline-flex items-center gap-1 ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-300 border border-amber-500/20">
                            🟡 {{ $pendingCount }} en attente
                        </span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.pending') }}"
                    class="px-3 py-2 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 hover:bg-amber-500/20 text-xs font-medium transition-all">
                    En attente de confirmation
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-emerald-300 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20">
                <p class="text-red-300 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <div class="admin-card p-4 mb-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-heroi-text-muted focus:outline-none focus:border-orange-500 transition-colors">
                </div>
                <div>
                    <select name="status"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-orange-500 transition-colors">
                        <option value="">Tous les statuts</option>
                        @foreach(App\Models\Order::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-orange-500 transition-colors [color-scheme:dark]">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-orange-500 transition-colors [color-scheme:dark]">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 px-3 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.orders.index') }}"
                        class="px-3 py-2 rounded-lg border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <div class="admin-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => ($sortBy === 'id' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-orange-400 transition-colors">
                                    # @if($sortBy === 'id')<span class="text-orange-400">{{ $sortOrder === 'asc' ? '▲' : '▼' }}</span>@endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($sortBy === 'name' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-orange-400 transition-colors">
                                    Client @if($sortBy === 'name')<span class="text-orange-400">{{ $sortOrder === 'asc' ? '▲' : '▼' }}</span>@endif
                                </a>
                            </th>
                            <th class="hidden sm:table-cell">Téléphone</th>
                            <th class="hidden sm:table-cell">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_price', 'sort_order' => ($sortBy === 'total_price' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-orange-400 transition-colors">
                                    Total @if($sortBy === 'total_price')<span class="text-orange-400">{{ $sortOrder === 'asc' ? '▲' : '▼' }}</span>@endif
                                </a>
                            </th>
                            <th>Statut</th>
                            <th class="hidden md:table-cell">Paiement</th>
                            <th class="hidden lg:table-cell">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($sortBy === 'created_at' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-orange-400 transition-colors">
                                    Date @if($sortBy === 'created_at')<span class="text-orange-400">{{ $sortOrder === 'asc' ? '▲' : '▼' }}</span>@endif
                                </a>
                            </th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>
                                    <span class="text-white text-sm font-medium">#{{ $order->id }}</span>
                                </td>
                                <td>
                                    <div class="text-white text-sm font-medium">{{ $order->name }}</div>
                                    <div class="text-heroi-text-muted text-xs">{{ $order->email ?: $order->phone }}</div>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $order->phone }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-white text-sm font-medium">{{ number_format($order->total_price, 2, ',', ' ') }}</span>
                                    <span class="text-heroi-text-muted text-xs">MAD</span>
                                </td>
                                <td>
                                    <span class="admin-badge inline-flex items-center gap-1 {{ $order->badge_class }}">
                                        {{ $order->status_icon }} {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell">
                                    @php
                                        $paymentLabels = [
                                            'pending' => 'En attente',
                                            'paid' => 'Payé',
                                            'failed' => 'Échoué',
                                            'refunded' => 'Remboursé',
                                        ];
                                        $paymentClasses = [
                                            'pending' => 'admin-badge-warning',
                                            'paid' => 'admin-badge-success',
                                            'failed' => 'admin-badge-danger',
                                            'refunded' => 'admin-badge-info',
                                        ];
                                    @endphp
                                    <span class="admin-badge {{ $paymentClasses[$order->payment_status] ?? 'admin-badge-warning' }}">
                                        {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($order->status === App\Models\Order::STATUS_PENDING)
                                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 hover:bg-emerald-500/20 text-xs transition-all font-medium">
                                                    Confirmer
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Annuler la commande #{{ $order->id }} ?');">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 text-xs transition-all">
                                                    Annuler
                                                </button>
                                            </form>
                                        @elseif($order->status !== App\Models\Order::STATUS_DELIVERED && $order->status !== App\Models\Order::STATUS_CANCELLED)
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" type="button"
                                                    class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                                    Statut ▾
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-cloak
                                                    class="absolute right-0 top-full mt-1 z-50 w-48 py-1 rounded-xl bg-[#1a1a1a] border border-white/10 shadow-xl">
                                                    @foreach(App\Models\Order::STATUSES as $newStatus)
                                                        @if($order->canTransitionTo($newStatus))
                                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="status" value="{{ $newStatus }}">
                                                                <button type="submit"
                                                                    class="w-full text-left px-3 py-2 text-xs text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors flex items-center gap-2">
                                                                    <span>{{ App\Models\Order::STATUS_ICONS[$newStatus] ?? '•' }}</span>
                                                                    <span>{{ App\Models\Order::STATUS_LABELS[$newStatus] ?? $newStatus }}</span>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                            Voir
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                            onsubmit="return confirm('Supprimer la commande #{{ $order->id }} ?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 text-xs transition-all">
                                                Suppr.
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <p class="text-sm">Aucune commande trouvée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
