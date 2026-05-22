@extends('layouts.app')

@section('title', 'Ajouter des Lunettes')

@php $hideNav = true; @endphp

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.glasses.index') }}" class="text-heroi-text-muted hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Ajouter des Lunettes</h1>
            <p class="text-heroi-text-muted text-sm mt-0.5">Importez une ou plusieurs images</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.glasses.upload') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="rounded-2xl border border-dashed border-white/10 bg-white/[0.02] p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-heroi-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="text-white font-medium mb-1">Glissez vos images ici</p>
            <p class="text-heroi-text-muted text-sm mb-4">ou cliquez pour sélectionner (JPG, PNG, WebP — max 10 Mo)</p>
            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" required
                class="w-full text-sm text-heroi-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-orange-500/20 file:text-orange-300 hover:file:bg-orange-500/30 cursor-pointer">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Catégorie</label>
                <select name="category" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40" required>
                    <option value="men">Homme — Optique</option>
                    <option value="women">Femme — Optique</option>
                    <option value="sunglasses/men">Homme — Solaire</option>
                    <option value="sunglasses/women">Femme — Solaire</option>
                    <option value="luxury">Luxe</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Genre</label>
                <select name="gender" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40" required>
                    <option value="men">Homme</option>
                    <option value="women">Femme</option>
                    <option value="unisex">Unisexe</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Nom du produit</label>
                <input type="text" name="name" placeholder="— Détection automatique —"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Prix (MAD)</label>
                <input type="number" name="price" step="0.01" min="0" placeholder="Ex: 299"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Forme</label>
                <select name="frame_shape" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    <option value="">— Détection automatique —</option>
                    <option value="Round">Ronde</option>
                    <option value="Square">Carrée</option>
                    <option value="Rectangle">Rectangulaire</option>
                    <option value="Aviator">Aviateur</option>
                    <option value="Cat-eye">Œil de chat</option>
                    <option value="Butterfly">Papillon</option>
                    <option value="Wrap">Enveloppante</option>
                    <option value="Oval">Ovale</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Matière</label>
                <select name="material" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    <option value="">— Détection automatique —</option>
                    <option value="Acetate">Acétate</option>
                    <option value="Titanium">Titane</option>
                    <option value="Stainless Steel">Acier Inoxydable</option>
                    <option value="Metal">Métal</option>
                    <option value="Polycarbonate">Polycarbonate</option>
                    <option value="TR-90">TR-90</option>
                    <option value="Wood">Bois</option>
                    <option value="Aluminum">Aluminium</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Couleur dominante</label>
                <select name="color" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    <option value="">— Détection automatique —</option>
                    <option value="black">Noir</option>
                    <option value="matte black">Noir Mat</option>
                    <option value="gold">Or</option>
                    <option value="rose gold">Or Rose</option>
                    <option value="silver">Argent</option>
                    <option value="gunmetal">Gunmetal</option>
                    <option value="tortoise">Tortoise</option>
                    <option value="crystal">Crystal</option>
                    <option value="white">Blanc</option>
                    <option value="blue">Bleu</option>
                    <option value="brown">Marron</option>
                    <option value="green">Vert</option>
                    <option value="red">Rouge</option>
                    <option value="purple">Violet</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Tags</label>
                <input type="text" name="tags" placeholder="Ex: vintage, oversize, écologique"
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
            </div>
        </div>

        <p class="text-xs text-heroi-text-muted">
            Les métadonnées (marque, forme) seront détectées automatiquement depuis le nom du fichier.
            Vous pourrez tout modifier après import. Les prix sont en dirhams marocains (MAD).
        </p>

        <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
            <a href="{{ route('admin.glasses.index') }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                Annuler
            </a>
            <x-button variant="primary" type="submit">
                Importer
            </x-button>
        </div>
    </form>
</div>
@endsection
