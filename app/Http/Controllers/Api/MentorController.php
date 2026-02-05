<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class MentorController extends Controller
{
    /**
     * Get students assigned to the authenticated mentor.
     */
    public function myStudents(Request $request)
    {
        $mentor = $request->user();

        // Ensure the user is actually a mentor
        if (!$mentor || !isset($mentor->mentor_id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $students = Student::where('mentor_id', $mentor->mentor_id)
            ->with('internship')
            ->get()
            ->map(function ($student) {
                // Use name_th if available, otherwise fallback to name
                $student->name = $student->name_th ?? $student->name;
                // Add company_name from internship if available
                $student->company_name = $student->internship ? $student->internship->company_name : null;
                return $student;
            });

        return response()->json($students);
    }
}
