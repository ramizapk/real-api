<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyTypeDetail extends Model
{
    use HasFactory;

    protected $fillable = ['type_id', 'image'];

    public function type()
    {
        return $this->belongsTo(PropertyType::class);
    }
    public function properties()
    {
        return $this->hasManyThrough(
            Property::class,
            PropertyType::class,
            'id', // المفتاح الأجنبي في النموذج الوسيط (PropertyType) الذي يشير إلى النموذج الحالي
            'type_id', // المفتاح الأجنبي في النموذج الهدف (Property) الذي يشير إلى النموذج الوسيط (PropertyType)
            'type_id', // المفتاح المحلي للنموذج الحالي (PropertyTypeDetail)
            'id' // المفتاح المحلي للنموذج الوسيط (PropertyType)
        );

    }
}
