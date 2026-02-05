<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Mentor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'mentor_id',
        'username',
        'password',
        'name',
        'surname',
        'department',
    ];

    protected $hidden = [
        'password',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'mentor_id', 'mentor_id');
    }
}
