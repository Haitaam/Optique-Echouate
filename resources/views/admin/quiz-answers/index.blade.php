@extends('layouts.app')

@section('title', 'Admin — Quiz')

@php $hideNav = true; @endphp

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Réponses Quiz</h1>
                <p class="text-sm text-heroi-text-muted mt-1">
                    <span class="text-white font-medium">{{ $answers instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $answers->total() : $answers->count() }}</span> réponses
                </p>
            </div>
        </div>

        <div class="admin-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Session</th>
                            <th class="hidden sm:table-cell">Type</th>
                            <th class="hidden sm:table-cell">Style</th>
                            <th class="hidden md:table-cell">Forme</th>
                            <th class="hidden md:table-cell">Couleur</th>
                            <th class="hidden lg:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($answers as $answer)
                            <tr>
                                <td>
                                    <span class="text-heroi-text-muted text-sm">{{ $answer->id }}</span>
                                </td>
                                <td>
                                    <span class="text-white text-xs font-mono">{{ Str::limit($answer->session_id, 12) }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm capitalize">{{ $answer->glasses_type ?? '—' }}</span>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-heroi-text-muted text-sm capitalize">{{ $answer->style ?? '—' }}</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="text-heroi-text-muted text-sm capitalize">{{ $answer->shape ?? '—' }}</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="text-heroi-text-muted text-sm capitalize">{{ $answer->color ?? '—' }}</span>
                                </td>
                                <td class="hidden lg:table-cell">
                                    <span class="text-heroi-text-muted text-sm">{{ $answer->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16 text-heroi-text-muted">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-sm">Aucune réponse quiz pour le moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $answers instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $answers->appends(request()->query())->links() : '' }}
        </div>
    </div>
@endsection
