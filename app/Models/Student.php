<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'student_id',
        'student_code',
        'national_id',
        'name',
        'surname',
        'name_th',
        'name_en',
        'email',
        'phone',
        'address',
        'gpa',
        'faculty',
        'group',
        'status',
        'major',
        'mentor_id',
        'supervisor_id',
        'cumulative_credits',
        'password_expires_at',
    ];

    protected $casts = [
        'password_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'national_id', // Assuming sensitive
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id', 'mentor_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'supervisor_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'student_id', 'student_id');
    }

    public function dailyLogs()
    {
        return $this->hasMany(DailyLog::class, 'student_id', 'student_id');
    }

    public function internship()
    {
        return $this->hasOne(Intern::class, 'student_code', 'student_code');
    }
}
