<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Officer::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'officer_id' => 'required|unique:officers',
            'username' => 'required|unique:officers',
            'password' => 'required',
            'name' => 'required',
            'surname' => 'required',
        ]);

        return Officer::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Officer::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $officer = Officer::findOrFail($id);

        $validated = $request->validate([
            'officer_id' => 'required|unique:officers,officer_id,' . $officer->id,
            'username' => 'required|unique:officers,username,' . $officer->id,
            'password' => 'sometimes|required',
            'name' => 'required',
            'surname' => 'required',
        ]);

        $officer->update($validated);

        return $officer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Officer::destroy($id);
    }
}
