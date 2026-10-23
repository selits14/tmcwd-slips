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
    Schema::create('gas_slips', function (Blueprint $table) {
        $table->id();
        $table->string('slip_number')->unique(); // GS-2026-00001
        $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
        $table->foreignId('driver_user_id')->constrained('users');
        $table->foreignId('requested_by_user_id')->constrained('users');
        $table->foreignId('division_id')->constrained();
        $table->string('purpose');
        $table->string('destination');
        $table->decimal('requested_liters', 8, 2);
        $table->decimal('approved_liters', 8, 2)->nullable();
        $table->decimal('actual_liters', 8, 2)->nullable();
        $table->integer('odometer_before')->nullable();
        $table->integer('odometer_after')->nullable();
        $table->enum('status', ['draft','pending','approved','released','rejected','cancelled'])->default('draft');
        $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
        $table->timestamp('approved_at')->nullable();
        $table->foreignId('released_by_user_id')->nullable()->constrained('users');
        $table->timestamp('released_at')->nullable();
        $table->text('remarks')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_slips');
    }
};
