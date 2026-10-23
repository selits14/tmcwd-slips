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
        // Remove old columns
        $table->dropColumn([
            'purpose', 'destination',
            'requested_liters', 'approved_liters', 'actual_liters',
            'odometer_before', 'odometer_after',
        ]);

        // Section A fields
        $table->string('passenger_name')->nullable()->after('driver_user_id');
        $table->string('places_to_visit')->after('passenger_name');
        $table->text('purpose')->after('places_to_visit');

        // Section B fields — driver fills after trip
        $table->dateTime('time_departure_office')->nullable()->after('purpose');
        $table->dateTime('time_arrival_destination')->nullable()->after('time_departure_office');
        $table->dateTime('time_departure_destination')->nullable()->after('time_arrival_destination');
        $table->dateTime('time_arrival_office')->nullable()->after('time_departure_destination');
        $table->decimal('distance_travelled', 8, 2)->nullable()->after('time_arrival_office');

        // Fuel tracking
        $table->decimal('balance_in_tank_begin', 8, 2)->nullable()->after('distance_travelled');
        $table->decimal('issued_from_stock', 8, 2)->nullable()->after('balance_in_tank_begin');
        $table->decimal('purchased_during_trip', 8, 2)->nullable()->after('issued_from_stock');
        $table->decimal('fuel_used', 8, 2)->nullable()->after('purchased_during_trip');
        $table->decimal('balance_in_tank_end', 8, 2)->nullable()->after('fuel_used');
    });
}

public function down(): void
{
    Schema::table('gas_slips', function (Blueprint $table) {
        $table->dropColumn([
            'passenger_name', 'places_to_visit', 'purpose',
            'time_departure_office', 'time_arrival_destination',
            'time_departure_destination', 'time_arrival_office',
            'distance_travelled', 'balance_in_tank_begin',
            'issued_from_stock', 'purchased_during_trip',
            'fuel_used', 'balance_in_tank_end',
        ]);
        $table->string('purpose');
        $table->string('destination');
        $table->decimal('requested_liters', 8, 2);
        $table->decimal('approved_liters', 8, 2)->nullable();
        $table->decimal('actual_liters', 8, 2)->nullable();
        $table->integer('odometer_before')->nullable();
        $table->integer('odometer_after')->nullable();
    });
}
};
