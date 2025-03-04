<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThesisTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'topic', 'status'
    ];

    public function thesisSelections()
    {
        return $this->hasMany(ThesisSelection::class);
    }

    public function liveActivities()
    {
        return $this->hasMany(LiveActivity::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}