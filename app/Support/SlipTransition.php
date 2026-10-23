<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class SlipTransition
{
    public static function update(Model $slip, string $from, array $attributes): void
    {
        $updated = $slip->newQuery()
            ->whereKey($slip->getKey())
            ->where('status', $from)
            ->update($attributes);

        abort_unless($updated === 1, 409, 'This slip has already changed. Refresh and try again.');
    }
}
