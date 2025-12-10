<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}

