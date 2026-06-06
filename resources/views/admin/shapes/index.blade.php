@extends('layouts.app')

@section('title', 'Admin — Formes')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Formes</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $shapes->total() }}</span> formes
                </p>
            </div>
            <a href="{{ route('admin.shapes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajouter
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
                            <th>Nom</th>
                            <th>Slug</th>
                            <th class="hidden sm:table-cell">Description</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shapes as $shape)
                            <tr>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $shape->id }}</span>
                                </td>
                                <td>
                                    <span class="text-white text-sm font-medium capitalize">{{ $shape->name }}</span>
                                </td>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $shape->slug }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ Str::limit($shape->description, 60) }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.shapes.edit', $shape) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                            Modifier
                                        </a>
                                        <form action="{{ route('admin.shapes.destroy', $shape) }}" method="POST"
                                            onsubmit="return confirm('Supprimer la forme « {{ addslashes($shape->name) }} » ?');" class="inline">
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
                                <td colspan="5" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    <p class="text-sm">Aucune forme trouvée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $shapes->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
