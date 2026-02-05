<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Company::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|unique:companies',
            'name' => 'required',
            'position' => 'required',
            'location' => 'required',
            'address_details' => 'nullable|string',
            'tel' => 'required',
            'student_vacancy' => 'required|integer|min:0',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        return Company::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Company::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|unique:companies,company_id,' . $company->id,
            'name' => 'required',
            'position' => 'required',
            'location' => 'required',
            'address_details' => 'nullable|string',
            'tel' => 'required',
            'student_vacancy' => 'required|integer|min:0',
        ]);

        $company->update($validated);

        return $company;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Company::destroy($id);
    }
}
