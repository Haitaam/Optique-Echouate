@extends('layouts.app')

@section('title', 'Admin — Clients')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Clients</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $customers->total() }}</span> clients
                </p>
            </div>
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
                            <th>Nom</th>
                            <th class="hidden sm:table-cell">Email</th>
                            <th class="hidden sm:table-cell">Téléphone</th>
                            <th class="hidden md:table-cell">Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $customer->id }}</span>
                                </td>
                                <td>
                                    <span class="text-white text-sm font-medium">{{ $customer->name }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $customer->email ?: '—' }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $customer->phone ?: '—' }}</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $customer->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.customers.show', $customer) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                            Voir
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST"
                                            onsubmit="return confirm('Supprimer le client « {{ addslashes($customer->name) }} » ?');" class="inline">
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
                                <td colspan="6" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="text-sm">Aucun client trouvé.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
