<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssayQuestion extends Model
{
    protected $fillable = ['question', 'quiz_category_id', 'created_by', 'mark_total'];
    public function answers()
    {
        return $this->hasMany(EssayAnswer::class, 'essay_questions_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }

    public function myAnswer()
    {
        return $this->hasOne(EssayAnswer::class, 'essay_questions_id')  // Correct foreign key name
            ->where('user_ic', auth()->guard('student')->user()->ic ?? null);
    }

    public function essayAnswers()
    {
        return $this->hasMany(EssayAnswer::class, 'essay_questions_id');
    }
}
