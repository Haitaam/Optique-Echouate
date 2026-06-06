@extends('layouts.app')

@section('title', 'Modifier — ' . $gender->name)

@php $hideNav = true; @endphp

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.genders.index') }}" class="text-heroi-text-muted hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Modifier le Genre</h1>
                <p class="text-heroi-text-muted text-sm mt-0.5">#{{ $gender->id }} — {{ $gender->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.genders.update', $gender) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Nom *</label>
                    <input type="text" name="name" value="{{ old('name', $gender->name) }}" required
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                </div>
                <div>
                    <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug', $gender->slug) }}" required
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Description</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 resize-none">{{ old('description', $gender->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
                <a href="{{ route('admin.genders.index') }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/30">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection
