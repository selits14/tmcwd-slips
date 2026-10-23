<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SlipVisibility
{
    public static function query(Builder $query, User $user, string $type): Builder
    {
        if (self::canViewAll($user)) {
            return $query;
        }

        if ($user->hasRole('guard') && in_array($type, ['pass', 'gate'], true)) {
            return $query;
        }

        if (! $user->hasRole('driver') && ! self::managesDivision($user)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $visible) use ($user, $type) {
            if ($user->hasRole('driver')) {
                match ($type) {
                    'pass' => $visible->orWhere('employee_user_id', $user->id),
                    'gas' => $visible->orWhere('driver_user_id', $user->id),
                    'gate' => $visible->orWhere('driver_name', $user->name),
                };
            }

            if (self::managesDivision($user)) {
                $visible->orWhere('division_id', $user->division_id)
                    ->orWhereHas('division', fn (Builder $division) => $division->where('head_user_id', $user->id));
            }
        });
    }

    public static function allows(User $user, object $slip, string $type): bool
    {
        if (self::canViewAll($user)) {
            return true;
        }

        if ($user->hasRole('guard') && in_array($type, ['pass', 'gate'], true)) {
            return true;
        }

        $owned = match ($type) {
            'pass' => $user->hasRole('driver') && $slip->employee_user_id === $user->id,
            'gas' => $user->hasRole('driver') && $slip->driver_user_id === $user->id,
            'gate' => $user->hasRole('driver') && $slip->driver_name === $user->name,
        };

        return $owned || (self::managesDivision($user) && $user->canApproveSlipInDivision($slip->division));
    }

    private static function canViewAll(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'encoder', 'viewer'])
            || $user->position === 'General Manager';
    }

    private static function managesDivision(User $user): bool
    {
        return $user->position === 'Division Manager'
            || $user->divisionsHeaded()->exists();
    }
}
