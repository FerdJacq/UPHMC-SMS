<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_id')->index();
            $table->decimal('shipping_fee',13,2)->default(0)->nullable();
            $table->decimal('voucher',13,2)->nullable();
            $table->decimal('coins',13,2)->nullable();
            $table->decimal('subtotal_amount',13,2)->nullable();
            $table->decimal('total_amount',13,2)->nullable();
            $table->decimal('transaction_fee',13,2)->nullable();
            $table->decimal('service_fee',13,2)->nullable();
            $table->decimal('commission_fee',13,2)->nullable();
            $table->decimal('tax',13,2)->nullable();
            $table->decimal('online_platform_vat',13,2)->nullable();
            $table->decimal('shipping_vat',13,2)->nullable();
            $table->decimal('item_vat',13,2)->nullable();
            $table->decimal('base_price',13,2)->nullable();
            $table->decimal('withholding_tax',13,2)->nullable();
            $table->tinyInteger('is_seen')->default(0)->comment('0=not seen,1=seen');
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
        Schema::dropIfExists('transaction_logs');
    }
}
