<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'thesis_title_id',
        'action',
        'ip_address'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function thesisTitle()
    {
        return $this->belongsTo(ThesisTitle::class);
    }
}
