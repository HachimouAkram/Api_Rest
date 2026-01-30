<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'path',
        'caption',
        'order'
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
