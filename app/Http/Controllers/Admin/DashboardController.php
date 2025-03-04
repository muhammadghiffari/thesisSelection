<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThesisSelection;
use App\Models\LiveActivity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $selections = ThesisSelection::with('student', 'thesisTitle')
            ->latest()
            ->take(10)
            ->get();

        $activities = LiveActivity::latest()
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard', [
            'selections' => $selections,
            'activities' => $activities
        ]);
    }
}
