<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
protected $fillable = [
    'name', 'email', 'password',
    'employee_id', 'position',
    'division_id', 'is_active'
];

public function division()
{
    return $this->belongsTo(Division::class);
}

public function divisionsHeaded()
{
    return $this->hasMany(Division::class, 'head_user_id');
}

public function gasSlips()
{
    return $this->hasMany(GasSlip::class, 'requested_by_user_id');
}

public function passSlips()
{
    return $this->hasMany(PassSlip::class, 'employee_user_id');
}

public function gatePasses()
{
    return $this->hasMany(GatePass::class, 'requested_by_user_id');
}

public function canApproveSlipInDivision(?Division $division): bool
{
    return $this->hasRole('super_admin')
        || $this->position === 'General Manager'
        || ($division && (
            $division->head_user_id === $this->id
            || ($this->position === 'Division Manager' && $this->division_id === $division->id)
        ));
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
