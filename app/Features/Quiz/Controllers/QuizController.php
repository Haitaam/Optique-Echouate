<?php

namespace App\Features\Quiz\Controllers;

use App\Features\Quiz\Models\QuizAnswer;
use App\Features\Quiz\Services\QuizRecommendationService;
use Illuminate\Http\Request;

class QuizController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('pages.quiz');
    }

    public function analyze(Request $request, QuizRecommendationService $quizService)
    {
        $data = $request->validate([
            'glasses_type' => 'required|string',
            'style' => 'required|string',
            'shape' => 'required|string',
            'color' => 'required|string',
            'material' => 'required|string',
            'lifestyle' => 'required|string',
        ]);

        $sessionId = \Illuminate\Support\Str::uuid();

        QuizAnswer::create(array_merge($data, ['session_id' => $sessionId]));

        $results = $quizService->recommend($data);

        return response()->json([
            'results' => $results,
            'html' => view('pages.quiz.partials.results', compact('results'))->render(),
        ]);
    }
}
