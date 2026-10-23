<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassSlip extends Model
{
    protected $fillable = [
        'slip_number', 'employee_user_id', 'encoded_by_user_id', 'division_id',
        'pass_type', 'purpose', 'destination',
        'time_out', 'expected_return', 'actual_return',
        'status', 'approved_by_user_id', 'approved_at',
        'verified_out_by', 'verified_in_by', 'remarks'
    ];

    protected $casts = [
        'time_out'       => 'datetime',
        'expected_return'=> 'datetime',
        'actual_return'  => 'datetime',
        'approved_at'    => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_user_id');
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

    public function verifiedOutBy()
    {
        return $this->belongsTo(User::class, 'verified_out_by');
    }

    public function verifiedInBy()
    {
        return $this->belongsTo(User::class, 'verified_in_by');
    }
}
