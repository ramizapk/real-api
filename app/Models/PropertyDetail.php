<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'check_in_time',
        'check_out_time',
        'security_deposit',
        'additional_notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
