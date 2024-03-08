<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_summaries', function (Blueprint $table) {
            $table->id();
           
            $table->integer('service_provider_id')->index();
            $table->integer('seller_id')->index()->nullable()->default(0);
            $table->decimal('transaction_fee',13,2)->nullable()->default(0);
            $table->decimal('service_fee',13,2)->nullable()->default(0);
            $table->decimal('commission_fee',13,2)->nullable()->default(0);
            $table->decimal('online_platform_vat',13,2)->nullable()->default(0);
            $table->decimal('shipping_vat',13,2)->nullable()->default(0);
            $table->decimal('item_vat',13,2)->nullable()->default(0);
            $table->decimal('total_amount',13,2)->nullable()->default(0);
            $table->decimal('base_price',13,2)->nullable()->default(0);
            $table->decimal('withholding_tax',13,2)->nullable()->default(0);
            $table->decimal('tax',13,2)->nullable()->default(0);
            $table->integer('pending')->nullable()->default(0);
            $table->integer('ongoing')->nullable()->default(0);
            $table->integer('delivered')->nullable()->default(0);
            $table->integer('cancelled')->nullable()->default(0);
            $table->integer('refunded')->nullable()->default(0);
            $table->integer('completed')->nullable()->default(0);
            $table->date('assigned_date')->nullable();
            $table->string('region_code')->nullable()->default("00");
            $table->enum('type', ['SERVICE', 'PRODUCT'])->nullable()->default('PRODUCT');
            $table->enum('vat_type', ['V', 'NV'])->nullable()->default('V');
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
        Schema::dropIfExists('transaction_summaries');
    }
}
