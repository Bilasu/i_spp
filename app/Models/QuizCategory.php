<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizCategory extends Model
{

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_category_id');
    }

    // Relasi dengan soalan
    public function subjectiveQuestions()
    {
        return $this->hasMany(SubjectiveQuestion::class, 'quiz_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'ic');
    }

    public function essayQuestions()
    {
        return $this->hasMany(EssayQuestion::class, 'quiz_category_id');
    }
}
