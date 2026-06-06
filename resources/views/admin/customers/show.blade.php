@extends('layouts.app')

@section('title', 'Client — ' . $customer->name)

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.customers.index') }}" class="text-heroi-text-muted hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $customer->name }}</h1>
                <p class="text-heroi-text-muted text-sm mt-0.5">Client #{{ $customer->id }} · inscrit le {{ $customer->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Informations</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-heroi-text-muted">Nom</span>
                        <span class="text-white">{{ $customer->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-heroi-text-muted">Email</span>
                        <span class="text-white">{{ $customer->email ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-heroi-text-muted">Téléphone</span>
                        <span class="text-white">{{ $customer->phone ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-heroi-text-muted">Dernier IP</span>
                        <span class="text-white">{{ $customer->last_login_ip ?: '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Statistiques</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-heroi-text-muted">Commandes</span>
                        <span class="text-white font-medium">{{ $customer->orders->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-heroi-text-muted">Total dépensé</span>
                        <span class="text-emerald-400 font-medium">{{ number_format($customer->orders->sum('total_price'), 2, ',', ' ') }} MAD</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($customer->orders->count() > 0)
        <div class="admin-card p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Commandes ({{ $customer->orders->count() }})</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->orders as $order)
                            <tr>
                                <td>
                                    <span class="text-white text-sm font-medium">#{{ $order->id }}</span>
                                </td>
                                <td>
                                    <span class="text-white text-sm">{{ number_format($order->total_price, 2, ',', ' ') }} MAD</span>
                                </td>
                                <td>
                                    @php
                                        $statusLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
                                        $statusClasses = ['pending' => 'admin-badge-warning', 'processing' => 'admin-badge-info', 'completed' => 'admin-badge-success', 'cancelled' => 'admin-badge-danger'];
                                    @endphp
                                    <span class="admin-badge {{ $statusClasses[$order->status] ?? 'admin-badge-warning' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                                </td>
                                <td>
                                    @php
                                        $paymentLabels = ['pending' => 'En attente', 'paid' => 'Payé', 'failed' => 'Échoué', 'refunded' => 'Remboursé'];
                                        $paymentClasses = ['pending' => 'admin-badge-warning', 'paid' => 'admin-badge-success', 'failed' => 'admin-badge-danger', 'refunded' => 'admin-badge-info'];
                                    @endphp
                                    <span class="admin-badge {{ $paymentClasses[$order->payment_status] ?? 'admin-badge-warning' }}">{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</span>
                                </td>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $order->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('admin.customers.index') }}"
                class="px-5 py-2.5 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
        </div>
    </div>
@endsection
