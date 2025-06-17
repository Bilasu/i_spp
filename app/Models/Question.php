<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'a', 'b', 'c', 'd', 'ans', 'quiz_category_id'];

    public function category()
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }
}
