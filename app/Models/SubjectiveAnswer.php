<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectiveAnswer extends Model
{
    protected $fillable = ['subjective_question_id', 'user_ic', 'answer', 'mark', 'comment',  'quiz_category_id',];

    public function question()
    {
        return $this->belongsTo(SubjectiveQuestion::class, 'subjective_question_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_ic', 'ic');
    }
}
