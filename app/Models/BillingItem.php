<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
