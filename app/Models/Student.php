<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'npm',
        'class',
        'thesis_topic',
        'email',
        'token',
        'has_selected',
        'has_reset'
    ];

    public function thesisSelection()
    {
        return $this->hasOne(ThesisSelection::class);
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
