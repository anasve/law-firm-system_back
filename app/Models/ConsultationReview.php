<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'client_id',
        'rating',
        'comment',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

