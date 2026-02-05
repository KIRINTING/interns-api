<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class ReportController extends Controller
{
    /**
     * Get aggregated report data for officers.
     */
    public function index(Request $request)
    {
        // Eager load relationships
        $students = Student::with(['internship', 'assessments', 'dailyLogs'])->get();

        $reportData = $students->map(function ($student) {

            // Calculate Mentor Score
            $mentorAssessment = $student->assessments->where('evaluator_type', 'mentor')->first();
            $mentorScore = 0;
            if ($mentorAssessment && isset($mentorAssessment->scores)) {
                // Assuming scores is an array of values, sum them up
                $scores = $mentorAssessment->scores;
                $mentorScore = array_sum($scores);
            }

            // Calculate Supervisor Score
            $supervisorAssessment = $student->assessments->where('evaluator_type', 'supervisor')->first();
            $supervisorScore = 0;
            if ($supervisorAssessment && isset($supervisorAssessment->scores)) {
                $scores = $supervisorAssessment->scores;
                $supervisorScore = array_sum($scores);
            }

            return [
                'student_id' => $student->student_id,
                'name' => $student->name . ' ' . $student->surname,
                'major' => $student->major,
                'company' => $student->internship ? $student->internship->company_name : 'ยังไม่มีสถานประกอบการ',
                'daily_logs_count' => $student->dailyLogs->count(),
                'mentor_score' => $mentorScore,
                'mentor_evaluated' => $mentorAssessment ? true : false,
                'supervisor_score' => $supervisorScore,
                'supervisor_evaluated' => $supervisorAssessment ? true : false,
            ];
        });

        return response()->json($reportData);
    }
}
