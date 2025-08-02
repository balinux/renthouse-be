<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'listing_id',
        'start_date',
        'end_date',
        'price_per_day',
        'total_days',
        'fee',
        'total_price',
        'status',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'total_days' => 'integer',
        'fee' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function setListingIdAttribute($value)
    {
        $listing = Listing::findOrFail($value);
        $totalDays = Carbon::createFromDate($this->attributes['start_date'])->diffInDays($this->attributes['end_date']) + 1;
        $totalPrice = $listing->price_per_day * $totalDays;
        $fee = $totalPrice * 0.1;
        $this->attributes['total_days'] = $totalDays;
        $this->attributes['price_per_day'] = $listing->price_per_day;
        $this->attributes['total_price'] = $totalPrice;
        $this->attributes['fee'] = $fee;
        $this->attributes['listing_id'] = $value;
    }
}
