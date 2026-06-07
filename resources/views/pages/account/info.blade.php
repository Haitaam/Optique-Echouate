@extends('layouts.app')

@section('title', 'Mes informations')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        <div class="mb-8">
            <a href="{{ route('profile') }}" class="text-orange-400 hover:text-orange-300 text-sm transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour au tableau de bord
            </a>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mes informations</h1>
            <p class="text-heroi-text-muted">Gérez vos informations personnelles.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                <p class="text-emerald-300 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-center">
                <p class="text-red-300 text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('account.info.update') }}" class="glass-strong rounded-3xl p-6 sm:p-8 space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-white mb-2">Nom complet</label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/30 transition-all text-sm">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-white mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/30 transition-all text-sm">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-white mb-2">Téléphone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/30 transition-all text-sm">
                @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-white/5">

            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Changer de mot de passe (optionnel)</h3>
            </div>

            <div>
                <label for="current_password" class="block text-sm font-medium text-white mb-2">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password"
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/30 transition-all text-sm">
                @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-white mb-2">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password"
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/30 transition-all text-sm">
                @error('new_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-white mb-2">Confirmer le nouveau mot de passe</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-heroi-text-muted/50 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/30 transition-all text-sm">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all shadow-lg shadow-orange-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>

    </div>
</section>
@endsection
