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
    Schema::table('gas_slips', function (Blueprint $table) {
        $table->string('fuel_type')->default('Diesel')->after('vehicle_id');
        $table->string('gas_station')->default('Petron Inocencio / Del Mundo\'s Gas Station')->after('fuel_type');
        $table->date('last_refuel_date')->nullable()->after('gas_station');
        $table->decimal('requested_liters', 8, 2)->nullable()->after('last_refuel_date');
    });
}

public function down(): void
{
    Schema::table('gas_slips', function (Blueprint $table) {
        $table->dropColumn(['fuel_type', 'gas_station', 'last_refuel_date', 'requested_liters']);
    });
}
};
