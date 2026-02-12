<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price_per_night',
        'address',
        'city',
        'country',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'is_active'
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class)->orderBy('order');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function specialOffers()
    {
        return $this->hasMany(SpecialOffer::class);
    }
}
