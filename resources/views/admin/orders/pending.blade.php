@extends('layouts.app')

@section('title', 'Admin — Commandes en attente')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    🟡 Commandes en attente de confirmation
                </h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-amber-300 font-medium">{{ $orders->total() }}</span> commande(s) en attente de vérification
                </p>
            </div>
            <a href="{{ route('admin.orders.index') }}"
                class="px-3 py-2 rounded-lg border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                Toutes les commandes
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-emerald-300 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <div class="admin-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Articles</th>
                            <th>Total</th>
                            <th class="hidden md:table-cell">Date</th>
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
                                    <div class="text-heroi-text-muted text-xs">{{ $order->email ?: '—' }}</div>
                                </td>
                                <td>
                                    <span class="text-white text-sm font-mono">{{ $order->phone }}</span>
                                </td>
                                <td>
                                    @php $pendingItems = $order->orderItems ?? collect(); @endphp
                                    @if($pendingItems->isNotEmpty())
                                        <div class="text-xs text-heroi-text-muted max-w-[200px] truncate">
                                            @foreach($pendingItems as $item)
                                                <div>{{ $item->product?->name ?? '#' . $item->product_id }} x{{ $item->quantity }}</div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-heroi-text-muted text-xs">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-white text-sm font-medium">{{ number_format($order->total_price, 2, ',', ' ') }}</span>
                                    <span class="text-heroi-text-muted text-xs">MAD</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white hover:bg-emerald-400 text-xs font-semibold transition-all shadow-lg shadow-emerald-500/30">
                                                ✅ Confirmer
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Annuler la commande #{{ $order->id }} ?');">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 text-xs transition-all">
                                                🔴 Annuler
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                            Voir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <p class="text-sm">Aucune commande en attente.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
