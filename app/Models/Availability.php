<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'start_date',
        'end_date',
        'is_available',
        'price_per_night'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_available' => 'boolean',
        'price_per_night' => 'decimal:2'
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
