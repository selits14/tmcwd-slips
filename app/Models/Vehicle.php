<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate_number',
        'description',
        'type',
        'assigned_division_id',
        'is_active',
        'num_cylinders',
        'normal_km_per_liter',
        'monthly_fuel_allocation',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'normal_km_per_liter' => 'decimal:2',
        'monthly_fuel_allocation' => 'decimal:2',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'assigned_division_id');
    }

    public function gasSlips()
    {
        return $this->hasMany(GasSlip::class);
    }
}
