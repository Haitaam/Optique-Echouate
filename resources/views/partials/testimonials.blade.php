<section class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">{{ __('messages.testimonials.title') }}</h2>
            <p class="mt-3 text-heroi-text-muted max-w-lg mx-auto">{{ __('messages.testimonials.subtitle') }}</p>
        </div>

        <div x-data="{ current: 0 }" class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${current * 100}%)`">
                @php
                    $testimonials = [
                        ['name' => 'Sophie Laurent', 'role' => 'Designer', 'avatar' => 'SL', 'text' => 'Le quiz de style était incroyablement précis. Il m\'a recommandé des montures que je n\'aurais jamais choisies moi-même, et elles sont devenues mes lunettes préférées !'],
                        ['name' => 'Marcus Chen', 'role' => 'Ingénieur Logiciel', 'avatar' => 'MC', 'text' => 'Une qualité premium qui se ressent vraiment. Les montures en titane sont incroyablement légères, et le revêtement anti-lumière bleue a fait une énorme différence pour mon travail sur écran.'],
                        ['name' => 'Aisha Patel', 'role' => 'Médecin', 'avatar' => 'AP', 'text' => 'En tant que personne qui porte des lunettes quotidiennement, le confort est primordial. Optique Échouate a allié style et confort toute la journée. L\'examen de la vue était complet et professionnel.'],
                        ['name' => 'James Wilson', 'role' => 'Directeur Marketing', 'avatar' => 'JW', 'text' => 'Je n\'ai jamais reçu autant de compliments sur mes lunettes. Le processus de sélection était fluide et l\'essayage à domicile très pratique.'],
                    ];
                @endphp

                @foreach ($testimonials as $t)
                    <div class="min-w-full px-2 sm:px-4">
                        <x-card padding="p-6 sm:p-8" class="text-center mx-auto max-w-lg">
                            <div class="w-16 h-16 rounded-full hero-gradient flex items-center justify-center mx-auto mb-4 text-lg font-bold text-white">
                                {{ $t['avatar'] }}
                            </div>
                            <p class="text-heroi-text leading-relaxed mb-6 text-sm sm:text-base">&ldquo;{{ $t['text'] }}&rdquo;</p>
                            <div>
                                <p class="font-semibold text-white">{{ $t['name'] }}</p>
                                <p class="text-xs text-heroi-text-muted">{{ $t['role'] }}</p>
                            </div>
                        </x-card>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center gap-2 mt-6">
                @foreach ($testimonials as $i => $t)
                    <button @click="current = {{ $i }}" class="w-2 h-2 rounded-full transition-all duration-300" :class="current === {{ $i }} ? 'bg-orange-500 w-6' : 'bg-white/20'"></button>
                @endforeach
            </div>

            <button @click="current = Math.max(0, current - 1)" class="absolute top-1/2 -translate-y-1/2 ltr:left-0 rtl:right-0 w-10 h-10 rounded-full glass-strong flex items-center justify-center text-white hover:bg-white/10 transition-all">
                <svg class="w-5 h-5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="current = Math.min({{ count($testimonials) - 1 }}, current + 1)" class="absolute top-1/2 -translate-y-1/2 ltr:right-0 rtl:left-0 w-10 h-10 rounded-full glass-strong flex items-center justify-center text-white hover:bg-white/10 transition-all">
                <svg class="w-5 h-5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</section>
