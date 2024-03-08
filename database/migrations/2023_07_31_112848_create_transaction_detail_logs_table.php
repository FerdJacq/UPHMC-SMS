<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_detail_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_log_id')->index();
            $table->integer('transaction_id')->index();
            $table->integer('transaction_detail_id')->index();
            $table->string('item')->index();
            $table->string('variant')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price',13,2)->default(0);
            $table->decimal('total_price',13,2)->default(0);;
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_detail_logs');
    }
}
