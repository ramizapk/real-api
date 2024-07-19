<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'image'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function properties()
    {
        return $this->hasManyThrough(Property::class, City::class, 'id', 'city_id', 'city_id', 'id');
    }
}
