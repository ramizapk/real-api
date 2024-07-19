<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'session_type',
        'capacity',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
