<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'type',
        'fence',
        'is_graduated',
        'depth',
        'length',
        'width',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
