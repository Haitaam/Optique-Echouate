<section class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">{{ __('messages.services.title') }}</h2>
            <p class="mt-3 text-heroi-text-muted max-w-lg mx-auto">{{ __('messages.services.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @php
                $serviceList = [
                    ['icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'title' => 'services.eye_exam', 'desc' => 'services.eye_exam_desc'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'services.prescription', 'desc' => 'services.prescription_desc'],
                    ['icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'title' => 'services.sunglasses', 'desc' => 'services.sunglasses_desc'],
                    ['icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'title' => 'services.blue_light', 'desc' => 'services.blue_light_desc'],
                ];
            @endphp

            @foreach ($serviceList as $service)
                <x-card padding="p-6 sm:p-8">
                    <div class="w-12 h-12 rounded-xl hero-gradient flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $service['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ __("messages.{$service['title']}") }}</h3>
                    <p class="text-sm text-heroi-text-muted">{{ __("messages.{$service['desc']}") }}</p>
                </x-card>
            @endforeach
        </div>
    </div>
</section>
