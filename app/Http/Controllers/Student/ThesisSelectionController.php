<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ThesisTitle;

class ThesisSelectionController extends Controller
{
     public function index()
    {
        return view('student.thesis-selection');
    }

    public function verifyToken($token)
    {
        $student = Student::where('token', strtoupper($token))->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Invalid token']);
        }

        return response()->json([
            'success' => true,
            'student' => $student
        ]);
    }
            // If no student is found, return an error message
}
        // If the student is found, return the student data
