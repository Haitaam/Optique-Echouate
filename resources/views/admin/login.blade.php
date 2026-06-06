<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — Connexion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-heroi-bg flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold hero-gradient-text">Optique Échouate</h1>
            <p class="text-heroi-text-muted text-sm mt-1">Administration</p>
        </div>

        <div class="glass-strong rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Connexion</h2>

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-300 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-1">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-white/5 text-orange-500 focus:ring-orange-500/40">
                        <span class="text-sm text-heroi-text-muted">Se souvenir de moi</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs text-orange-400 hover:text-orange-300 transition-colors">Mot de passe oublié ?</a>
                </div>
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-400 text-sm font-medium transition-all shadow-lg shadow-orange-500/25">
                    Se connecter
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-heroi-text-muted mt-6">
            <a href="{{ route('home') }}" class="hover:text-orange-400 transition-colors">← Retour au site</a>
        </p>
    </div>
</body>
</html>
