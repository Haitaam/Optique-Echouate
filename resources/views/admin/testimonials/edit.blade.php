@extends('layouts.app')

@section('title', 'Modifier le Témoignage')

@php $hideNav = true; @endphp

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.testimonials.index') }}" class="text-heroi-text-muted hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Modifier le Témoignage</h1>
            <p class="text-heroi-text-muted text-sm mt-0.5">#{{ $testimonial->id }} — {{ $testimonial->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Nom *</label>
                <input type="text" name="name" value="{{ old('name', $testimonial->name) }}" required
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Rôle</label>
                <input type="text" name="role" value="{{ old('role', $testimonial->role) }}"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Texte du témoignage *</label>
            <textarea name="text" rows="4" required
                class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 resize-none">{{ old('text', $testimonial->text) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Initiales</label>
                <input type="text" name="avatar_initials" value="{{ old('avatar_initials', $testimonial->avatar_initials) }}" maxlength="4"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Ordre d'affichage</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" min="0"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
        </div>

        <label class="flex items-center gap-2.5 cursor-pointer pt-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->is_active))
                class="w-4 h-4 rounded border-white/10 bg-white/5 text-orange-500 focus:ring-orange-500/40">
            <span class="text-sm text-white">Actif (affiché sur le site)</span>
        </label>

        <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
            <a href="{{ route('admin.testimonials.index') }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                Annuler
            </a>
            <x-button variant="primary" type="submit">Enregistrer</x-button>
        </div>
    </form>
</div>
@endsection
