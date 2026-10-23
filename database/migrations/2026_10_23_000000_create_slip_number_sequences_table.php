<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slip_number_sequences', function (Blueprint $table) {
            $table->string('prefix')->primary();
            $table->unsignedInteger('number')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slip_number_sequences');
    }
};
