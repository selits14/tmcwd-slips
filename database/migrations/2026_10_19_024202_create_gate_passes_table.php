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
    Schema::create('gate_passes', function (Blueprint $table) {
        $table->id();
        $table->string('pass_number')->unique(); // GP-2026-00001
        $table->foreignId('requested_by_user_id')->constrained('users');
        $table->foreignId('division_id')->constrained();
        $table->enum('pass_type', ['outgoing','incoming'])->default('outgoing');
        $table->string('purpose');
        $table->string('destination');
        $table->enum('status', ['draft','pending','approved','released','returned','rejected'])->default('draft');
        $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
        $table->timestamp('approved_at')->nullable();
        $table->foreignId('released_by_user_id')->nullable()->constrained('users');
        $table->timestamp('released_at')->nullable();
        $table->timestamp('returned_at')->nullable();
        $table->foreignId('returned_verified_by')->nullable()->constrained('users');
        $table->text('remarks')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
};
