<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_id',
        'classroom_id',
        'student_ic',
        'mark',
    ];
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function student()
    {
        // Assuming student is a user with role 'student'
        // user_ic in exam_marks relates to ic in users table
        return $this->belongsTo(User::class, 'student_ic', 'ic');
    }
}
