<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    protected $fillable = ['class_name', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'classroom_user', 'classroom_id', 'user_ic')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->users()->wherePivot('role', 'teacher');
    }

    public function students()
    {
        return $this->users()->wherePivot('role', 'student');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_classroom');
    }
}
