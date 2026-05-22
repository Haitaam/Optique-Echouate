@extends('layouts.app')

@section('title', 'Admin — Gestion des Lunettes')

@php $hideNav = true; @endphp

@section('content')
<div class="min-h-screen bg-heroi-bg">
    {{-- Confirm Modal --}}
    <div x-data="adminConfirm"
        x-show="open"
        x-cloak
        @keydown.escape.window="cancel()"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity.duration.200ms class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="cancel()"></div>
        <div x-show="open" x-transition.duration.300ms class="relative z-10 w-full max-w-sm rounded-2xl border border-white/10 bg-heroi-bg-alt p-6 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <p class="text-white text-sm font-medium" x-text="message"></p>
            </div>
            <div class="flex justify-end gap-2">
                <button @click="cancel()" class="px-4 py-2 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">Annuler</button>
                <button @click="confirm()" class="px-4 py-2 rounded-xl bg-orange-500/20 text-orange-300 border border-orange-500/30 hover:bg-orange-500/30 text-sm font-medium transition-all">Confirmer</button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-data="toast"
        x-show="visible"
        x-transition.duration.300ms
        x-cloak
        class="fixed top-4 right-4 z-[9999] w-full max-w-sm">
        <div class="flex items-center gap-3 px-5 py-4 rounded-xl border shadow-2xl backdrop-blur-xl"
            :class="type === 'success' ? 'bg-emerald-500/15 border-emerald-500/25' : 'bg-red-500/15 border-red-500/25'">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                :class="type === 'success' ? 'bg-emerald-500/20' : 'bg-red-500/20'">
                <svg x-show="type === 'success'" class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="type === 'error'" class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-sm text-white font-medium" x-text="message"></p>
        </div>
    </div>

    {{-- Main Container --}}
    <div x-data="adminDashboard"
        x-init="init()"
        data-total="{{ $products->total() }}"
        data-all-ids="{{ json_encode($products->pluck('id')) }}"
        data-categories="{{ json_encode($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }}"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Gestion des Lunettes</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $products->total() }}</span> produits ·
                    <span class="text-emerald-400">{{ $stats['valid'] }}</span> valides ·
                    <span x-show="{{ $stats['broken'] }} > 0" class="text-red-400">{{ $stats['broken'] }} cassés</span>
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Smart Fix --}}
                <button @click="smartFix()" :disabled="smartFixing"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600/25 to-pink-600/25 border border-purple-500/30 text-purple-300 hover:from-purple-600/35 hover:to-pink-600/35 hover:border-purple-400/40 text-sm font-medium transition-all duration-200">
                    <svg class="w-4 h-4" :class="smartFixing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <span x-text="smartFixing ? 'Analyse...' : 'Smart Fix'"></span>
                </button>

                {{-- Sync --}}
                <form action="{{ route('admin.glasses.sync') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500/20 border border-orange-500/30 text-orange-300 hover:bg-orange-500/30 hover:border-orange-400/40 text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sync
                    </button>
                </form>

                {{-- Export --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-heroi-text-muted hover:text-white hover:bg-white/15 text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 mt-2 w-40 rounded-xl border border-white/10 bg-heroi-bg-alt shadow-2xl z-20 overflow-hidden">
                        <button @click="exportCSV(); open = false" class="w-full text-left px-4 py-3 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors">CSV</button>
                        <button @click="exportJSON(); open = false" class="w-full text-left px-4 py-3 text-sm text-heroi-text-muted hover:text-white hover:bg-white/5 transition-colors border-t border-white/5">JSON</button>
                    </div>
                </div>

                {{-- Add --}}
                <a href="{{ route('admin.glasses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all duration-200 shadow-lg shadow-orange-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajouter
                </a>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="admin-card admin-stat">
                <div class="admin-stat-value">{{ $stats['total'] }}</div>
                <div class="admin-stat-label">Total produits</div>
            </div>
            <div class="admin-card admin-stat">
                <div class="admin-stat-value text-emerald-400">{{ $stats['valid'] }}</div>
                <div class="admin-stat-label">Images valides</div>
            </div>
            <div class="admin-card admin-stat">
                <div class="admin-stat-value" :class="{{ $stats['broken'] }} > 0 ? 'text-red-400' : 'text-white'">{{ $stats['broken'] }}</div>
                <div class="admin-stat-label">Images manquantes</div>
            </div>
            <div class="admin-card admin-stat">
                <div class="admin-stat-value text-purple-400">{{ $stats['categories'] }}</div>
                <div class="admin-stat-label">Catégories</div>
            </div>
        </div>

        {{-- Filters + Search --}}
        <div class="admin-card p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-heroi-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <form method="GET" action="{{ route('admin.glasses.index') }}" id="admin-search-form">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Rechercher par nom, marque..."
                            x-on:input.debounce.400ms="$el.form.submit()"
                            class="w-full pl-9 pr-3 py-2 rounded-lg bg-white/[0.04] border border-white/10 text-white text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-1 focus:ring-orange-500/40 transition-all">
                    </form>
                </div>
                <button @click="filterOpen = !filterOpen" class="ml-3 px-3 py-2 rounded-lg bg-white/[0.04] border border-white/10 text-heroi-text-muted hover:text-white text-xs transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                </button>
            </div>

            {{-- Filter fields --}}
            <div x-show="filterOpen" x-collapse.duration.200ms x-cloak>
                <form method="GET" action="{{ route('admin.glasses.index') }}" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 pt-3 border-t border-white/5">
                    <select name="brand" onchange="this.form.submit()" class="admin-filter-select">
                        <option value="">Toutes les marques</option>
                        @foreach ($brands as $b)
                            <option value="{{ $b }}" @selected(request('brand') === $b)>{{ $b }}</option>
                        @endforeach
                    </select>
                    <select name="gender" onchange="this.form.submit()" class="admin-filter-select">
                        <option value="">Tous les genres</option>
                        @foreach ($genders as $g)
                            <option value="{{ $g }}" @selected(request('gender') === $g)>{{ $g }}</option>
                        @endforeach
                    </select>
                    <select name="category" onchange="this.form.submit()" class="admin-filter-select">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->slug }}" @selected(request('category') === $c->slug)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select name="frame_shape" onchange="this.form.submit()" class="admin-filter-select">
                        <option value="">Toutes les formes</option>
                        @foreach ($shapes as $s)
                            <option value="{{ $s }}" @selected(request('frame_shape') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <select name="color" onchange="this.form.submit()" class="admin-filter-select">
                        <option value="">Toutes les couleurs</option>
                        @foreach ($colors as $c)
                            <option value="{{ $c }}" @selected(request('color') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Bulk action bar --}}
        <div x-show="hasSelection"
            x-transition.duration.200ms
            class="admin-card p-3 mb-4 flex items-center justify-between flex-wrap gap-2"
            style="border-color: rgba(249,115,22,0.3);">
            <span class="text-sm text-white font-medium">
                <span x-text="selectedCount" class="text-orange-400"></span> produit(s) sélectionné(s)
            </span>
            <div class="flex items-center gap-2 flex-wrap">
                <button @click="showBulkEdit()" class="px-3 py-1.5 rounded-lg bg-blue-500/15 border border-blue-500/25 text-blue-300 hover:bg-blue-500/25 text-xs font-medium transition-all">
                    Modifier en lot
                </button>
                <button @click="confirmBulkSyncFix()" class="px-3 py-1.5 rounded-lg bg-emerald-500/15 border border-emerald-500/25 text-emerald-300 hover:bg-emerald-500/25 text-xs font-medium transition-all">
                    Vérifier images
                </button>
                <button @click="confirmBulkDeleteDbOnly()" class="px-3 py-1.5 rounded-lg bg-amber-500/15 border border-amber-500/25 text-amber-300 hover:bg-amber-500/25 text-xs font-medium transition-all">
                    Retirer de la base
                </button>
                <button @click="confirmBulkDelete()" class="px-3 py-1.5 rounded-lg bg-red-500/15 border border-red-500/25 text-red-300 hover:bg-red-500/25 text-xs font-medium transition-all">
                    Supprimer
                </button>
            </div>
        </div>

        {{-- Bulk edit form --}}
        <div x-show="showingBulkForm" x-transition.duration.200ms x-cloak class="admin-card p-4 mb-4 border-blue-500/20">
            <h3 class="text-sm font-medium text-white mb-3">Modification en lot — <span x-text="selectedCount"></span> produit(s)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                <select x-model="bulkForm.brand" class="admin-filter-select">
                    <option value="">— Conserver la marque —</option>
                    @foreach ($brandList as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
                <select x-model="bulkForm.category_id" class="admin-filter-select">
                    <option value="">— Conserver la catégorie —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <input type="text" x-model="bulkForm.tags" placeholder="Tags (séparés par des virgules)" class="w-full rounded-lg bg-white/[0.04] border border-white/10 text-white px-3 py-2 text-xs placeholder-heroi-text-muted/50 focus:outline-none focus:ring-1 focus:ring-orange-500/40">
            </div>
            <div class="flex justify-end gap-2">
                <button @click="showingBulkForm = false" class="px-3 py-1.5 rounded-lg border border-white/10 text-heroi-text-muted hover:text-white text-xs transition-colors">Annuler</button>
                <button @click="submitBulkEdit()" class="px-3 py-1.5 rounded-lg bg-blue-500/20 text-blue-300 border border-blue-500/30 hover:bg-blue-500/30 text-xs font-medium transition-all">Appliquer</button>
            </div>
        </div>

        {{-- Table --}}
        <div class="admin-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox"
                                    :checked="selectAll"
                                    @change="toggleSelectAll()"
                                    class="w-4 h-4 rounded border-white/20 bg-white/5 text-orange-500 focus:ring-orange-500/40 cursor-pointer">
                            </th>
                            <th class="w-14"></th>
                            <th>Produit</th>
                            <th class="hidden md:table-cell">Marque</th>
                            <th class="hidden lg:table-cell">Genre</th>
                            <th class="hidden sm:table-cell">Forme</th>
                            <th class="hidden sm:table-cell">Couleur</th>
                            <th class="hidden sm:table-cell">Prix</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                        :checked="selected.has({{ $product->id }})"
                                        @change="toggleSelect({{ $product->id }})"
                                        class="w-4 h-4 rounded border-white/20 bg-white/5 text-orange-500 focus:ring-orange-500/40 cursor-pointer">
                                </td>
                                <td>
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-white/5 flex items-center justify-center flex-shrink-0">
                                        @if ($product->imageExists())
                                            <img src="{{ $product->image }}" alt=""
                                                class="max-w-full max-h-full object-contain mix-blend-multiply hover:scale-110 transition-transform duration-300">
                                        @else
                                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white text-sm font-medium truncate max-w-[180px] sm:max-w-xs">{{ $product->name }}</div>
                                    <div class="text-heroi-text-muted text-xs truncate max-w-[180px] sm:max-w-xs">{{ $product->brand }} · {{ $product->color }}</div>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $product->brand }}</span>
                                </td>
                                <td class="hidden lg:table-cell">
                                    <span class="admin-badge"
                                        :class="'{{ $product->gender }}' === 'Men' ? 'admin-badge-info' : '{{ $product->gender }}' === 'Women' ? 'admin-badge-success' : 'admin-badge-warning'">
                                        {{ $product->gender }}
                                    </span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm capitalize">{{ $product->frame_shape }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full border border-white/10 block" style="background: {{ match(strtolower($product->color)) { 'gold' => '#FFD700', 'rose gold' => '#B76E79', 'silver' => '#C0C0C0', 'gunmetal' => '#2C3539', 'black' => '#000', 'matte black' => '#1a1a1a', 'tortoise' => '#8B6914', 'crystal' => '#E8E8E8', 'white' => '#fff', 'blue' => '#3B82F6', 'red' => '#EF4444', default => '#f97316' } }}"></span>
                                        <span class="text-heroi-text-muted text-sm capitalize">{{ $product->color }}</span>
                                    </span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-white text-sm font-medium">{{ number_format($product->price * 10, 0, ',', ' ') }}</span>
                                    <span class="text-heroi-text-muted text-xs">MAD</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if (!$product->imageExists())
                                            <span class="admin-badge admin-badge-danger text-[10px] px-1.5 py-0.5">X</span>
                                        @endif
                                        <a href="{{ route('admin.glasses.edit', $product) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                            Modifier
                                        </a>
                                        <form action="{{ route('admin.glasses.destroy', $product) }}" method="POST"
                                            onsubmit="return confirm('Supprimer « {{ addslashes($product->name) }} » ?');" class="inline">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="delete_file" value="1">
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
                                <td colspan="9" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-sm">Aucun produit trouvé</p>
                                    <p class="text-xs mt-1">Ajoutez des images ou lancez une synchronisation.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $products->appends(request()->query())->links() }}
        </div>

        {{-- Disk files accordion --}}
        <details class="mt-8 admin-card p-4">
            <summary class="text-heroi-text-muted text-sm cursor-pointer hover:text-white transition-colors font-medium">
                Fichiers sur le disque ({{ collect($diskFiles)->sum(fn($f) => count($f)) }})
            </summary>
            <div class="mt-3 space-y-2">
                @foreach ($diskFiles as $folder => $files)
                    <div>
                        <span class="text-xs text-orange-400 font-medium">{{ $folder ?: '/' }}</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach ($files as $f)
                                <span class="text-xs text-heroi-text-muted bg-white/5 px-2 py-0.5 rounded">{{ $f }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </details>
    </div>
</div>
@endsection
