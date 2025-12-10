<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilityTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'lawyer_id',
        'name',
        'start_time',
        'end_time',
        'days_of_week',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }
}

