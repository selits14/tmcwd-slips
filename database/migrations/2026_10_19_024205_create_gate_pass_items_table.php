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
    Schema::create('gate_pass_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('gate_pass_id')->constrained()->cascadeOnDelete();
        $table->string('description');
        $table->decimal('quantity', 8, 2);
        $table->string('unit');
        $table->string('serial_number')->nullable();
        $table->string('item_condition')->nullable(); // good, damaged, etc
        $table->boolean('is_returned')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_pass_items');
    }
};
