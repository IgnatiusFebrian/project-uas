<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop existing check constraint if exists
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

            // Modify role column with proper check constraint
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'employee'))");
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        });
    }
};