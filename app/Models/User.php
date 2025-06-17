<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     *
     *
     */
    // Set IC as primary key
    protected $primaryKey = 'ic';      // <-- Add this line
    public $incrementing = false;      // <-- IC is not auto-incrementing
    protected $keyType = 'string';  // <-- Set the key type to string
    protected $fillable = [
        'name',
        'ic',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_user', 'user_ic', 'classroom_id', 'ic', 'id')
            ->withPivot('role')
            ->withTimestamps();
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_ic', 'ic');
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class, 'student_ic', 'ic');
    }

    // Hash the password before saving to the database
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getEmailForPasswordReset()
    {
        return $this->ic; // <-- Return IC sebagai "email" ganti
    }
}
