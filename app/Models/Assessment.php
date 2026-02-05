<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'evaluator_id',
        'evaluator_type',
        'scores',
        'comments',
        'evaluation_date',
    ];

    protected $casts = [
        'scores' => 'array',
        'evaluation_date' => 'date',
    ];

    public function evaluator()
    {
        // Polymorphic relationship if needed, or manual lookup
        // Laravel conventions for evaluator_type would be App\Models\Mentor
    }
}
