<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ThesisSelection;
use App\Models\LiveActivity;

class Dashboard extends Component
{
    public $selections;
    public $activities;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->selections = ThesisSelection::with('student', 'thesisTitle')
            ->latest()
            ->take(10)
            ->get();

        $this->activities = LiveActivity::latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
