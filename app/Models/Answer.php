<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class Answer extends Model
{
    public function student()
    {
        return $this->belongsTo(SubjectiveAnswer::class, 'user_ic', 'ic');
    }

    public function question()
    {
        return $this->belongsTo(SubjectiveQuestion::class, 'subjective_question_id');
    }
}
