<div
    x-data="facePreview()"
    x-show="open"
    x-cloak
    @keydown.escape.window="close()"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
>
    <div x-show="open" x-transition.opacity.duration.300ms class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="close()"></div>

    <div
        x-show="open"
        x-transition.duration.400ms
        x-on:click.away="close()"
        class="relative z-10 w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-3xl border border-white/20 shadow-2xl"
        style="background: linear-gradient(135deg, rgba(17,17,17,0.97), rgba(30,30,30,0.95)); backdrop-filter: blur(32px);"
    >
        <button @click="close()" class="absolute top-5 right-5 z-20 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Header --}}
        <div class="relative p-6 sm:p-8 pb-4 border-b border-white/5">
            <div class="flex items-start gap-5">
                <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0 ring-2 ring-orange-500/30 shadow-lg">
                    <img x-bind:src="product.image" x-bind:alt="product.name" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white truncate" x-text="product.name"></h2>
                    <p class="text-orange-400 font-medium text-sm mt-1" x-text="product.brand"></p>
                    <p class="text-gray-400 text-sm mt-1" x-show="bestLabel">
                        ⋆ Idéal pour visage <span class="text-orange-300 font-semibold" x-text="bestLabel"></span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Face shape tabs --}}
        <div class="px-6 sm:px-8 pt-5">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide" x-ref="tabBar">
                <template x-for="(data, shape) in faceData" :key="shape">
                    <button
                        x-bind:data-shape="shape"
                        @click="selectedShape = shape; scrollToShape(shape)"
                        class="flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 border"
                        :class="selectedShape === shape
                            ? 'bg-orange-500/20 text-orange-300 border-orange-500/40 shadow-lg shadow-orange-500/10'
                            : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-gray-200'"
                        x-text="`${shapeLabels[shape] || shape} (${data.percentage}%)`"
                    ></button>
                </template>
            </div>
        </div>

        {{-- Main preview area --}}
        <div class="p-6 sm:p-8 pt-5">
            <template x-for="(data, shape) in faceData" :key="shape">
                <div
                    x-show="selectedShape === shape"
                    x-transition:enter.duration.400ms
                    class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-10"
                >
                    {{-- Left: SVG face --}}
                    <div class="lg:col-span-2 flex flex-col items-center justify-center">
                        <div class="w-full max-w-[240px] mx-auto">
                            <svg viewBox="0 0 400 320" class="w-full h-auto drop-shadow-lg">
                                <defs>
                                    <filter x-bind:id="`glow-${shape}`">
                                        <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                                        <feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge>
                                    </filter>
                                    <clipPath x-bind:id="`face-clip-${shape}`">
                                        <path x-bind:d="facePaths[shape]"/>
                                    </clipPath>
                                </defs>
                                <path x-bind:d="facePaths[shape]" x-bind:fill="faceColors[shape].fill" x-bind:stroke="faceColors[shape].stroke" stroke-width="2.5" opacity="0.9"/>
                                <g x-bind:clip-path="`url(#face-clip-${shape})`" opacity="0.35">
                                    <image x-bind:href="product.image" x="60" y="40" width="280" height="200" preserveAspectRatio="xMidYMid slice"/>
                                </g>
                                <line x1="120" y1="100" x2="280" y2="100" stroke="#d1d5db" stroke-width="0.5" stroke-dasharray="4,4" opacity="0.5"/>
                                <line x1="100" y1="160" x2="300" y2="160" stroke="#d1d5db" stroke-width="0.5" stroke-dasharray="4,4" opacity="0.5"/>
                                <ellipse cx="165" cy="85" rx="12" ry="10" fill="#1f2937" opacity="0.8"/>
                                <ellipse cx="235" cy="85" rx="12" ry="10" fill="#1f2937" opacity="0.8"/>
                                <circle cx="165" cy="85" r="3" fill="white" opacity="0.7"/>
                                <circle cx="235" cy="85" r="3" fill="white" opacity="0.7"/>
                                <path d="M 180 140 Q 200 155 220 140" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                                <g opacity="0.85">
                                    <path d="M 130 75 L 140 60 Q 150 50 170 55 L 180 65" fill="none" stroke="#1f2937" stroke-width="4" stroke-linecap="round" opacity="0.3"/>
                                    <path d="M 270 75 L 260 60 Q 250 50 230 55 L 220 65" fill="none" stroke="#1f2937" stroke-width="4" stroke-linecap="round" opacity="0.3"/>
                                    <rect x="130" y="65" width="140" height="45" rx="8" fill="none" stroke="#f59e0b" stroke-width="3" opacity="0.5"/>
                                </g>
                                <text x="200" y="305" text-anchor="middle" font-family="system-ui" font-size="16" font-weight="600" fill="#374151" x-text="shapeLabels[shape] || shape"></text>
                            </svg>
                        </div>
                    </div>

                    {{-- Right: details --}}
                    <div class="lg:col-span-3 space-y-5">
                        <div>
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span x-text="`Visage ${shapeLabels[shape] || shape}`"></span>
                                <span class="text-orange-400 text-sm font-normal">— Compatibilité</span>
                            </h3>
                        </div>

                        <div class="w-full">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-sm font-medium text-gray-300">Taux de compatibilité</span>
                                <span class="text-sm font-bold"
                                    :class="data.percentage >= 80 ? 'text-green-400' : data.percentage >= 50 ? 'text-amber-400' : 'text-red-400'"
                                    x-text="`${data.percentage}%`"></span>
                            </div>
                            <div class="w-full h-2.5 bg-white/10 rounded-full overflow-hidden backdrop-blur-sm">
                                <div
                                    class="h-full rounded-full transition-all duration-1000 ease-out"
                                    x-bind:style="`width: ${data.percentage}%; background: linear-gradient(90deg, ${data.percentage >= 80 ? '#22c55e' : data.percentage >= 50 ? '#f59e0b' : '#ef4444'}, ${data.percentage >= 80 ? '#16a34a' : data.percentage >= 50 ? '#d97706' : '#dc2626'})`"
                                ></div>
                            </div>
                        </div>

                        <div class="bg-white/5 rounded-2xl p-5 border border-white/10">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full bg-orange-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-orange-300 mb-1">👓 Conseil du styliste</p>
                                    <p class="text-gray-300 text-sm leading-relaxed" x-text="data.explanation"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white/5 rounded-xl p-3 text-center border border-white/5">
                                <p class="text-xs text-gray-500">Forme de monture</p>
                                <p class="text-sm font-semibold text-white mt-0.5" x-text="product.frame_shape"></p>
                            </div>
                            <div class="bg-white/5 rounded-xl p-3 text-center border border-white/5">
                                <p class="text-xs text-gray-500">Matière</p>
                                <p class="text-sm font-semibold text-white mt-0.5" x-text="product.material"></p>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <a x-bind:href="`/products?shape=${selectedShape}`"
                               class="flex-1 text-center px-4 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-orange-500 text-white font-medium text-sm hover:from-orange-500 hover:to-orange-400 transition-all duration-200 shadow-lg shadow-orange-500/20">
                                Voir plus pour ce visage
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Mobile swipe indicator --}}
            <div class="flex justify-center gap-1.5 mt-6 lg:hidden">
                <template x-for="(data, shape) in faceData" :key="'dot-'+shape">
                    <button
                        @click="selectedShape = shape"
                        class="w-2 h-2 rounded-full transition-all duration-300"
                        :class="selectedShape === shape ? 'bg-orange-400 w-6' : 'bg-white/20 hover:bg-white/40'"
                    ></button>
                </template>
            </div>
        </div>
    </div>
</div>
