@extends('layouts.app')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Mot de passe oublié</h1>
            <p class="text-heroi-text-muted">Saisissez votre email pour recevoir un lien de réinitialisation.</p>
        </div>

        <div class="glass-strong rounded-3xl p-6 sm:p-8">
            <div class="mb-4 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-sm text-center">
                Fonctionnalité à venir. Contactez-nous pour réinitialiser votre mot de passe.
            </div>
            <form class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-1.5">Email</label>
                    <input type="email" name="email" id="email" placeholder="votre@email.com"
                        class="w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-heroi-text-muted/50 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                </div>
                <button type="button" disabled
                    class="w-full py-3 rounded-xl bg-orange-500/50 text-white/50 text-sm font-medium cursor-not-allowed">
                    Envoyer le lien
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
