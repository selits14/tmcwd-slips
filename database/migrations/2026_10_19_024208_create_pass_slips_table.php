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
    Schema::create('pass_slips', function (Blueprint $table) {
        $table->id();
        $table->string('slip_number')->unique(); // PS-2026-00001
        $table->foreignId('employee_user_id')->constrained('users');
        $table->foreignId('division_id')->constrained();
        $table->enum('pass_type', ['official_business','personal'])->default('official_business');
        $table->string('purpose');
        $table->string('destination');
        $table->dateTime('time_out')->nullable();
        $table->dateTime('expected_return')->nullable();
        $table->dateTime('actual_return')->nullable();
        $table->enum('status', ['draft','pending','approved','out','returned','rejected'])->default('draft');
        $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
        $table->timestamp('approved_at')->nullable();
        $table->foreignId('verified_out_by')->nullable()->constrained('users');
        $table->foreignId('verified_in_by')->nullable()->constrained('users');
        $table->text('remarks')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pass_slips');
    }
};
