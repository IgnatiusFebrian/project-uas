<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
            $table->string('unit')->nullable()->after('stock');
            $table->integer('minimum_stock')->default(0)->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['category', 'unit', 'minimum_stock']);
        });
    }
};
