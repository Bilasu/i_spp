<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssayAnswer extends Model
{
    protected $fillable = ['essay_questions_id', 'user_ic', 'answer', 'mark', 'comment',  'quiz_category_id',];

    public function question()
    {
        return $this->belongsTo(EssayQuestion::class, 'essay_questions_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_ic', 'ic');
    }
}
