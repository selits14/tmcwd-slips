<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $number = DB::table('pass_slips')
            ->where('slip_number', 'like', 'PS-%')
            ->pluck('slip_number')
            ->map(function (string $slipNumber): int {
                return preg_match('/^PS-\d{4}-(\d+)$/', $slipNumber, $matches)
                    ? (int) $matches[1]
                    : 0;
            })
            ->max() ?? 0;

        DB::table('slip_number_sequences')->updateOrInsert(
            ['prefix' => 'PS-'],
            ['number' => $number],
        );
    }

    public function down(): void
    {
        DB::table('slip_number_sequences')->where('prefix', 'PS-')->delete();
    }
};
