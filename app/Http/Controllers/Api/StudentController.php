<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Student::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students',
            'student_code' => 'required|unique:students',
            'national_id' => 'required',
            'name' => 'required',
            'surname' => 'required',
            'group' => 'required',
            'status' => 'required',
            'major' => 'required',
        ]);

        return Student::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Student::findOrFail($id);
    }

    /**
     * Get student by student code
     */
    public function getByStudentCode(Request $request)
    {
        $studentCode = $request->query('student_code');

        if (!$studentCode) {
            return response()->json([
                'success' => false,
                'message' => 'Student code is required'
            ], 400);
        }

        $student = Student::where('student_code', $studentCode)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'sometimes|required|unique:students,student_id,' . $student->id,
            'student_code' => 'sometimes|required|unique:students,student_code,' . $student->id,
            'national_id' => 'sometimes|required',
            'name' => 'sometimes|required',
            'surname' => 'sometimes|required',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'faculty' => 'nullable|string|max:255',
            'group' => 'sometimes|required',
            'status' => 'sometimes|required',
            'major' => 'sometimes|required',
            'cumulative_credits' => 'nullable|integer|min:0',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student profile updated successfully',
            'data' => $student
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Student::destroy($id);
    }
}
