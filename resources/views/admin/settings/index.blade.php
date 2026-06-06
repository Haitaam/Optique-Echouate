@extends('layouts.app')

@section('title', 'Admin — Paramètres')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Paramètres</h1>
            <p class="text-sm text-heroi-text-muted mt-1">Configuration générale du site</p>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-emerald-300 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5" enctype="multipart/form-data">
            @csrf

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Informations générales</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Nom du site</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Optique Échouate') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Email de contact</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Téléphone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Adresse</label>
                        <input type="text" name="address" value="{{ old('address', $settings['address'] ?? '') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Pied de page</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Profession / Titre</label>
                        <input type="text" name="profession" value="{{ old('profession', $settings['profession'] ?? 'Opticien - Optométriste') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Ville</label>
                        <input type="text" name="city" value="{{ old('city', $settings['city'] ?? 'Asilah') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Horaires d'ouverture</label>
                        <input type="text" name="working_hours" value="{{ old('working_hours', $settings['working_hours'] ?? 'Du lundi au samedi : 10h - 21h') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Lien Google Maps</label>
                        <input type="url" name="maps_url" value="{{ old('maps_url', $settings['maps_url'] ?? 'https://maps.app.goo.gl/fXQCFf24qHRWArKN8?g_st=ipc') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Favicon</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Image du favicon</label>
                        @if(isset($settings['favicon']) && $settings['favicon'])
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ $settings['favicon'] }}" alt="Favicon actuel" class="w-10 h-10 rounded-lg border border-white/10 object-contain bg-white/5">
                                <span class="text-xs text-heroi-text-muted">Favicon actuel</span>
                            </div>
                        @endif
                        <input type="file" name="favicon" accept="image/png,image/x-icon,image/svg+xml"
                            class="w-full text-sm text-heroi-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-orange-500/10 file:text-orange-400 hover:file:bg-orange-500/20 transition-colors">
                        <p class="text-xs text-heroi-text-muted/60 mt-1.5">Format PNG, ICO ou SVG. Taille recommandée : 192×192 px</p>
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Réseaux sociaux</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Instagram</label>
                        <input type="text" name="instagram" value="{{ old('instagram', $settings['instagram'] ?? '') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Facebook</label>
                        <input type="text" name="facebook" value="{{ old('facebook', $settings['facebook'] ?? '') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Livraison & Commandes</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Frais de livraison (MAD)</label>
                        <input type="number" name="shipping_fee" step="0.01" value="{{ old('shipping_fee', $settings['shipping_fee'] ?? '30') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-heroi-text-muted mb-1.5">Seuil livraison gratuite (MAD)</label>
                        <input type="number" name="free_shipping_threshold" step="0.01" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '500') }}"
                            class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-2.5 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-white/5">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-semibold transition-all shadow-lg shadow-orange-500/30">
                    Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
@endsection
