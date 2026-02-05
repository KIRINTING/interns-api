<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Intern extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        // Student Information
        'student_code',
        'title',
        'first_name',
        'last_name',
        'phone',
        'class_group',
        'registration_status',
        // Company Information
        'company_name',
        'position',
        'job_description',
        'company_address',
        'company_phone',
        // Coordinator Information
        'coordinator_name',
        'coordinator_position',
        'coordinator_phone',
        // Approver Information
        'approver_name',
        'approver_position',
        // Location & Photo
        'google_map_coordinates',
        'photo_path',
        // Additional
        'notes',
        // Approval workflow
        'status',
        'pdf_path',
        'officer_approved_at',
        'officer_approved_by',
        'dean_approved_at',
        'dean_signature_path',
        'rejection_reason',
        // Training evidence
        'total_training_hours',
        'absence_days',
        'leave_days',
        'calculated_hours',
        'training_status',
        'start_date',
        'end_date',
        'evidence_submitted_at',
    ];

    protected $casts = [
        'officer_approved_at' => 'datetime',
        'dean_approved_at' => 'datetime',
        'evidence_submitted_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_training_hours' => 'decimal:2',
        'calculated_hours' => 'decimal:2',
        'absence_days' => 'integer',
        'leave_days' => 'integer',
    ];

    protected $appends = ['photo_url'];

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
    public function logs()
    {
        return $this->hasMany(DailyWorkLog::class, 'student_code', 'student_code');
    }
}
