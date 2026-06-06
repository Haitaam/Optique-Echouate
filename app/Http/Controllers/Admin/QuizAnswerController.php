<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class QuizAnswerController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        if (class_exists(\App\Features\Quiz\Models\QuizAnswer::class)) {
            $answers = \App\Features\Quiz\Models\QuizAnswer::latest()->paginate(20);
        } else {
            $answers = collect();
        }

        return view('admin.quiz-answers.index', compact('answers'));
    }
}
