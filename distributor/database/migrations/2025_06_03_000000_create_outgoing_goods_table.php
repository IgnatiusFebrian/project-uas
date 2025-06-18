<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outgoing_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->dateTime('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outgoing_goods');
    }
};
