<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'offer_title',
        'offer_description',
        'discount',
        'start_date',
        'end_date'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
