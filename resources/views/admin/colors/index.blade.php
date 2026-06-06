@extends('layouts.app')

@section('title', 'Admin — Couleurs')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Couleurs</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $colors->total() }}</span> couleurs
                </p>
            </div>
            <a href="{{ route('admin.colors.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/30">
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
                            <th class="hidden sm:table-cell">Hex</th>
                            <th class="hidden md:table-cell">Aperçu</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($colors as $color)
                            <tr>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $color->id }}</span>
                                </td>
                                <td>
                                    <span class="text-white text-sm font-medium">{{ $color->name }}</span>
                                </td>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $color->slug }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <code class="text-heroi-text-muted text-sm">{{ $color->hex_value }}</code>
                                </td>
                                <td class="hidden md:table-cell">
                                    @if ($color->hex_value)
                                        <span class="inline-block w-6 h-6 rounded-full border border-white/10" style="background: {{ $color->hex_value }}"></span>
                                    @else
                                        <span class="text-heroi-text-muted text-xs">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.colors.edit', $color) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                            Modifier
                                        </a>
                                        <form action="{{ route('admin.colors.destroy', $color) }}" method="POST"
                                            onsubmit="return confirm('Supprimer la couleur « {{ addslashes($color->name) }} » ?');" class="inline">
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
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                    <p class="text-sm">Aucune couleur trouvée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $colors->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
