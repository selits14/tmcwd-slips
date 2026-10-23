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
    Schema::table('users', function (Blueprint $table) {
        $table->enum('position', [
            'General Manager',
            'Division Manager',
            'Admin Services Aide',
            'Driver',
            'Security Guard',
            'IT Officer',
            'Other',
        ])->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('position')->nullable()->change();
    });
}
};
