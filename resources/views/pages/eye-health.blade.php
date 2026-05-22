@extends('layouts.app')

@section('title', 'Santé Visuelle — Optique Échouate')

@section('content')
    <div class="pt-24 sm:pt-28 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-3xl sm:text-4xl font-bold text-white">{{ __('messages.eye_health.title') }}</h1>
                <p class="mt-3 text-heroi-text-muted max-w-xl mx-auto">{{ __('messages.eye_health.subtitle') }}</p>
            </div>

            <div class="space-y-8">
                <x-card padding="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="w-14 h-14 rounded-xl hero-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">{{ __('messages.eye_health.myopia_title') }}</h2>
                            <p class="text-heroi-text-muted text-sm leading-relaxed mb-3">{{ __('messages.eye_health.myopia_p1') }}</p>
                            <p class="text-heroi-text-muted text-sm leading-relaxed">{{ __('messages.eye_health.myopia_p2') }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card padding="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="w-14 h-14 rounded-xl hero-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">{{ __('messages.eye_health.strain_title') }}</h2>
                            <p class="text-heroi-text-muted text-sm leading-relaxed mb-3">{{ __('messages.eye_health.strain_p1') }}</p>
                            <p class="text-heroi-text-muted text-sm leading-relaxed">{{ __('messages.eye_health.strain_p2') }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card padding="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="w-14 h-14 rounded-xl hero-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">{{ __('messages.eye_health.blue_title') }}</h2>
                            <p class="text-heroi-text-muted text-sm leading-relaxed mb-3">{{ __('messages.eye_health.blue_p1') }}</p>
                            <p class="text-heroi-text-muted text-sm leading-relaxed">{{ __('messages.eye_health.blue_p2') }}</p>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="mt-12 text-center">
                <div class="glass-strong rounded-2xl p-8 sm:p-10">
                    <h2 class="text-2xl font-bold text-white mb-4">{{ __('messages.eye_health.cta_title') }}</h2>
                    <p class="text-heroi-text-muted mb-6 max-w-lg mx-auto">{{ __('messages.eye_health.cta_subtitle') }}</p>
                    <x-button href="{{ route('appointments') }}" variant="primary" size="lg" class="shadow-2xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('messages.eye_health.cta_button') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
@endsection
