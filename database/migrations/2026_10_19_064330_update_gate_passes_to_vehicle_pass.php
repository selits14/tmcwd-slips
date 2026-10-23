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
    Schema::table('gate_passes', function (Blueprint $table) {
        // Remove old columns
        $table->dropColumn(['pass_type', 'destination']);

        // Add vehicle pass specific columns
        $table->string('lr_number')->nullable()->after('pass_number');
        $table->string('pr_number')->nullable()->after('lr_number');
        $table->string('driver_name')->after('pr_number');
        $table->string('vehicle_type')->after('driver_name');
        $table->string('plate_number')->after('vehicle_type');
        $table->string('destination')->after('plate_number');
        $table->text('passengers')->nullable()->after('destination');
        $table->dateTime('time_of_departure')->nullable()->after('passengers');
        $table->dateTime('time_of_arrival')->nullable()->after('time_of_departure');
    });
}

public function down(): void
{
    Schema::table('gate_passes', function (Blueprint $table) {
        $table->dropColumn([
            'lr_number', 'pr_number', 'driver_name',
            'vehicle_type', 'plate_number', 'passengers',
            'time_of_departure', 'time_of_arrival'
        ]);
        $table->string('pass_type')->default('outgoing');
        $table->string('destination');
    });
}
};
