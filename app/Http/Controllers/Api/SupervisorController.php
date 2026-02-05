<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class SupervisorController extends Controller
{
    /**
     * Get students assigned to the authenticated supervisor.
     */
    public function myStudents(Request $request)
    {
        $supervisor = $request->user();

        // Ensure the user is actually a supervisor
        if (!$supervisor || !isset($supervisor->supervisor_id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $students = Student::where('supervisor_id', $supervisor->supervisor_id)->get();
        return response()->json($students);
    }
}
