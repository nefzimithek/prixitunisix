<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['offer_id', 'price', 'recorded_at'];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
