<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipCriteria extends Model
{
    use HasFactory;

    protected $table = 'internship_criteria';

    protected $fillable = [
        'student_code',
        'gpa',
        'credits_completed',
        'required_courses_completed',
        'has_advisor_approval',
        'is_eligible',
        'notes'
    ];

    protected $casts = [
        'gpa' => 'decimal:2',
        'credits_completed' => 'integer',
        'required_courses_completed' => 'boolean',
        'has_advisor_approval' => 'boolean',
        'is_eligible' => 'boolean',
    ];

    /**
     * Calculate eligibility based on criteria
     */
    public function calculateEligibility(): bool
    {
        // Criteria: GPA >= 2.00, Credits >= 90, Required courses completed, Advisor approval
        $this->is_eligible =
            ($this->gpa >= 2.00) &&
            ($this->credits_completed >= 90) &&
            ($this->required_courses_completed) &&
            ($this->has_advisor_approval);

        return $this->is_eligible;
    }
}
