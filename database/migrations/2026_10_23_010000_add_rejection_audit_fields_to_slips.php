<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pass_slips', 'gate_passes', 'gas_slips'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('rejected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('rejected_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['pass_slips', 'gate_passes', 'gas_slips'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('rejected_by_user_id');
                $table->dropColumn('rejected_at');
            });
        }
    }
};
