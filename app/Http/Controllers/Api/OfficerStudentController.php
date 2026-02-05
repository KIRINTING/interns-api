<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class OfficerStudentController extends Controller
{
    /**
     * Get list of all students
     */
    public function index(Request $request)
    {
        $query = Student::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(10);

        return response()->json($students);
    }

    /**
     * Update student password expiration
     */
    public function updatePasswordExpiry(Request $request, $id)
    {
        $request->validate([
            'password_expires_at' => 'nullable|date',
            // Can add option specifically for "duration in days" if UI sends that
        ]);

        $student = Student::findOrFail($id);
        $student->password_expires_at = $request->password_expires_at;
        $student->save();

        return response()->json([
            'message' => 'Password expiration updated successfully',
            'student' => $student
        ]);
    }
}
