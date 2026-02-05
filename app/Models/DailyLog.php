<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'work_details',
        'image_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
