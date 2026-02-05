<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Info;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Info::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'info_id' => 'required|unique:infos',
            'title' => 'required|string|max:255',
            'category' => 'required|in:Announce,Important,Guide',
            'detail' => 'required',
            'due_date' => 'required|date',
        ]);

        return Info::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Info::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $info = Info::findOrFail($id);

        $validated = $request->validate([
            'info_id' => 'required|unique:infos,info_id,' . $info->id,
            'title' => 'required|string|max:255',
            'category' => 'required|in:Announce,Important,Guide',
            'detail' => 'required',
            'due_date' => 'required|date',
        ]);

        $info->update($validated);

        return $info;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Info::destroy($id);
    }
}
