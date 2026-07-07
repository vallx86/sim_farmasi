<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = [];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
