<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Assessment::query();

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('evaluator_id')) {
            $query->where('evaluator_id', $request->evaluator_id);
        }

        // Filter by evaluator type if needed (e.g. only mentor assessments)
        if ($request->has('evaluator_type')) {
            $query->where('evaluator_type', $request->evaluator_type);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'evaluator_id' => 'required',
            'evaluator_type' => 'required|in:mentor,supervisor',
            'scores' => 'nullable|array',
            'comments' => 'nullable|string',
            'evaluation_date' => 'nullable|date',
        ]);

        $assessment = Assessment::create($request->all());

        return response()->json($assessment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Assessment::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->update($request->all());
        return response()->json($assessment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Assessment::destroy($id);
        return response()->json(null, 204);
    }
}
