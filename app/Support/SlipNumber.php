<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SlipNumber
{
    public static function next(string $prefix, int $digits, ?string $displayPrefix = null): string
    {
        DB::table('slip_number_sequences')->insertOrIgnore([
            'prefix' => $prefix,
            'number' => 0,
        ]);

        $sequence = DB::table('slip_number_sequences')
            ->where('prefix', $prefix)
            ->lockForUpdate()
            ->first();

        $number = $sequence->number + 1;

        DB::table('slip_number_sequences')
            ->where('prefix', $prefix)
            ->update(['number' => $number]);

        return ($displayPrefix ?? $prefix) . str_pad((string) $number, $digits, '0', STR_PAD_LEFT);
    }
}
