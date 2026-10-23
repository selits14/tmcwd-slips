<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clear non-matching values first
        DB::statement("UPDATE users SET position = NULL WHERE position NOT IN (
            'General Manager',
            'Division Manager',
            'Admin Services Aide',
            'Driver',
            'Security Guard',
            'IT Officer',
            'Other'
        )");

        // Change to enum
        DB::statement("ALTER TABLE users MODIFY position ENUM(
            'General Manager',
            'Division Manager',
            'Admin Services Aide',
            'Driver',
            'Security Guard',
            'IT Officer',
            'Other'
        ) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY position VARCHAR(255) NULL");
    }
};