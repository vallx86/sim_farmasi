<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getDaysLeftAttribute(): ?int
    {
        return $this->expiry_date ? now()->startOfDay()->diffInDays($this->expiry_date, false) : null;
    }

    public function refreshStatus(): void
    {
        $days = $this->days_left;

        if ($this->stock <= $this->min_stock) {
            $this->status = 'Critical';
        } elseif ($days !== null && $days < 30) {
            $this->status = 'Exp. Soon';
        } elseif ($days !== null && $days <= 90) {
            $this->status = 'Adequate';
        } else {
            $this->status = 'Optimal';
        }
    }
}
