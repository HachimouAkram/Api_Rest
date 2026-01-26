<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hebergement extends Model
{
    protected $fillable = [
        'name',
        'location',
        'price',
        'rating',
        'image',
        'type',
        'capacity',
        'description',
        'amenities'
    ];

    protected $casts = [
        'amenities' => 'array'
    ];
}
