@extends('layouts.app')

@section('title', 'Modifier — ' . $product->name)

@php $hideNav = true; @endphp

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.glasses.index') }}" class="text-heroi-text-muted hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Modifier le Produit</h1>
            <p class="text-heroi-text-muted text-sm mt-0.5">#{{ $product->id }} — {{ $product->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.glasses.update', $product) }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Image preview + replace --}}
        @php $imgExists = $product->imageExists(); @endphp
        <div class="rounded-2xl overflow-hidden border border-white/10 bg-white/[0.02]">
            <div class="h-48 flex items-center justify-center bg-white/5 relative group">
                @if ($imgExists)
                    <img src="{{ $product->image }}" alt="" class="max-h-full max-w-full object-contain mix-blend-multiply">
                @else
                    <span class="text-red-400 text-sm">Image introuvable : {{ $product->image }}</span>
                @endif
            </div>
            <div class="px-4 py-2 text-xs text-heroi-text-muted border-t border-white/5 truncate flex items-center justify-between">
                <span>{{ $product->image }}</span>
                <label class="text-orange-400 hover:text-orange-300 cursor-pointer text-xs font-medium">
                    Remplacer l'image
                    <input type="file" name="replace_image" accept="image/jpeg,image/png,image/webp" class="hidden">
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Nom" name="name" :value="$product->name" required />
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Marque</label>
                <select name="brand" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    @foreach ($brands as $brand)
                        <option value="{{ $brand }}" @selected($product->brand === $brand)>{{ $brand }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Forme</label>
                <select name="frame_shape" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    @foreach ($shapes as $shape)
                        <option value="{{ $shape }}" @selected($product->frame_shape === $shape)>{{ $shape }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Genre</label>
                <select name="gender" class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    @foreach ($genders as $gender)
                        <option value="{{ $gender }}" @selected($product->gender === $gender)>{{ $gender }}</option>
                    @endforeach
                </select>
            </div>
            <x-input label="Matière" name="material" :value="$product->material" required />
            <x-input label="Couleur" name="color" :value="$product->color" required />
            <x-input label="Prix (MAD)" name="price" type="number" step="0.01" :value="$product->price" required />
            <x-input label="Stock" name="stock" type="number" min="0" :value="$product->stock ?? 0" required />
        </div>

        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" @checked($product->is_featured) class="w-4 h-4 rounded border-white/10 bg-white/5 text-orange-500 focus:ring-orange-500/40">
                <span class="text-sm text-white">En vedette</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_luxury" value="1" @checked($product->is_luxury) class="w-4 h-4 rounded border-white/10 bg-white/5 text-orange-500 focus:ring-orange-500/40">
                <span class="text-sm text-white">Luxe</span>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Catégories</label>
            <div class="flex flex-wrap gap-3">
                @foreach ($categories as $cat)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                            @checked($product->categories->contains($cat->id))
                            class="w-4 h-4 rounded border-white/10 bg-white/5 text-orange-500 focus:ring-orange-500/40">
                        <span class="text-sm text-white">{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <x-input label="Tags (séparés par des virgules)" name="style_tags"
            :value="is_array($product->style_tags) ? implode(', ', $product->style_tags) : $product->style_tags" />

        <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
            <a href="{{ route('admin.glasses.index') }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-heroi-text-muted hover:text-white text-sm transition-colors">
                Annuler
            </a>
            <x-button variant="primary" type="submit">
                Enregistrer
            </x-button>
        </div>
    </form>
</div>
@endsection
