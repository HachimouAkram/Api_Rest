<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'title',
        'description',
        'discount_percentage',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $appends = ['original_price', 'discounted_price', 'image'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function getOriginalPriceAttribute()
    {
        return $this->listing ? (float) $this->listing->price_per_night : 0;
    }

    public function getDiscountedPriceAttribute()
    {
        if (!$this->listing) return 0;
        $original = (float) $this->listing->price_per_night;
        $discount = (float) $this->discount_percentage;
        return round($original * (1 - ($discount / 100)), 2);
    }

    public function getImageAttribute()
    {
        if ($this->listing && $this->listing->photos->count() > 0) {
            return $this->listing->photos->first()->path;
        }
        return null;
    }

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];
}
