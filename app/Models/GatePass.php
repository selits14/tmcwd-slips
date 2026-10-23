<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    protected $fillable = [
        'pass_number', 'lr_number', 'pr_number',
        'requested_by_user_id', 'encoded_by_user_id', 'division_id',
        'driver_name', 'vehicle_type', 'plate_number',
        'destination', 'passengers', 'purpose',
        'time_of_departure', 'time_of_arrival',
        'status', 'approved_by_user_id', 'approved_at',
        'rejected_by_user_id', 'rejected_at',
        'released_by_user_id', 'released_at',
        'returned_at', 'returned_verified_by', 'remarks'
    ];

    protected $casts = [
        'approved_at'       => 'datetime',
        'rejected_at'       => 'datetime',
        'released_at'       => 'datetime',
        'returned_at'       => 'datetime',
        'time_of_departure' => 'datetime',
        'time_of_arrival'   => 'datetime',
    ];

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

    public function returnVerifiedBy()
    {
        return $this->belongsTo(User::class, 'returned_verified_by');
    }
}
