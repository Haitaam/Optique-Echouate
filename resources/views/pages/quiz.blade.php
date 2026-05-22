@extends('layouts.app')

@section('title', 'Style Quiz — Optique Échouate')

@section('content')
    <div class="pt-24 sm:pt-28 pb-20" x-data="quizWizard">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10" x-show="step < 7">
                <h1 class="text-3xl sm:text-4xl font-bold text-white">{{ __('messages.quiz.title') }}</h1>
                <p class="mt-2 text-heroi-text-muted">{{ __('messages.quiz.subtitle') }}</p>
            </div>

            <div x-show="step < 7" class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-heroi-text-muted" x-text="`{{ __('messages.quiz.step') }} ${step} {{ __('messages.quiz.of') }} ${totalSteps}`"></span>
                    <span class="text-xs text-heroi-text-muted" x-text="`${Math.round((step / totalSteps) * 100)}%`"></span>
                </div>
                <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full hero-gradient rounded-full transition-all duration-500 ease-out" :style="`width: ${(step / totalSteps) * 100}%`"></div>
                </div>
            </div>

            @php
                $questions = [
                    1 => ['key' => 'glasses_type', 'question' => 'quiz.question_1', 'grid' => 'grid-cols-1 sm:grid-cols-3', 'padding' => 'p-6 sm:p-8'],
                    2 => ['key' => 'style', 'question' => 'quiz.question_2', 'grid' => 'grid-cols-1 sm:grid-cols-3', 'padding' => 'p-6 sm:p-8'],
                    3 => ['key' => 'shape', 'question' => 'quiz.question_3', 'grid' => 'grid-cols-2 sm:grid-cols-3', 'padding' => 'p-5 sm:p-6'],
                    4 => ['key' => 'color', 'question' => 'quiz.question_4', 'grid' => 'grid-cols-2 sm:grid-cols-3', 'padding' => 'p-5 sm:p-6'],
                    5 => ['key' => 'material', 'question' => 'quiz.question_5', 'grid' => 'grid-cols-2 sm:grid-cols-3', 'padding' => 'p-5 sm:p-6'],
                    6 => ['key' => 'lifestyle', 'question' => 'quiz.question_6', 'grid' => 'grid-cols-1 sm:grid-cols-2', 'padding' => 'p-6 sm:p-8'],
                ];
            @endphp

            @foreach ($questions as $qStep => $q)
                <div x-show="step === {{ $qStep }}" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" class="space-y-4">
                    <h2 class="text-xl font-semibold text-white mb-6">{{ __("messages.{$q['question']}") }}</h2>
                    <div class="{{ $q['grid'] }} gap-4">
                        <template x-for="opt in options.{{ $q['key'] }}" :key="opt.value">
                            <button @click="selectAnswer('{{ $q['key'] }}', opt.value)"
                                class="glass rounded-2xl {{ $q['padding'] }} text-center transition-all duration-300 border"
                                :class="{
                                    'border-orange-500/50 bg-orange-500/10 ring-1 ring-orange-500/30 scale-105 shadow-lg shadow-orange-500/10': answers.{{ $q['key'] }} === opt.value,
                                    'border-white/5 hover:border-white/10 hover:scale-[1.02]': answers.{{ $q['key'] }} !== opt.value
                                }"
                                x-bind:style="answers.{{ $q['key'] }} && answers.{{ $q['key'] }} !== opt.value ? 'opacity: 0.4' : 'opacity: 1'">
                                <span class="text-3xl sm:text-4xl block mb-2 sm:mb-3" x-text="opt.icon"></span>
                                <span class="text-sm font-medium text-white" x-text="opt.label"></span>
                                <template x-if="'{{ $q['key'] }}' === 'shape'">
                                    <div class="mt-2 flex justify-center">
                                        <svg viewBox="0 0 60 48" class="w-10 h-8" x-bind:style="getShapeSvgStyle(opt.value)">
                                            <path x-bind:d="shapePaths[opt.value]" fill="currentColor" opacity="0.6"/>
                                        </svg>
                                    </div>
                                </template>
                            </button>
                        </template>
                    </div>
                    @if ($qStep === 6)
                        <div class="mt-8 text-center">
                            <x-button @click="submitQuiz()" variant="primary" size="lg" class="shadow-2xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                {{ __('messages.quiz.analyze') }}
                            </x-button>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Loading Screen --}}
            <div x-show="loading" x-cloak x-transition:enter="transition ease-out duration-300" class="text-center py-20">
                <div class="relative w-24 h-24 mx-auto mb-8">
                    <div class="absolute inset-0 hero-gradient rounded-full opacity-20 animate-ping"></div>
                    <div class="relative w-24 h-24 hero-gradient rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-white animate-spin-slow" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">{{ __('messages.quiz.loading_title') }}</h3>
                <p class="text-heroi-text-muted">{{ __('messages.quiz.loading_subtitle') }}</p>
            </div>

            {{-- Results --}}
            <div x-show="step === 7 && !loading" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8">
                <div x-html="resultsHtml"></div>
            </div>

            {{-- Navigation --}}
            <div x-show="step > 1 && step < 6" class="flex justify-between mt-8">
                <x-button @click="prevStep()" variant="ghost" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('messages.quiz.back') }}
                </x-button>
            </div>
        </div>
    </div>
@endsection
