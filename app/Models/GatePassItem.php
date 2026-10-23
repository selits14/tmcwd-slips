<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatePassItem extends Model
{
    protected $fillable = [
        'gate_pass_id', 'description', 'quantity',
        'unit', 'serial_number', 'item_condition', 'is_returned'
    ];

    public function gatePass()
    {
        return $this->belongsTo(GatePass::class);
    }
}