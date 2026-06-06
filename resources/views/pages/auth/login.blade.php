@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Connexion</h1>
            <p class="text-heroi-text-muted">Connectez-vous pour accéder à votre compte.</p>
        </div>

        <div class="glass-strong rounded-3xl p-6 sm:p-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-300 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-1.5">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-white/5 text-orange-500 focus:ring-orange-500/40">
                        <span class="text-sm text-heroi-text-muted">Se souvenir de moi</span>
                    </label>
                </div>
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all shadow-lg shadow-orange-500/25">
                    Se connecter
                </button>
            </form>

            <p class="text-center text-sm text-heroi-text-muted mt-6">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-orange-400 hover:text-orange-300 transition-colors font-medium">S'inscrire</a>
            </p>
        </div>
    </div>
</section>
@endsection
