<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    use HasFactory;

    protected $fillable = [
        'info_id',
        'title',
        'category',
        'detail',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];
}
