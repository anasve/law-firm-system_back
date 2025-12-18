<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'lawyer_id',
        'specialization_id',
        'subject',
        'description',
        'priority',
        'preferred_channel',
        'meeting_link',
        'status',
        'rejection_reason',
        'legal_summary',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function attachments()
    {
        return $this->hasMany(ConsultationAttachment::class);
    }

    public function messages()
    {
        return $this->hasMany(ConsultationMessage::class)->orderBy('created_at', 'asc');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function review()
    {
        return $this->hasOne(ConsultationReview::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForLawyer($query, $lawyerId)
    {
        return $query->where('lawyer_id', $lawyerId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}

