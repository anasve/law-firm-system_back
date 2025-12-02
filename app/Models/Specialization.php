<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name' , 'description'];

    public function lawyers()
    {
        return $this->belongsToMany(
            Lawyer::class,
            'lawyer_specialization',
            'specialization_id',
            'lawyer_id'
        );
    }

}
