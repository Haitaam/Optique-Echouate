@extends('layouts.app')

@section('title', 'Admin — Notifications')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Notifications</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    {{ $notifications->total() }} notifications
                </p>
            </div>
            @if ($notifications->total() > 0)
                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white text-xs transition-colors">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        <div class="admin-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Titre</th>
                            <th class="hidden sm:table-cell">Message</th>
                            <th>Statut</th>
                            <th class="hidden md:table-cell">Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $n)
                            <tr class="{{ !$n->read ? 'bg-white/5' : '' }}">
                                <td>
                                    <span class="text-heroi-text-muted text-sm">
                                        @if($n->type === 'new_order') Nouvelle commande
                                        @elseif($n->type === 'order_cancelled') Annulation
                                        @elseif($n->type === 'low_stock') Stock faible
                                        @elseif($n->type === 'out_of_stock') Rupture de stock
                                        @elseif($n->type === 'new_review') Nouvel avis
                                        @elseif($n->type === 'contact') Contact
                                        @else {{ $n->type }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="text-white text-sm font-medium">{{ $n->title }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm line-clamp-1">{{ $n->body }}</span>
                                </td>
                                <td>
                                    @if($n->read)
                                        <span class="admin-badge admin-badge-success">Lu</span>
                                    @else
                                        <span class="admin-badge admin-badge-warning">Non lu</span>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $n->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$n->read)
                                            <form action="{{ route('admin.notifications.read', $n) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 hover:bg-emerald-500/20 text-xs transition-all">
                                                    Marquer lu
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <p class="text-sm">Aucune notification.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
