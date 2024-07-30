<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_name',
        'description',
        'price',
        'type_id',
        'city_id',
        'address',
        'latitude',
        'longitude',
        'bathrooms',
        'bedrooms',
        'capacity',
        'amenities',
        'kitchen_amenities',
        'property_status',
        'availability_status',
        'owner_id',
        'owner_type',
        'request_status'
    ];

    protected $casts = [
        'amenities' => 'array',
        'kitchen_amenities' => 'array',
    ];

    public function type()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function admin()
    // {
    //     return $this->belongsTo(admins::class);
    // }

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function details()
    {
        return $this->hasOne(PropertyDetail::class);
    }

    public function owner()
    {
        return $this->morphTo();
    }


    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating');
    }

    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }


    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

}
