<?php

namespace App\Features\Quiz\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'session_id', 'glasses_type', 'style', 'shape',
        'color', 'material', 'lifestyle',
    ];
}
