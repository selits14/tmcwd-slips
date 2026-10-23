<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'gate_passes' => ['pass_number', 'GP-'],
            'gas_slips' => ['slip_number', 'GS-'],
        ] as $table => [$column, $prefix]) {
            $latestNumber = DB::table($table)
                ->orderByDesc('id')
                ->value($column);

            $number = preg_match('/-(\d+)$/', (string) $latestNumber, $matches)
                ? (int) $matches[1]
                : 0;

            DB::table('slip_number_sequences')->insertOrIgnore([
                'prefix' => $prefix,
                'number' => $number,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('slip_number_sequences')->whereIn('prefix', ['GP-', 'GS-'])->delete();
    }
};
