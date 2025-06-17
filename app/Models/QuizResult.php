<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = [
        'user_ic',
        'quiz_category_id',
        'correct',
        'wrong',
        'total',
        'taken_at'
    ];
    protected $casts = [
        'taken_at' => 'datetime',
    ];
    public function category()
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_ic', 'ic');
    }

    public function quizCategory()
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }
}
