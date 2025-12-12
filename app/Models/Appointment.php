<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consultation_id',
        'availability_id',
        'lawyer_id',
        'client_id',
        'subject',
        'description',
        'datetime',
        'type',
        'meeting_link',
        'notes',
        'status',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function availability()
    {
        return $this->belongsTo(LawyerAvailability::class, 'availability_id');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('datetime', '>=', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopeForLawyer($query, $lawyerId)
    {
        return $query->where('lawyer_id', $lawyerId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Accessor للتحقق من أن الموعد هو طلب وقت مخصص
    public function getIsCustomTimeRequestAttribute()
    {
        return $this->availability_id === null;
    }

    // Scope للمواعيد بوقت مخصص
    public function scopeCustomTimeRequests($query)
    {
        return $query->whereNull('availability_id');
    }
}

