<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThesisSelection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'thesis_title_id',
        'ip_address',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function thesisTitle()
    {
        return $this->belongsTo(ThesisTitle::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
