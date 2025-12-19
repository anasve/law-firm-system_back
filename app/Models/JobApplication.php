<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'status',
        'name',
        'email',
        'phone',
        'age',
        'address',
        'photo',
        'specialization_id',
        'experience_years',
        'bio',
        'certificate',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $hidden = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the admin who reviewed this application
     */
    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    /**
     * Get the specialization (for lawyer applications)
     */
    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    /**
     * Get photo URL accessor
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    /**
     * Get certificate URL accessor (for lawyers)
     */
    public function getCertificateUrlAttribute()
    {
        if ($this->certificate) {
            return asset('storage/' . $this->certificate);
        }
        return null;
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if application is for lawyer
     */
    public function isLawyer()
    {
        return $this->type === 'lawyer';
    }

    /**
     * Check if application is for employee
     */
    public function isEmployee()
    {
        return $this->type === 'employee';
    }
}

