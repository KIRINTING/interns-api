<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Evaluation::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'evaluation_id' => 'required|unique:evaluations',
            'student_id' => 'required',
            'company_name' => 'required',
            'detail' => 'required',
            'hours' => 'required',
        ]);

        return Evaluation::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Evaluation::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $evaluation = Evaluation::findOrFail($id);

        $validated = $request->validate([
            'evaluation_id' => 'required|unique:evaluations,evaluation_id,' . $evaluation->id,
            'student_id' => 'required',
            'company_name' => 'required',
            'detail' => 'required',
            'hours' => 'required',
        ]);

        $evaluation->update($validated);

        return $evaluation;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Evaluation::destroy($id);
    }
}
