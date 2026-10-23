<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pass_slips', 'gate_passes', 'gas_slips'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('encoded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        DB::table('pass_slips')->whereNull('encoded_by_user_id')->update([
            'encoded_by_user_id' => DB::raw('employee_user_id'),
        ]);
        DB::table('gate_passes')->whereNull('encoded_by_user_id')->update([
            'encoded_by_user_id' => DB::raw('requested_by_user_id'),
        ]);
        DB::table('gas_slips')->whereNull('encoded_by_user_id')->update([
            'encoded_by_user_id' => DB::raw('requested_by_user_id'),
        ]);
    }

    public function down(): void
    {
        foreach (['pass_slips', 'gate_passes', 'gas_slips'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('encoded_by_user_id');
            });
        }
    }
};
