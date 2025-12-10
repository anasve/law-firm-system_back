<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LawyerAvailability extends Model
{
    use HasFactory;

    protected $table = 'lawyer_availability';

    protected $fillable = [
        'lawyer_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'is_vacation',
        'vacation_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'is_vacation' => 'boolean',
    ];

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'availability_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('date', '>=', now()->toDateString());
    }

    public function scopeForLawyer($query, $lawyerId)
    {
        return $query->where('lawyer_id', $lawyerId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
}

