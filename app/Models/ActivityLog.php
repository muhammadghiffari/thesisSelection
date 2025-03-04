<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'action',
        'description',
        'ip_address'
    ];

    public function loggable()
    {
        return $this->morphTo();
    }
}
