<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $casts = [

        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'due_date',
        'classroom_id',
        // 'file_path', // Uncomment if you want to store file paths
    ];
    //
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
    //     public function files()
    // {
    //     return $this->hasMany(AssignmentFile::class);
    // }
}
