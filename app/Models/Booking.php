<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'start_date',
        'end_date',
        'amount',
        'currency',
        'status'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
