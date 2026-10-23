<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // PostgreSQL cannot include a CHECK clause in ALTER COLUMN TYPE.
    if (DB::getDriverName() === 'pgsql') {
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_position_check CHECK (position IS NULL OR position IN (
            'General Manager',
            'Division Manager',
            'Admin Services Aide',
            'Driver',
            'Security Guard',
            'IT Officer',
            'Other'
        ))");

        return;
    }

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
    if (DB::getDriverName() === 'pgsql') {
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_position_check');

        return;
    }

    Schema::table('users', function (Blueprint $table) {
        $table->string('position')->nullable()->change();
    });
}
};
