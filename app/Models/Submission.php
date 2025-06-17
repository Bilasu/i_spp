<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    //
    protected $fillable = [
        'assignment_id',
        'student_ic',
        'file_path',
        'comment',
        'submitted_at',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_ic', 'ic');
    }
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
