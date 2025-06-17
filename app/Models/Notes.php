<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $fillable = [
        'name',
        'desc',
        'file',
        'admin_id',
        'teacher_id',
        'notetypes_id',
        'updated_at',
        'created_at'
    ];

    public function Teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }



    public function Admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function Notetypes()
    {
        return $this->belongsTo(Notetypes::class, 'notetypes_id');
    }


    protected $casts = [
        'student_id' => 'array',
    ];
}
