<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'user_id',
        'message',
        'sender_type',
        'is_read',
        'thesis_selection_id',
    ];

    /**
     * Relationship with Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship with User (Admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Thesis Selection
     */
    public function thesisSelection()
    {
        return $this->belongsTo(ThesisSelection::class);
    }
}