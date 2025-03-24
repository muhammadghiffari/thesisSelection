<?php

namespace App\Http\Controllers;

use App\Models\ThesisSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThesisSelectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selections = ThesisSelection::with(['student', 'thesisTitle'])->get();
        return response()->json($selections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id'      => 'required|exists:students,id',
            'thesis_title_id' => 'required|exists:thesis_titles,id',
            'ip_address'      => 'nullable|ip',
            'status'          => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $selection = ThesisSelection::create($request->all());
        return response()->json($selection, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ThesisSelection $thesisSelection)
    {
        return response()->json($thesisSelection->load(['student', 'thesisTitle']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThesisSelection $thesisSelection)
    {
        $validator = Validator::make($request->all(), [
            'student_id'      => 'sometimes|exists:students,id',
            'thesis_title_id' => 'sometimes|exists:thesis_titles,id',
            'ip_address'      => 'nullable|ip',
            'status'          => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $thesisSelection->update($request->all());
        return response()->json($thesisSelection);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThesisSelection $thesisSelection)
    {
        $thesisSelection->delete();
        return response()->json(['message' => 'Thesis selection deleted successfully.']);
    }
}
