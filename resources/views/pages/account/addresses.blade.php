@extends('layouts.app')

@section('title', 'Mes adresses')

@section('content')
<section class="relative min-h-screen pt-32 pb-20">
    <div class="absolute inset-0 bg-gradient-to-b from-heroi-bg via-heroi-bg-alt/30 to-heroi-bg pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mes adresses</h1>
            <p class="text-heroi-text-muted">Gérez vos adresses de livraison.</p>
        </div>

        <div class="glass-strong rounded-3xl p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-heroi-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-heroi-text-muted text-lg font-medium">Page adresses en cours de développement</p>
            <p class="text-heroi-text-muted/60 text-sm mt-2">Cette fonctionnalité sera bientôt disponible.</p>
        </div>
    </div>
</section>
@endsection
