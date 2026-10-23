<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    // Add to gas_slips
    Schema::table('gas_slips', function (Blueprint $table) {
        $table->decimal('oil_used', 8, 2)->nullable()->after('fuel_used');
        $table->integer('odometer_end')->nullable()->after('distance_travelled');
    });

    // Add to vehicles
    Schema::table('vehicles', function (Blueprint $table) {
        $table->integer('num_cylinders')->nullable()->after('type');
        $table->decimal('normal_km_per_liter', 5, 2)->nullable()->after('num_cylinders');
    });
}

public function down(): void
{
    Schema::table('gas_slips', function (Blueprint $table) {
        $table->dropColumn(['oil_used', 'odometer_end']);
    });
    Schema::table('vehicles', function (Blueprint $table) {
        $table->dropColumn(['num_cylinders', 'normal_km_per_liter']);
    });
}
};
