<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\SlipWorkflowNotification;
use Illuminate\Database\Eloquent\Model;

class SlipNotifier
{
    public static function submitted(Model $slip, string $type, string $routeName): void
    {
        $division = $slip->division;
        $number = $slip->slip_number ?? $slip->pass_number;

        $approvers = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($division) {
                $query->where('position', 'General Manager')
                    ->orWhereHas('roles', fn ($roles) => $roles->where('name', 'super_admin'));

                if ($division) {
                    $query->orWhere(function ($managers) use ($division) {
                        $managers->where('position', 'Division Manager')
                            ->where('division_id', $division->id);
                    })->orWhereHas('divisionsHeaded', fn ($divisions) => $divisions->whereKey($division->id));
                }
            })
            ->get()
            ->filter(fn (User $user) => ($user->hasRole('super_admin') || $user->hasRole('approver'))
                && $user->canApproveSlipInDivision($division)
                && SlipVisibility::allows($user, $slip, $type));

        foreach ($approvers as $approver) {
            $approver->notify(new SlipWorkflowNotification(
                'Approval required',
                "{$type} {$number} is awaiting your review.",
                route($routeName, $slip->getRouteKey(), false),
            ));
        }
    }

    public static function decided(Model $slip, string $type, string $routeName, string $status, ?int $actorId): void
    {
        $number = $slip->slip_number ?? $slip->pass_number;
        $recipients = User::query()
            ->where('is_active', true)
            ->whereIn('id', array_filter([
                $slip->requested_by_user_id ?? $slip->employee_user_id,
                $slip->encoded_by_user_id,
            ]))
            ->when($actorId, fn ($query) => $query->where('id', '!=', $actorId))
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new SlipWorkflowNotification(
                "Slip {$status}",
                "{$type} {$number} was {$status}.",
                route($routeName, $slip->getRouteKey(), false),
            ));
        }
    }
}
