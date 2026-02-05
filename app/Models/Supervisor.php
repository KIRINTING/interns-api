<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Supervisor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'supervisor_id',
        'username',
        'password',
        'name',
        'surname',
        'company_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'supervisor_id', 'supervisor_id');
    }
}
