<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionColumnsToReturnsTable extends Migration
{
    public function up()
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('transaction_detail_id')->nullable()->after('transaction_id');

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->foreign('transaction_detail_id')->references('id')->on('transaction_details')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['transaction_detail_id']);
            $table->dropColumn(['transaction_id', 'transaction_detail_id']);
        });
    }
}
