<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('service_provider_id')->index();
            $table->string('customer_id')->nullable()->index();
            $table->string('seller_id')->nullable()->index();
            $table->string('trans_id')->nullable()->index();
            $table->string('region_code')->nullable()->default("00");
            $table->string('or_number')->nullable()->index();
            $table->string('reference_number')->nullable()->index();
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
            $table->datetime('pending_date')->nullable();
            $table->datetime('ongoing_date')->nullable();
            $table->datetime('cancelled_date')->nullable();
            $table->datetime('refunded_date')->nullable();
            $table->datetime('delivered_date')->nullable();
            $table->datetime('completed_date')->nullable();
            $table->datetime('remitted_date')->nullable();
            $table->string('blockchain_trx_id')->nullable();
            $table->string('blockchain_block_number')->nullable();
            $table->enum('vat_type', ['V', 'NV'])->nullable()->default('V');
            $table->enum('type', ['SERVICE', 'PRODUCT'])->nullable()->default('PRODUCT');
            $table->tinyInteger('email_notified')->default(0)->comment('0=pending,1=processing,2=completed');
            $table->tinyInteger('blockchain_status')->default(0)->comment('0=pending,1=processing,2=completed');
            $table->enum('status', ['PENDING', 'ONGOING', 'DELIVERED', 'CANCELLED', 'REFUNDED', 'COMPLETED'])->default('PENDING')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
