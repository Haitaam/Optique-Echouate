@extends('layouts.app')

@section('title', 'Admin — Témoignages Clients')

@php $hideNav = true; @endphp

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Témoignages & Avis</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $testimonials->count() }}</span> au total ·
                    <span class="text-emerald-400">{{ $adminCount }}</span> admin ·
                    <span class="text-amber-400">{{ $visitorCount }}</span> visiteurs
                </p>
            </div>
            <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajouter
            </a>
        </div>

        {{-- Filter tabs --}}
        <div class="flex items-center gap-1 mb-6 p-1 rounded-xl bg-white/[0.03] border border-white/5 w-fit">
            <a href="{{ route('admin.testimonials.index') }}"
                class="px-4 py-2 text-xs font-medium rounded-lg transition-all {{ !request('source') ? 'bg-white/10 text-white' : 'text-heroi-text-muted hover:text-white' }}">
                Tous
            </a>
            <a href="{{ route('admin.testimonials.index', ['source' => 'admin']) }}"
                class="px-4 py-2 text-xs font-medium rounded-lg transition-all {{ request('source') === 'admin' ? 'bg-white/10 text-white' : 'text-heroi-text-muted hover:text-white' }}">
                Admin
            </a>
            <a href="{{ route('admin.testimonials.index', ['source' => 'visitor']) }}"
                class="px-4 py-2 text-xs font-medium rounded-lg transition-all {{ request('source') === 'visitor' ? 'bg-white/10 text-white' : 'text-heroi-text-muted hover:text-white' }}">
                Visiteurs
            </a>
        </div>

        <div class="space-y-3">
            @forelse ($testimonials as $t)
                <div class="rounded-2xl border border-white/[0.06] bg-white/[0.02] p-5 hover:bg-white/[0.04] transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full hero-gradient flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                            {{ $t->avatar_initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                                <span class="font-semibold text-white">{{ $t->name }}</span>
                                @if ($t->role)
                                    <span class="text-xs text-heroi-text-muted">{{ $t->role }}</span>
                                @endif
                                <span class="admin-badge {{ $t->source === 'visitor' ? 'admin-badge-warning' : 'admin-badge-info' }}">
                                    {{ $t->source === 'visitor' ? 'Visiteur' : 'Admin' }}
                                </span>
                                @if (!$t->is_active)
                                    <span class="admin-badge admin-badge-danger">Masqué</span>
                                @endif
                                @if ($t->source === 'visitor')
                                    <span class="text-[10px] text-heroi-text-muted">{{ $t->created_at->diffForHumans() }}</span>
                                @endif
                                <span class="text-[10px] text-heroi-text-muted ml-auto">Ordre : {{ $t->sort_order }}</span>
                            </div>
                            <p class="text-sm text-heroi-text-muted leading-relaxed">&ldquo;{{ $t->text }}&rdquo;</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <a href="{{ route('admin.testimonials.edit', $t) }}"
                                class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-heroi-text-muted hover:text-white hover:bg-white/10 text-xs transition-all">
                                Modifier
                            </a>
                            <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST"
                                onsubmit="return confirm('Supprimer ce témoignage ?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 text-xs transition-all">
                                    Suppr.
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-heroi-text-muted">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <p class="text-sm">Aucun témoignage pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
