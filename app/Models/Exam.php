<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{

    protected $fillable = ['name', 'start_date', 'end_date'];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'exam_classroom', 'exam_id', 'classroom_id');
    }
    public function examMarks()
    {
        return $this->hasMany(ExamMark::class, 'exam_id');
    }
}
