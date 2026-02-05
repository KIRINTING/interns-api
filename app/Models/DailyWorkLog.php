<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DailyWorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_code',
        'intern_id',
        'log_date',
        'work_description',
        'hours_worked',
        'is_weekend',
        'photo_path'
    ];

    protected $casts = [
        'log_date' => 'date',
        'hours_worked' => 'decimal:2',
        'is_weekend' => 'boolean',
    ];

    /**
     * Get the intern that owns the log
     */
    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }

    /**
     * Get the photo URL
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return url(Storage::url($this->photo_path));
        }
        return null;
    }

    /**
     * Delete photo when log is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($log) {
            if ($log->photo_path) {
                Storage::delete($log->photo_path);
            }
        });
    }
}
