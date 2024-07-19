<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_text',
        'sub_text',
        'button_text',
        'image',
        'ad_type',
        'ad_url',
        'expiration_date',
    ];
}
