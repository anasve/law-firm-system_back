<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Law extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'title', 'category', 'summary', 'full_content', 'status',
    ];
}
