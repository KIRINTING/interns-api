<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InternshipCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CriteriaController extends Controller
{
    /**
     * Get criteria for a student
     */
    public function getCriteria($studentCode)
    {
        $criteria = InternshipCriteria::where('student_code', $studentCode)->first();

        if (!$criteria) {
            return response()->json([
                'success' => false,
                'message' => 'ยังไม่มีข้อมูลเกณฑ์การฝึกงาน'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Create or update criteria
     */
    public function upsertCriteria(Request $request, $studentCode)
    {
        $validator = Validator::make($request->all(), [
            'gpa' => 'required|numeric|min:0|max:4',
            'credits_completed' => 'required|integer|min:0',
            'required_courses_completed' => 'required|boolean',
            'has_advisor_approval' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $criteria = InternshipCriteria::updateOrCreate(
            ['student_code' => $studentCode],
            $request->all()
        );

        // Calculate eligibility
        $criteria->calculateEligibility();
        $criteria->save();

        return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลเกณฑ์สำเร็จ',
            'data' => $criteria
        ]);
    }

    /**
     * Check eligibility
     */
    /**
     * Check eligibility
     */
    public function checkEligibility($studentCode)
    {
        // Get or create criteria record
        $criteria = InternshipCriteria::firstOrCreate(
            ['student_code' => $studentCode],
            [
                'gpa' => 0.00,
                'credits_completed' => 0,
                'required_courses_completed' => false,
                'has_advisor_approval' => false
            ]
        );

        // Sync data from Student model if available
        $student = \App\Models\Student::where('student_code', $studentCode)->first();
        if ($student) {
            $criteria->gpa = $student->gpa ?? $criteria->gpa;
            $criteria->credits_completed = $student->cumulative_credits ?? $criteria->credits_completed;
        }

        $criteria->calculateEligibility();
        $criteria->save();

        $details = [
            'gpa_pass' => $criteria->gpa >= 2.00,
            'credits_pass' => $criteria->credits_completed >= 90,
            'courses_pass' => $criteria->required_courses_completed,
            'advisor_pass' => $criteria->has_advisor_approval
        ];

        return response()->json([
            'success' => true,
            'is_eligible' => $criteria->is_eligible,
            'details' => $details,
            'data' => $criteria
        ]);
    }
}
