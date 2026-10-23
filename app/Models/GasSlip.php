<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GasSlip extends Model
{
    protected $fillable = [
        'slip_number', 'vehicle_id', 'driver_user_id',
        'requested_by_user_id', 'encoded_by_user_id', 'division_id',
        'passenger_name', 'places_to_visit', 'purpose',
        'time_departure_office', 'time_arrival_destination',
        'time_departure_destination', 'time_arrival_office',
        'distance_travelled', 'balance_in_tank_begin',
        'issued_from_stock', 'purchased_during_trip',
        'fuel_used', 'balance_in_tank_end',
        'status', 'approved_by_user_id', 'approved_at',
        'rejected_by_user_id', 'rejected_at',
        'released_by_user_id', 'released_at', 'remarks',
        'fuel_type', 'gas_station', 'last_refuel_date', 'requested_liters','oil_used', 'odometer_end',
    ];

    protected $casts = [
        'approved_at'               => 'datetime',
        'rejected_at'               => 'datetime',
        'released_at'               => 'datetime',
        'time_departure_office'     => 'datetime',
        'time_arrival_destination'  => 'datetime',
        'time_departure_destination'=> 'datetime',
        'time_arrival_office'       => 'datetime',
    ];

    // Total fuel available
    public function getTotalFuelAttribute()
    {
        return ($this->balance_in_tank_begin ?? 0)
             + ($this->issued_from_stock ?? 0)
             + ($this->purchased_during_trip ?? 0);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function encodedBy()
    {
        return $this->belongsTo(User::class, 'encoded_by_user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by_user_id');
    }
}
